@php
    $account = $account ?? null;
    $submitLabel = $submitLabel ?? 'Save account';
    $cancelUrl = $cancelUrl ?? route('finances.accounts.index');
@endphp

@if($errors->any())
    <div class="mb-6 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
        <p class="font-semibold">Please fix the following errors:</p>
        <ul class="mt-1 list-inside list-disc">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
    <div>
        <label class="field-label" for="code">Account code <span class="text-rose-500">*</span></label>
        <input id="code" type="text" name="code" maxlength="20" required
               value="{{ old('code', $account?->code) }}" placeholder="e.g. 1020" class="field">
        @error('code') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="field-label" for="name">Account name <span class="text-rose-500">*</span></label>
        <input id="name" type="text" name="name" maxlength="255" required
               value="{{ old('name', $account?->name) }}" placeholder="e.g. Petty Cash" class="field">
        @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="field-label" for="type">Account type <span class="text-rose-500">*</span></label>
        <select id="type" name="type" required class="field">
            <option value="">Select type</option>
            @foreach($types as $value => $label)
                <option value="{{ $value }}" {{ old('type', $account?->type) === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-slate-500">The normal balance is set automatically from the type.</p>
        @error('type') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="field-label" for="parent_id">Parent account</label>
        <select id="parent_id" name="parent_id" class="field">
            <option value="">None</option>
            @foreach($accounts as $option)
                <option value="{{ $option->id }}" {{ (string) old('parent_id', $account?->parent_id) === (string) $option->id ? 'selected' : '' }}>
                    {{ $option->code }} · {{ $option->name }}
                </option>
            @endforeach
        </select>
        @error('parent_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="field-label" for="description">Description</label>
        <textarea id="description" name="description" rows="3" maxlength="1000" class="field">{{ old('description', $account?->description) }}</textarea>
        @error('description') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500"
                   {{ old('is_active', $account?->is_active ?? true) ? 'checked' : '' }}>
            Active account
        </label>
    </div>
</div>

<div class="mt-8 flex items-center gap-4 border-t border-slate-100 pt-6">
    <button type="submit" class="btn-primary">{{ $submitLabel }}</button>
    <a href="{{ $cancelUrl }}" class="btn-secondary">Cancel</a>
</div>
