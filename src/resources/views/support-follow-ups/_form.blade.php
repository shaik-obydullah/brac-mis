@php
    $selectedSupportId = old('immediate_support_id', $followUp->immediate_support_id);
@endphp

<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
    <div class="md:col-span-2">
        <label class="field-label" for="immediate_support_id">Support record</label>
        <select id="immediate_support_id" name="immediate_support_id" class="field" required>
            <option value="">Select a support record</option>
            @foreach($supports as $option)
                @php
                    $recipient = $option->beneficiary?->name
                        ?? $option->returnee?->beneficiary?->name
                        ?? $option->recipient_name
                        ?? 'N/A';
                @endphp
                <option value="{{ $option->id }}" {{ (string) $selectedSupportId === (string) $option->id ? 'selected' : '' }}>
                    {{ optional($option->support_date)->format('d M Y') }} &middot; {{ $option->type_label }} &middot; {{ $recipient }}
                </option>
            @endforeach
        </select>
        @error('immediate_support_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="field-label" for="follow_up_date">Follow-up date</label>
        <input id="follow_up_date" type="date" name="follow_up_date"
               value="{{ old('follow_up_date', optional($followUp->follow_up_date)->format('Y-m-d') ?? now()->toDateString()) }}"
               class="field" required>
        @error('follow_up_date')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="field-label" for="next_follow_up_date">Next follow-up date</label>
        <input id="next_follow_up_date" type="date" name="next_follow_up_date"
               value="{{ old('next_follow_up_date', optional($followUp->next_follow_up_date)->format('Y-m-d')) }}"
               class="field">
        @error('next_follow_up_date')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="field-label" for="method">Method</label>
        <select id="method" name="method" class="field" required>
            @foreach($methods as $value => $label)
                <option value="{{ $value }}" {{ old('method', $followUp->method) === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('method')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="field-label" for="status">Status</label>
        <select id="status" name="status" class="field" required>
            @foreach($statuses as $value => $label)
                <option value="{{ $value }}" {{ old('status', $followUp->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label class="field-label" for="outcome">Outcome</label>
        <textarea id="outcome" name="outcome" rows="3" class="field" placeholder="What was the result of the follow-up?">{{ old('outcome', $followUp->outcome) }}</textarea>
        @error('outcome')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label class="field-label" for="notes">Notes</label>
        <textarea id="notes" name="notes" rows="3" class="field" placeholder="Additional notes">{{ old('notes', $followUp->notes) }}</textarea>
        @error('notes')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
</div>
