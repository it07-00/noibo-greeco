<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CommissionRequestStatus;
use App\Enums\RoleEnum;
use App\Models\CommissionRequest;
use App\Models\User;
use App\Notifications\CommissionRequestUpdated;
use DomainException;
use Illuminate\Support\Facades\Notification;

final class CommissionRequestService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, User $actor): CommissionRequest
    {
        $request = CommissionRequest::query()->create(array_merge($data, [
            'user_id' => $actor->id,
            'status' => CommissionRequestStatus::Estimated,
        ]));

        $this->notifyAccountants($request, $actor);

        return $request;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(CommissionRequest $request, array $data): CommissionRequest
    {
        if (in_array($request->status, [CommissionRequestStatus::Approved, CommissionRequestStatus::Paid], true)) {
            throw new DomainException('Không thể chỉnh sửa yêu cầu đã duyệt hoặc đã chi.');
        }

        $payload = $data;
        if ($request->status === CommissionRequestStatus::Rejected) {
            $payload['status'] = CommissionRequestStatus::Estimated;
            $payload['processed_at'] = null;
            $payload['processed_by'] = null;
        }

        $request->update($payload);

        return $request->refresh();
    }

    public function approve(CommissionRequest $request, User $actor): CommissionRequest
    {
        if ($request->status !== CommissionRequestStatus::Estimated) {
            throw new DomainException('Chỉ có thể duyệt yêu cầu đang ở trạng thái dự chi.');
        }

        $request->update([
            'status' => CommissionRequestStatus::Approved,
            'processed_at' => now(),
            'processed_by' => $actor->id,
        ]);

        $this->notifyRequester($request, $actor, 'approved');

        return $request->refresh();
    }

    public function reject(CommissionRequest $request, string $reason, User $actor): CommissionRequest
    {
        if ($request->status !== CommissionRequestStatus::Estimated) {
            throw new DomainException('Chỉ có thể từ chối yêu cầu đang ở trạng thái dự chi.');
        }

        if (blank($reason)) {
            throw new DomainException('Vui lòng nhập lý do từ chối.');
        }

        $request->update([
            'status' => CommissionRequestStatus::Rejected,
            'processed_at' => now(),
            'processed_by' => $actor->id,
            'notes' => trim(($request->notes ? rtrim($request->notes)."\n\n" : '').'Lý do từ chối (kế toán): '.trim($reason)),
        ]);

        $this->notifyRequester($request, $actor, 'rejected');

        return $request->refresh();
    }

    public function markPaid(CommissionRequest $request, string $billPath, User $actor): CommissionRequest
    {
        if ($request->status !== CommissionRequestStatus::Approved) {
            throw new DomainException('Chỉ có thể xác nhận chi cho yêu cầu đã duyệt.');
        }

        $request->update([
            'status' => CommissionRequestStatus::Paid,
            'payment_bill_path' => $billPath,
            'processed_at' => now(),
            'processed_by' => $actor->id,
        ]);

        $this->notifyRequester($request, $actor, 'paid');

        return $request->refresh();
    }

    private function notifyAccountants(CommissionRequest $request, User $actor): void
    {
        $accountants = User::role(RoleEnum::Accountant->value)->get();
        if ($accountants->isNotEmpty()) {
            Notification::send($accountants, new CommissionRequestUpdated($request, $actor->name, 'created'));
        }
    }

    private function notifyRequester(CommissionRequest $request, User $actor, string $action): void
    {
        $request->loadMissing('requester');
        $requester = $request->requester;
        if ($requester) {
            $requester->notify(new CommissionRequestUpdated($request, $actor->name, $action));
        }
    }
}
