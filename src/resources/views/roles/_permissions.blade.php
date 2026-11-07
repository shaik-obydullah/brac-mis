@php
    $selectedPermissions = $selectedPermissions ?? [];
@endphp

<div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div class="w-full sm:max-w-xs">
        <label class="field-label" for="permission-search">Search permissions</label>
        <input id="permission-search" type="search" placeholder="Module or action..." class="field">
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <span id="permission-count" class="mr-2 text-sm text-slate-500">
            {{ count($selectedPermissions) }} of {{ \App\Support\PermissionCatalog::total() }} selected
        </span>
        <button type="button" id="select-all-permissions" class="btn-secondary">Select all</button>
        <button type="button" id="clear-all-permissions" class="btn-secondary">Clear all</button>
    </div>
</div>

@if(empty($permissionGroups))
    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-10 text-center">
        <p class="text-sm font-medium text-slate-700">No permissions available</p>
        <p class="mt-1 text-sm text-slate-500">Run the permission seeder to populate the permission catalog.</p>
    </div>
@else
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 xl:grid-cols-3">
        @foreach($permissionGroups as $group)
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white" data-permission-group>
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 bg-slate-50/70 px-4 py-3">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-800">{{ $group['label'] }}</h3>
                        <p class="mt-0.5 text-[11px] text-slate-500">{{ count($group['permissions']) }} {{ \Illuminate\Support\Str::plural('permission', count($group['permissions'])) }}</p>
                    </div>
                    <label class="flex cursor-pointer items-center gap-1.5 text-xs font-medium text-slate-600">
                        <input type="checkbox" class="group-select rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                        All
                    </label>
                </div>

                <div class="divide-y divide-slate-100">
                    @foreach($group['permissions'] as $permission)
                        <label class="flex cursor-pointer items-center gap-2 px-4 py-2.5 hover:bg-slate-50" data-permission-item>
                            <input type="checkbox" name="permissions[]" value="{{ $permission['name'] }}"
                                   class="permission-checkbox rounded border-slate-300 text-brand-600 focus:ring-brand-500"
                                   {{ in_array($permission['name'], $selectedPermissions, true) ? 'checked' : '' }}>
                            <span class="text-sm text-slate-700">{{ $permission['label'] }}</span>
                            <span class="ml-auto font-mono text-[11px] text-slate-400">{{ $permission['name'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@endif

@once
    @push('scripts')
        <script>
            (function () {
                var search = document.getElementById('permission-search');
                var count = document.getElementById('permission-count');
                var checkboxes = Array.from(document.querySelectorAll('.permission-checkbox'));
                var items = Array.from(document.querySelectorAll('[data-permission-item]'));
                var groups = Array.from(document.querySelectorAll('[data-permission-group]'));

                function updateCount() {
                    var checked = checkboxes.filter(function (checkbox) { return checkbox.checked; }).length;
                    count.textContent = checked + ' of ' + checkboxes.length + ' selected';
                }

                function updateGroup(group) {
                    var selectAll = group.querySelector('.group-select');
                    if (! selectAll) return;

                    var visibleBoxes = Array.from(group.querySelectorAll('.permission-checkbox')).filter(function (checkbox) {
                        return ! checkbox.closest('[data-permission-item]').classList.contains('hidden');
                    });
                    var checkedBoxes = visibleBoxes.filter(function (checkbox) { return checkbox.checked; });

                    selectAll.checked = visibleBoxes.length > 0 && checkedBoxes.length === visibleBoxes.length;
                    selectAll.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < visibleBoxes.length;
                }

                groups.forEach(function (group) {
                    var selectAll = group.querySelector('.group-select');

                    if (selectAll) {
                        selectAll.addEventListener('change', function () {
                            group.querySelectorAll('.permission-checkbox').forEach(function (checkbox) {
                                if (! checkbox.closest('[data-permission-item]').classList.contains('hidden')) {
                                    checkbox.checked = selectAll.checked;
                                }
                            });
                            updateCount();
                            updateGroup(group);
                        });
                    }

                    group.querySelectorAll('.permission-checkbox').forEach(function (checkbox) {
                        checkbox.addEventListener('change', function () {
                            updateGroup(group);
                            updateCount();
                        });
                    });
                });

                document.getElementById('select-all-permissions').addEventListener('click', function () {
                    checkboxes.forEach(function (checkbox) { checkbox.checked = true; });
                    groups.forEach(updateGroup);
                    updateCount();
                });

                document.getElementById('clear-all-permissions').addEventListener('click', function () {
                    checkboxes.forEach(function (checkbox) { checkbox.checked = false; });
                    groups.forEach(updateGroup);
                    updateCount();
                });

                if (search) {
                    search.addEventListener('input', function () {
                        var term = search.value.toLowerCase().trim();

                        items.forEach(function (item) {
                            item.classList.toggle('hidden', term !== '' && ! item.textContent.toLowerCase().includes(term));
                        });

                        groups.forEach(function (group) {
                            var hasVisibleItem = Array.from(group.querySelectorAll('[data-permission-item]')).some(function (item) {
                                return ! item.classList.contains('hidden');
                            });

                            group.classList.toggle('hidden', ! hasVisibleItem);
                            updateGroup(group);
                        });
                    });
                }

                groups.forEach(updateGroup);
                updateCount();
            })();
        </script>
    @endpush
@endonce
