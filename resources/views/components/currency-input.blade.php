@props([
    'id',
    'wire' => null,
    'label' => null,
    'required' => false,
    'error' => null,
    'suffix' => 'đ',
])

<div {{ $attributes->only('class') }}
    x-data="{
        raw: @entangle($wire),
        get display() {
            if (!this.raw && this.raw !== 0) return '';
            return Number(this.raw).toLocaleString('vi-VN');
        },
        set display(val) {
            let clean = val.replace(/\D/g, '');
            this.raw = clean ? clean : '';
        }
    }"
>
    @if ($label)
        <label for="{{ $id }}" class="form-label">
            {{ $label }}
            @if ($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <div class="input-group">
        <input
            id="{{ $id }}"
            type="text"
            class="form-control sales-number @if($error) @error($error) is-invalid @enderror @endif"
            x-model="display"
            {{ $attributes->except('class') }}
        >
        @if ($suffix)
            <span class="input-group-text bg-light">{{ $suffix }}</span>
        @endif
    </div>

    @if ($error)
        @error($error)
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    @endif
</div>
