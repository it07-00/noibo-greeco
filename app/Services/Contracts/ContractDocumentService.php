<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Enums\DocumentStatus;
use App\Models\Contract;
use App\Models\ContractDocument;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final class ContractDocumentService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(
        Contract $contract,
        array $data,
        User $actor,
        ?ContractDocument $superseded = null,
    ): ContractDocument {
        if ($superseded !== null && $superseded->contract_id !== $contract->id) {
            throw new DomainException('Chứng từ cần sửa không thuộc hợp đồng này.');
        }

        if ($superseded !== null && ! in_array($superseded->status, [
            DocumentStatus::RevisionRequired,
            DocumentStatus::Rejected,
        ], true)) {
            throw new DomainException('Chỉ chứng từ bị yêu cầu sửa hoặc từ chối mới được tạo phiên bản mới.');
        }

        if (isset($data['payment_schedule_id'])) {
            $belongsToContract = $contract->paymentSchedules()
                ->whereKey($data['payment_schedule_id'])
                ->exists();

            if (! $belongsToContract) {
                throw new DomainException('Đợt thanh toán không thuộc hợp đồng này.');
            }
        }

        return DB::transaction(function () use ($contract, $data, $actor, $superseded): ContractDocument {
            $version = $superseded !== null
                ? $superseded->version + 1
                : 1;

            return ContractDocument::query()->create(array_merge($data, [
                'contract_id' => $contract->id,
                'supersedes_id' => $superseded?->id,
                'type' => $superseded?->type ?? $data['type'],
                'status' => DocumentStatus::Draft,
                'version' => $version,
                'submitted_by' => $actor->id,
            ]));
        });
    }

    public function submit(ContractDocument $document, User $actor): ContractDocument
    {
        if ($document->status !== DocumentStatus::Draft) {
            throw new DomainException('Chỉ chứng từ nháp mới có thể gửi kiểm tra.');
        }

        $document->update([
            'status' => DocumentStatus::Submitted,
            'submitted_by' => $actor->id,
            'submitted_at' => now(),
            'reviewed_by' => null,
            'reviewed_at' => null,
            'review_feedback' => null,
        ]);

        return $document->refresh();
    }

    public function startReview(ContractDocument $document, User $actor): ContractDocument
    {
        if ($document->status !== DocumentStatus::Submitted) {
            throw new DomainException('Chứng từ không ở trạng thái chờ kiểm tra.');
        }

        $document->update([
            'status' => DocumentStatus::UnderReview,
            'reviewed_by' => $actor->id,
        ]);

        return $document->refresh();
    }

    public function review(
        ContractDocument $document,
        DocumentStatus $decision,
        User $actor,
        ?string $feedback = null,
    ): ContractDocument {
        if (! in_array($document->status, [DocumentStatus::Submitted, DocumentStatus::UnderReview], true)) {
            throw new DomainException('Chứng từ không ở trạng thái có thể đánh giá.');
        }

        if (! in_array($decision, [
            DocumentStatus::Approved,
            DocumentStatus::RevisionRequired,
            DocumentStatus::Rejected,
        ], true)) {
            throw new DomainException('Kết quả đánh giá chứng từ không hợp lệ.');
        }

        if ($decision !== DocumentStatus::Approved && blank($feedback)) {
            throw new DomainException('Vui lòng nhập nội dung phản hồi cho người lập chứng từ.');
        }

        return DB::transaction(function () use ($document, $decision, $actor, $feedback): ContractDocument {
            $document->update([
                'status' => $decision,
                'reviewed_by' => $actor->id,
                'reviewed_at' => now(),
                'review_feedback' => filled($feedback) ? trim((string) $feedback) : null,
            ]);

            if ($decision === DocumentStatus::Approved) {
                $this->archiveSupersededDocuments($document);
            }

            return $document->refresh();
        });
    }

    public function deleteDraft(ContractDocument $document): void
    {
        if ($document->status !== DocumentStatus::Draft) {
            throw new DomainException('Chỉ có thể xóa chứng từ đang ở bản nháp.');
        }

        DB::transaction(function () use ($document): void {
            $path = $document->file_path;
            $document->delete();
            Storage::disk('local')->delete($path);
        });
    }

    private function archiveSupersededDocuments(ContractDocument $document): void
    {
        $supersededId = $document->supersedes_id;

        while ($supersededId !== null) {
            $superseded = ContractDocument::query()->find($supersededId);

            if ($superseded === null) {
                break;
            }

            $nextId = $superseded->supersedes_id;
            $superseded->update(['status' => DocumentStatus::Archived]);
            $supersededId = $nextId;
        }
    }
}
