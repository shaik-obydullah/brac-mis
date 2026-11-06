@php
    $selectedType = old('support_type', $support->support_type);
    $selectedStatus = old('status', $support->status);
    $formattedDate = old('support_date', $support->support_date?->format('Y-m-d'));
    $singleType = count($types) === 1 ? array_key_first($types) : null;
@endphp

@if($singleType)
    <input type="hidden" name="support_type" value="{{ $singleType }}">
@endif

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @unless($singleType)
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Support Type <span class="text-red-500">*</span></label>
            <select name="support_type" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                @foreach($types as $value => $label)
                    <option value="{{ $value }}" {{ $selectedType === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('support_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    @endunless

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Support Date <span class="text-red-500">*</span></label>
        <input type="date" name="support_date" value="{{ $formattedDate }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
        @error('support_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
        <select name="status" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
            @foreach($statuses as $value => $label)
                <option value="{{ $value }}" {{ $selectedStatus === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">User Profile</label>
        <select name="user_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
            <option value="">Not linked to a registered user</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" {{ old('user_id', $support->user_id) == $user->id ? 'selected' : '' }}>
                    {{ $user->name }} &middot; {{ $user->profile_type_label }}{{ $user->phone ? ' &middot; ' . $user->phone : '' }}
                </option>
            @endforeach
        </select>
        @error('user_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
        <input type="number" name="amount" value="{{ old('amount', $support->amount) }}" step="0.01" min="0" placeholder="0.00" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
        <p class="text-xs text-gray-500 mt-1">Use this for financial support.</p>
        @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Recipient Name</label>
        <input type="text" name="recipient_name" value="{{ old('recipient_name', $support->recipient_name) }}" placeholder="For unregistered recipients" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
        <p class="text-xs text-gray-500 mt-1">Required if no user profile is selected.</p>
        @error('recipient_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Recipient Phone</label>
        <input type="text" name="recipient_phone" value="{{ old('recipient_phone', $support->recipient_phone) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
        @error('recipient_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-3">
        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
        <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">{{ old('description', $support->description) }}</textarea>
        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-3">
        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
        <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">{{ old('notes', $support->notes) }}</textarea>
        @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
</div>
