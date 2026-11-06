@php
    $entryDate = old('entry_date', $finance?->entry_date?->format('Y-m-d') ?? now()->toDateString());
    $type = old('type', $finance?->type ?? 'expense');
    $currency = old('currency', $finance?->currency ?? 'BDT');
    $branchId = old('branch_id', $finance?->branch_id);
@endphp

<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
    <div>
        <label class="field-label" for="entry_date">Entry date <span class="text-rose-500">*</span></label>
        <input id="entry_date" type="date" name="entry_date" value="{{ $entryDate }}" required class="field">
        @error('entry_date') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="field-label" for="type">Type <span class="text-rose-500">*</span></label>
        <select id="type" name="type" required class="field">
            <option value="income" {{ $type === 'income' ? 'selected' : '' }}>Income</option>
            <option value="expense" {{ $type === 'expense' ? 'selected' : '' }}>Expense</option>
        </select>
        @error('type') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="field-label" for="category">Category</label>
        <input id="category" type="text" name="category" value="{{ old('category', $finance?->category) }}" placeholder="e.g. Reintegration support" class="field">
        @error('category') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-3 gap-3">
        <div class="col-span-2">
            <label class="field-label" for="amount">Amount <span class="text-rose-500">*</span></label>
            <input id="amount" type="number" step="0.01" min="0" name="amount" value="{{ old('amount', $finance?->amount) }}" required class="field">
            @error('amount') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="field-label" for="currency">Currency</label>
            <input id="currency" type="text" name="currency" value="{{ $currency }}" maxlength="3" class="field">
            @error('currency') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label class="field-label" for="branch_id">Branch</label>
        <select id="branch_id" name="branch_id" class="field">
            <option value="">No branch</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}" {{ (string) $branchId === (string) $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
            @endforeach
        </select>
        @error('branch_id') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="field-label" for="description">Description</label>
        <textarea id="description" name="description" rows="4" class="field" placeholder="Add any notes about this entry...">{{ old('description', $finance?->description) }}</textarea>
        @error('description') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
    </div>
</div>
