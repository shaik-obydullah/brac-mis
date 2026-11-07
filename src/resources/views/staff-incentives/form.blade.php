@php
    $staffId = old('staff_id', $incentive?->staff_id ?? request('staff_id'));
    $incentiveDate = old('incentive_date', $incentive?->incentive_date?->format('Y-m-d') ?? now()->toDateString());
    $status = old('status', $incentive?->status ?? 'pending');
    $paidFrom = old('paid_from', $incentive?->paid_from ?? 'cash');
@endphp

<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
    <div class="md:col-span-2">
        <label class="field-label" for="staff_id">Staff member <span class="text-rose-500">*</span></label>
        <select id="staff_id" name="staff_id" required class="field">
            <option value="">Select staff member</option>
            @foreach($staff as $member)
                <option value="{{ $member->id }}" {{ (string) $staffId === (string) $member->id ? 'selected' : '' }}>
                    {{ $member->user?->name ?? $member->employee_id }} — {{ $member->employee_id }} ({{ $member->branch?->name ?? 'No branch' }})
                </option>
            @endforeach
        </select>
        @error('staff_id') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="field-label" for="title">Incentive title <span class="text-rose-500">*</span></label>
        <input id="title" type="text" name="title" value="{{ old('title', $incentive?->title) }}" required placeholder="e.g. Performance bonus" class="field">
        @error('title') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="field-label" for="amount">Amount <span class="text-rose-500">*</span></label>
        <input id="amount" type="number" step="0.01" min="0" name="amount" value="{{ old('amount', $incentive?->amount) }}" required class="field">
        <p class="mt-1 text-xs text-slate-500">Posted automatically to accounting: paid = posted, pending = draft.</p>
        @error('amount') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="field-label" for="paid_from">Paid from</label>
        <select id="paid_from" name="paid_from" class="field">
            @foreach($paidFromOptions as $value => $label)
                <option value="{{ $value }}" {{ $paidFrom === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-slate-500">Which account the payment is credited to.</p>
        @error('paid_from') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="field-label" for="incentive_date">Incentive date <span class="text-rose-500">*</span></label>
        <input id="incentive_date" type="date" name="incentive_date" value="{{ $incentiveDate }}" required class="field">
        @error('incentive_date') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="field-label" for="status">Status <span class="text-rose-500">*</span></label>
        <select id="status" name="status" required class="field">
            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid</option>
        </select>
        @error('status') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="field-label" for="notes">Notes</label>
        <textarea id="notes" name="notes" rows="4" class="field" placeholder="Add any notes about this incentive...">{{ old('notes', $incentive?->notes) }}</textarea>
        @error('notes') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
    </div>
</div>
