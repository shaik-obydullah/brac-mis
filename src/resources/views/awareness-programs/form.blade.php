@php
    $divisions = config('locations.divisions', []);
    $selectedDivision = old('division', $program?->division);
    $selectedDistrict = old('district', $program?->district);
    $selectedUpazila = old('upazila', $program?->upazila);
@endphp

<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
    <div class="md:col-span-2">
        <label class="field-label" for="program_name">Program name <span class="text-rose-500">*</span></label>
        <input id="program_name" type="text" name="program_name" value="{{ old('program_name', $program?->program_name) }}" required placeholder="e.g. Safe Migration Awareness Session" class="field">
        @error('program_name') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="field-label" for="division">Division <span class="text-rose-500">*</span></label>
        <select id="division" name="division" required class="field">
            <option value="">Select division</option>
            @foreach($divisions as $division => $districts)
                <option value="{{ $division }}" {{ (string) $selectedDivision === (string) $division ? 'selected' : '' }}>{{ $division }}</option>
            @endforeach
        </select>
        @error('division') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="field-label" for="district">District <span class="text-rose-500">*</span></label>
        <select id="district" name="district" required class="field">
            <option value="">Select district</option>
            @foreach($divisions as $division => $districts)
                @foreach($districts as $district => $upazilas)
                    <option value="{{ $district }}" data-division="{{ $division }}" {{ (string) $selectedDistrict === (string) $district ? 'selected' : '' }}>{{ $district }}</option>
                @endforeach
            @endforeach
        </select>
        @error('district') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="field-label" for="upazila">Upazila <span class="text-rose-500">*</span></label>
        <select id="upazila" name="upazila" required class="field">
            <option value="">Select upazila</option>
            @foreach($divisions as $division => $districts)
                @foreach($districts as $district => $upazilas)
                    @foreach($upazilas as $upazila)
                        <option value="{{ $upazila }}" data-district="{{ $district }}" {{ (string) $selectedUpazila === (string) $upazila ? 'selected' : '' }}>{{ $upazila }}</option>
                    @endforeach
                @endforeach
            @endforeach
        </select>
        @error('upazila') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="field-label" for="address">Address</label>
        <input id="address" type="text" name="address" value="{{ old('address', $program?->address) }}" placeholder="Enter the program address" class="field">
        @error('address') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2 rounded-xl border border-slate-200 bg-slate-50 p-4">
        <p class="text-sm font-semibold text-slate-800">Participant outcomes</p>
        <p class="mt-1 text-xs text-slate-500">Save the program first. You can then add each participant and record whether they understood the awareness message on the program detail page.</p>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        const division = document.getElementById('division');
        const district = document.getElementById('district');
        const upazila = document.getElementById('upazila');
        if (!division || !district || !upazila) return;

        const filterUpazilas = () => {
            const selected = district.value;
            let currentIsValid = false;

            Array.from(upazila.options).forEach((option) => {
                if (!option.value) return;
                const matches = !selected || option.dataset.district === selected;
                option.hidden = !matches;
                option.disabled = !matches;
                if (matches && option.value === upazila.value) currentIsValid = true;
            });

            if (!currentIsValid) upazila.value = '';
        };

        const filterDistricts = () => {
            const selected = division.value;
            let currentIsValid = false;

            Array.from(district.options).forEach((option) => {
                if (!option.value) return;
                const matches = !selected || option.dataset.division === selected;
                option.hidden = !matches;
                option.disabled = !matches;
                if (matches && option.value === district.value) currentIsValid = true;
            });

            if (!currentIsValid) district.value = '';
            filterUpazilas();
        };

        division.addEventListener('change', filterDistricts);
        district.addEventListener('change', filterUpazilas);
        filterDistricts();
    })();
</script>
@endpush
