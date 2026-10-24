<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Contract;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class ContractListing
{
    public function query(string $search = '', string $status = '', string $type = '', array $filters = []): Builder
    {
        $actor = auth()->user();
        $query = Contract::query();
        if (! $actor instanceof User) {
            return $query->whereRaw('1 = 0');
        }
        $query->visibleTo($actor);
        if (trim($search) !== '') {
            $search = '%'.trim($search).'%';
            $query->where(fn ($query) => $query->where('contract_number', 'like', $search)->orWhere('title', 'like', $search)
                ->orWhere('report_number', 'like', $search)
                ->orWhereHas('customer', fn ($query) => $query->where('name', 'like', $search)->orWhere('tax_code', 'like', $search))
                ->orWhereHas('handler', fn ($query) => $query->where('name', 'like', $search)));
        }
        $query->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($type !== '', fn ($query) => $query->where('type', $type));
        foreach (['owner_id', 'department_id', 'handler_id', 'province', 'info_source', 'payment_method', 'renewal_status', 'voucher_status'] as $field) {
            if (filled($filters[$field] ?? null) && is_scalar($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }
        foreach (['signed_from' => ['signed_at', '>='], 'signed_to' => ['signed_at', '<='],
            'submitted_from' => ['submitted_at', '>='], 'submitted_to' => ['submitted_at', '<=']] as $key => [$field, $operator]) {
            $value = $filters[$key] ?? '';
            if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}$/D', $value) && ($date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value)) && $date->format('Y-m-d') === $value) {
                $query->whereDate($field, $operator, $value);
            }
        }
        foreach (['is_offset', 'has_room_fund', 'is_overdue'] as $flag) {
            if (filter_var($filters[$flag] ?? false, FILTER_VALIDATE_BOOLEAN)) {
                $query->where($flag, true);
            }
        }
        if (! empty($filters['hide_completed_workflow'])) {
            $query->whereDoesntHave('workflowSteps', fn ($query) => $query->where('step_name', 'finished')->where('action', 'complete'));
        }
        if (! empty($filters['missing_submission_place'])) {
            $query->where(fn ($query) => $query->whereNull('submission_place')->orWhere('submission_place', ''));
        }
        if (filled($filters['service_type'] ?? null) && is_string($filters['service_type'])) {
            $query->whereHas('services', fn ($query) => $query->where('service_type', $filters['service_type']));
        }

        return $query;
    }

    public function ordered(Builder $query, string $sort, string $direction): Builder
    {
        $sort = in_array($sort, ['id', 'created_at', 'signed_at', 'submitted_at', 'report_number', 'contract_number'], true) ? $sort : 'created_at';

        return $query->orderBy($sort, $direction === 'asc' ? 'asc' : 'desc')->orderByDesc('id');
    }
}
