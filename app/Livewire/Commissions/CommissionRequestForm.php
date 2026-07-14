<?php

declare(strict_types=1);

namespace App\Livewire\Commissions;

use App\Enums\CommissionRequestStatus;
use App\Enums\ContractType;
use App\Enums\PermissionEnum;
use App\Models\CommissionRequest;
use App\Models\Contract;
use App\Models\User;
use App\Services\CommissionRequestService;
use DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Yêu cầu chi hoa hồng')]
final class CommissionRequestForm extends Component
{
    public int $requestId = 0;

    public string $contractType = '';

    public string $contractId = '';

    public string $receiverName = '';

    public string $receiverPhone = '';

    public string $bankAccount = '';

    public string $bankCode = '';

    public string $bankNumber = '';

    public string $amount = '';

    public string $referrerInfo = '';

    public string $notes = '';

    public string $selectedSavedAccountId = '';

    /** @var array<string, string> */
    public array $banks = [];

    public function mount(?int $id = null): void
    {
        $this->banks = $this->loadBanks();

        if ($id === null) {
            Gate::authorize(PermissionEnum::CommissionCreate->value);

            return;
        }

        $request = CommissionRequest::query()->findOrFail($id);
        $this->authorizeEditable($request);

        $this->requestId = $request->id;
        $this->contractType = $request->contract?->type?->value ?? '';
        $this->contractId = (string) $request->contract_id;
        $this->receiverName = $this->cleanReceiverName($request->receiver_name);
        $this->receiverPhone = $request->receiver_phone ?? '';
        $this->bankAccount = $request->bank_account ?? '';
        $this->bankCode = $request->bank_code ?? '';
        $this->bankNumber = $request->bank_number ?? '';
        $this->amount = (string) $request->amount;
        $this->referrerInfo = $request->referrer_info ?? '';
        $this->notes = $request->status === CommissionRequestStatus::Rejected ? '' : ($request->notes ?? '');
    }

    public function updatedReceiverName(mixed $value): void
    {
        $this->receiverName = $this->cleanReceiverName($value);
    }

    public function updatedContractType(): void
    {
        $this->contractId = '';
        $this->amount = '';
    }

    public function updatedContractId(mixed $value): void
    {
        $contract = Contract::query()->find((int) $value);

        if ($contract && $contract->customer_commission > 0 && $this->amount === '') {
            $this->amount = (string) $contract->customer_commission;
        }
    }

    public function updatedSelectedSavedAccountId(mixed $value): void
    {
        if (! $value) {
            return;
        }

        $account = CommissionRequest::query()
            ->where('user_id', Auth::id())
            ->find((int) $value);

        if (! $account) {
            return;
        }

        $this->receiverName = $this->cleanReceiverName($account->receiver_name);
        $this->receiverPhone = $account->receiver_phone ?? '';
        $this->bankAccount = $account->bank_account ?? '';
        $this->bankCode = $account->bank_code ?? '';
        $this->bankNumber = $account->bank_number ?? '';
    }

    public function save(bool $exit = false): mixed
    {
        $service = app(CommissionRequestService::class);
        $request = $this->requestId > 0
            ? CommissionRequest::query()->findOrFail($this->requestId)
            : null;

        if ($request) {
            $this->authorizeEditable($request);
        } else {
            Gate::authorize(PermissionEnum::CommissionCreate->value);
        }

        $validated = $this->validate([
            'contractType' => ['required', Rule::enum(ContractType::class)],
            'contractId' => ['required', 'integer', 'exists:contracts,id'],
            'receiverName' => ['required', 'string', 'max:255'],
            'receiverPhone' => ['nullable', 'string', 'max:30'],
            'bankAccount' => ['nullable', 'string', 'max:100'],
            'bankCode' => ['nullable', 'string', 'max:20'],
            'bankNumber' => ['nullable', 'string', 'max:50'],
            'amount' => ['required', 'numeric', 'min:1'],
            'referrerInfo' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ], [
            'contractType.required' => 'Vui lòng chọn loại hợp đồng.',
            'contractId.required' => 'Vui lòng chọn số hợp đồng.',
            'receiverName.required' => 'Vui lòng nhập người nhận hoa hồng.',
            'amount.required' => 'Vui lòng nhập số tiền hoa hồng.',
            'amount.min' => 'Số tiền hoa hồng phải lớn hơn 0.',
        ]);

        $contract = Contract::query()->findOrFail((int) $validated['contractId']);
        if ($contract->type->value !== $validated['contractType']) {
            $this->addError('contractId', 'Hợp đồng không thuộc loại đã chọn.');

            return null;
        }

        $payload = [
            'contract_id' => $contract->id,
            'receiver_name' => $this->cleanReceiverName($validated['receiverName']),
            'receiver_phone' => $validated['receiverPhone'] ?: null,
            'bank_account' => $validated['bankAccount'] ?: null,
            'bank_code' => $validated['bankCode'] ?: null,
            'bank_number' => $validated['bankNumber'] ?: null,
            'amount' => (float) $validated['amount'],
            'referrer_info' => $validated['referrerInfo'] ?: null,
            'notes' => $validated['notes'] ?: null,
        ];

        try {
            if ($request) {
                $service->update($request, $payload);
                $message = 'Đã cập nhật yêu cầu chi hoa hồng';
            } else {
                $service->create($payload, $this->actor());
                $message = 'Đã tạo yêu cầu chi hoa hồng';
            }
        } catch (DomainException $exception) {
            $this->dispatch('swal:alert', ['icon' => 'error', 'title' => 'Không thể lưu', 'text' => $exception->getMessage()]);

            return null;
        }

        if ($exit) {
            session()->flash('status', $message);

            $this->redirectRoute('commissions.index');

            return null;
        }

        $this->dispatch('swal:alert', ['icon' => 'success', 'title' => $message, 'toast' => true, 'position' => 'top-end', 'timer' => 2200]);

        if (! $request) {
            $this->reset(['contractType', 'contractId', 'receiverName', 'receiverPhone', 'bankAccount', 'bankCode', 'bankNumber', 'amount', 'referrerInfo', 'notes', 'selectedSavedAccountId']);
        }

        return null;
    }

