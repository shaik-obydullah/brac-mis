@php
    $status = old('status', $designation?->status ?? true);
@endphp

<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
    <div>
        <label class="field-label" for="name">Name <span class="text-rose-500">*</span></label>
        <input id="name" type="text" name="name" value="{{ old('name', $designation?->name) }}" required placeholder="e.g. Field Officer" class="field">
        @error('name') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="field-label" for="status">Status</label>
        <select id="status" name="status" class="field">
            <option value="1" {{ (string) $status === '1' ? 'selected' : '' }}>Active</option>
            <option value="0" {{ (string) $status === '0' ? 'selected' : '' }}>Inactive</option>
        </select>
        @error('status') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
    </div>
</div>
