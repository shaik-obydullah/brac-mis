@php
    $isEdit = $course->exists;
@endphp

<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
    <div>
        <label class="field-label" for="code">Course code</label>
        <input id="code" type="text" name="code" value="{{ old('code', $course->code) }}" class="field" placeholder="e.g. CRS-001" required>
        @error('code')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="field-label" for="title">Course title</label>
        <input id="title" type="text" name="title" value="{{ old('title', $course->title) }}" class="field" required>
        @error('title')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="field-label" for="category">Category</label>
        <input id="category" type="text" name="category" value="{{ old('category', $course->category) }}" class="field" placeholder="e.g. Skills, Awareness">
        @error('category')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="field-label" for="duration_hours">Duration (hours)</label>
        <input id="duration_hours" type="number" name="duration_hours" value="{{ old('duration_hours', $course->duration_hours) }}" min="1" max="1000" class="field">
        @error('duration_hours')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="field-label" for="level">Level</label>
        <select id="level" name="level" class="field" required>
            @foreach($levels as $value => $label)
                <option value="{{ $value }}" {{ old('level', $course->level) === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('level')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="field-label" for="status">Status</label>
        <select id="status" name="status" class="field" required>
            @foreach($statuses as $value => $label)
                <option value="{{ $value }}" {{ old('status', $course->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div class="md:col-span-2">
        <label class="field-label" for="description">Description</label>
        <textarea id="description" name="description" rows="4" class="field" placeholder="Course overview and learning outcomes">{{ old('description', $course->description) }}</textarea>
        @error('description')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
</div>