    public function getVietQrUrl(): string
    {
        if (! $this->bankCode || ! $this->bankNumber) {
            return '';
        }

        $amount = $this->amount !== '' ? (int) preg_replace('/\D+/', '', $this->amount) : 0;
        $contract = $this->contractId !== '' ? Contract::query()->find((int) $this->contractId) : null;
        $contractNumber = $contract?->contract_number ?: ($contract ? '#'.$contract->id : 'Hoa hong');
        $receiverName = $this->cleanReceiverName($this->receiverName);
        $description = "Chi hoa hong HD {$contractNumber}";

        $query = [
            'addInfo' => $description,
            'accountName' => $receiverName,
        ];

        if ($amount > 0) {
            $query['amount'] = $amount;
        }

        return "https://img.vietqr.io/image/{$this->bankCode}-{$this->bankNumber}-compact2.png?" . http_build_query($query);
    }

    public function render(): View
    {
        $contracts = Contract::query()
            ->with('customer')
            ->when($this->contractType !== '', fn ($query) => $query->where('type', $this->contractType))
            ->orderByDesc('signed_at')
            ->orderByDesc('id')
            ->get();

        $savedAccounts = CommissionRequest::query()
            ->where('user_id', Auth::id())
            ->whereNotNull('receiver_name')
            ->where(fn ($query) => $query->whereNotNull('bank_number')->orWhereNotNull('bank_account'))
            ->latest()
            ->get()
            ->unique(fn (CommissionRequest $item): string => $item->receiver_name.'_'.($item->bank_number ?: $item->bank_account))
            ->values();

        return view('livewire.commissions.commission-request-form', [
            'contractTypes' => ContractType::options(),
            'contracts' => $contracts,
            'savedAccounts' => $savedAccounts,
        ]);
    }

    private function authorizeEditable(CommissionRequest $request): void
    {
        $actor = $this->actor();
        abort_if($request->status === CommissionRequestStatus::Paid, 403);
        abort_unless(
            $request->user_id === $actor->id || $actor->can(PermissionEnum::CommissionUpdate->value),
            403,
        );
        Gate::authorize(PermissionEnum::CommissionUpdate->value);
    }

    private function actor(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        return $user;
    }

    private function cleanReceiverName(mixed $value): string
    {
        return strtoupper(Str::ascii(trim((string) $value)));
    }

    /**
     * @return array<string, string>
     */
    private function loadBanks(): array
    {
        return Cache::remember('vietqr_banks_list', 86400, function (): array {
            try {
                $response = Http::timeout(5)->get('https://api.vietqr.io/v2/banks');

                if ($response->successful()) {
                    $banks = collect($response->json('data', []))
                        ->mapWithKeys(function (array $bank): array {
                            $bin = (string) ($bank['bin'] ?? '');
                            $code = (string) ($bank['code'] ?? '');
                            $name = (string) ($bank['shortName'] ?? $bank['short_name'] ?? '');

                            return $bin && $name ? [$bin => "{$name} ({$code})"] : [];
                        })
                        ->all();

                    if ($banks !== []) {
                        asort($banks);

                        return $banks;
                    }
                }
            } catch (\Throwable) {
                // Fall back to a static list when VietQR is unavailable locally.
            }

            $banks = [
                '970436' => 'Vietcombank (VCB)',
                '970407' => 'Techcombank (TCB)',
                '970418' => 'BIDV',
                '970415' => 'VietinBank (ICB)',
                '970405' => 'Agribank (VBA)',
                '970422' => 'MBBank (MB)',
                '970416' => 'ACB',
                '970432' => 'VPBank (VPB)',
                '970423' => 'TPBank (TPB)',
                '970403' => 'Sacombank (STB)',
                '970437' => 'HDBank (HDB)',
                '970441' => 'VIB',
                '970443' => 'SHB',
                '970426' => 'MSB',
                '970448' => 'OCB',
            ];
            asort($banks);

            return $banks;
        });
    }
}
