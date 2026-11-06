@php
    $divisions = config('locations.divisions', []);
    $prefix = $prefix ?? 'location';
    $labelPrefix = $labelPrefix ?? '';
    $labelPrefix = $labelPrefix !== '' ? $labelPrefix.' ' : '';
    $required = $required ?? false;
    $fieldClass = $fieldClass ?? 'w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-brand-500 focus:border-brand-500';
    $labelClass = $labelClass ?? 'block text-sm font-medium text-gray-700 mb-1';
    $divisionName = $divisionName ?? 'division_id';
    $districtName = $districtName ?? 'district_id';
    $upazilaName = $upazilaName ?? 'upazila_id';
    $selectedDivision = old($divisionName, $divisionValue ?? null);
    $selectedDistrict = old($districtName, $districtValue ?? null);
    $selectedUpazila = old($upazilaName, $upazilaValue ?? null);
@endphp

<div>
    <label class="{{ $labelClass }}" for="{{ $prefix }}_division">{{ $labelPrefix }}Division @if($required)<span class="text-red-500">*</span>@endif</label>
    <select id="{{ $prefix }}_division" name="{{ $divisionName }}" @if($required) required @endif class="{{ $fieldClass }}">
        <option value="">Select Division</option>
        @foreach($divisions as $division => $districts)
            <option value="{{ $division }}" {{ (string) $selectedDivision === (string) $division ? 'selected' : '' }}>{{ $division }}</option>
        @endforeach
    </select>
    @error($divisionName) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label class="{{ $labelClass }}" for="{{ $prefix }}_district">{{ $labelPrefix }}District @if($required)<span class="text-red-500">*</span>@endif</label>
    <select id="{{ $prefix }}_district" name="{{ $districtName }}" @if($required) required @endif class="{{ $fieldClass }}">
        <option value="">Select District</option>
        @foreach($divisions as $division => $districts)
            @foreach($districts as $district => $upazilas)
                <option value="{{ $district }}" data-division="{{ $division }}" {{ (string) $selectedDistrict === (string) $district ? 'selected' : '' }}>{{ $district }}</option>
            @endforeach
        @endforeach
    </select>
    @error($districtName) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label class="{{ $labelClass }}" for="{{ $prefix }}_upazila">{{ $labelPrefix }}Upazila @if($required)<span class="text-red-500">*</span>@endif</label>
    <select id="{{ $prefix }}_upazila" name="{{ $upazilaName }}" @if($required) required @endif class="{{ $fieldClass }}">
        <option value="">Select Upazila</option>
        @foreach($divisions as $division => $districts)
            @foreach($districts as $district => $upazilas)
                @foreach($upazilas as $upazila)
                    <option value="{{ $upazila }}" data-district="{{ $district }}" {{ (string) $selectedUpazila === (string) $upazila ? 'selected' : '' }}>{{ $upazila }}</option>
                @endforeach
            @endforeach
        @endforeach
    </select>
    @error($upazilaName) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

@push('scripts')
<script>
    (function () {
        const division = document.getElementById('{{ $prefix }}_division');
        const district = document.getElementById('{{ $prefix }}_district');
        const upazila = document.getElementById('{{ $prefix }}_upazila');
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
