@extends('layouts.app')

@section('title', 'Create Role')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Create Role</h1>
    <p class="text-gray-600 mt-1">Add a new role and assign permissions</p>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <form action="{{ route('roles.store') }}" method="POST">
        @csrf

        <div class="max-w-lg">
            <label class="block text-sm font-medium text-gray-700 mb-1">Role Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. data-entry-officer" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-purple-500 focus:border-purple-500">
            <p class="text-xs text-gray-500 mt-1">Lowercase letters, numbers and hyphens only (spaces are converted to hyphens).</p>
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mt-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Permissions</h2>
            </div>

            @error('permissions') <p class="text-red-500 text-xs mb-3">{{ $message }}</p> @enderror

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($permissions as $group => $items)
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-700 capitalize">{{ $group }}</h3>
                            <label class="flex items-center text-xs text-gray-600 cursor-pointer">
                                <input type="checkbox" class="group-select rounded border-gray-300 text-purple-600 focus:ring-purple-500 mr-1">
                                Select all
                            </label>
                        </div>
                        <div class="p-3 space-y-1.5">
                            @foreach($items as $permission)
                                <label class="flex items-start cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                           class="group-item mt-0.5 rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                                           {{ in_array($permission->name, old('permissions', []), true) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">{{ $permission->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex items-center gap-4 mt-8 pt-6 border-t border-gray-200">
            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2.5 rounded-lg font-medium">Save Role</button>
            <a href="{{ route('roles.index') }}" class="text-gray-600 hover:text-gray-800 px-4 py-2.5">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.group-select').forEach(function (selectAll) {
        var container = selectAll.closest('.border');
        var items = container.querySelectorAll('.group-item');

        selectAll.addEventListener('change', function () {
            items.forEach(function (item) {
                item.checked = selectAll.checked;
            });
        });

        items.forEach(function (item) {
            item.addEventListener('change', function () {
                selectAll.checked = items.length > 0 && Array.from(items).every(function (i) { return i.checked; });
            });
        });
    });
</script>
@endpush
