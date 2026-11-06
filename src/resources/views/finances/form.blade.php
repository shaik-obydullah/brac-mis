@php
    $entry = $journalEntry ?? null;

    $defaultLines = [];
    if ($entry && $entry->lines->count()) {
        foreach ($entry->lines as $line) {
            $defaultLines[] = [
                'account_id' => $line->account_id,
                'description' => $line->description,
                'debit' => (float) $line->debit,
                'credit' => (float) $line->credit,
            ];
        }
    }

    if (old('lines')) {
        $defaultLines = [];
        foreach (old('lines') as $line) {
            $defaultLines[] = [
                'account_id' => $line['account_id'] ?? '',
                'description' => $line['description'] ?? '',
                'debit' => (float) ($line['debit'] ?? 0),
                'credit' => (float) ($line['credit'] ?? 0),
            ];
        }
    }

    if (! count($defaultLines)) {
        $defaultLines = [
            ['account_id' => '', 'description' => '', 'debit' => 0, 'credit' => 0],
            ['account_id' => '', 'description' => '', 'debit' => 0, 'credit' => 0],
        ];
    }

    $accountOptions = $accounts->map(fn ($account) => [
        'id' => $account->id,
        'label' => $account->code.' · '.$account->name,
        'type' => $account->type_label,
    ]);
@endphp

@if($errors->any())
    <div class="mb-6 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
        <p class="font-semibold">Please fix the following errors:</p>
        <ul class="mt-1 list-inside list-disc">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 gap-5 md:grid-cols-3">
    <div>
        <label class="field-label" for="entry_date">Entry date <span class="text-rose-500">*</span></label>
        <input id="entry_date" type="date" name="entry_date" required
               value="{{ old('entry_date', $entry?->entry_date?->format('Y-m-d') ?? now()->toDateString()) }}" class="field">
        @error('entry_date') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="field-label" for="reference">Reference</label>
        <input id="reference" type="text" name="reference" maxlength="255"
               value="{{ old('reference', $entry?->reference) }}" placeholder="Voucher, receipt or invoice no." class="field">
        @error('reference') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="field-label" for="branch_id">Branch</label>
        <select id="branch_id" name="branch_id" class="field">
            <option value="">Organisation-wide</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}" {{ (string) old('branch_id', $entry?->branch_id) === (string) $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
            @endforeach
        </select>
        @error('branch_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="field-label" for="description">Description</label>
        <input id="description" type="text" name="description" maxlength="2000"
               value="{{ old('description', $entry?->description) }}" placeholder="What is this entry for?" class="field">
        @error('description') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="field-label" for="status">Status <span class="text-rose-500">*</span></label>
        <select id="status" name="status" required class="field">
            @foreach($statuses as $value => $label)
                <option value="{{ $value }}" {{ old('status', $entry?->status ?? 'posted') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-slate-500">Only posted entries appear in reports.</p>
        @error('status') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-8">
    <div class="mb-3 flex flex-col justify-between gap-2 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-base font-semibold text-slate-900">Journal lines</h2>
            <p class="text-xs text-slate-500">Every entry needs at least two lines with equal total debits and credits.</p>
        </div>
        <button type="button" id="add-line" class="btn-secondary">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add line
        </button>
    </div>

    @error('lines') <p class="mb-3 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs text-rose-700">{{ $message }}</p> @enderror
    @error('lines.*') <p class="mb-3 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs text-rose-700">{{ $message }}</p> @enderror

    <div class="overflow-x-auto rounded-xl border border-slate-200">
        <table class="w-full min-w-[860px]">
            <thead class="bg-slate-50">
                <tr>
                    <th class="table-head px-3 py-2 text-left">Account</th>
                    <th class="table-head px-3 py-2 text-left">Line description</th>
                    <th class="table-head px-3 py-2 text-right">Debit</th>
                    <th class="table-head px-3 py-2 text-right">Credit</th>
                    <th class="table-head px-3 py-2"></th>
                </tr>
            </thead>
            <tbody id="line-rows" class="divide-y divide-slate-100 bg-white">
                @foreach($defaultLines as $index => $line)
                    <tr class="line-row">
                        <td class="px-3 py-2 align-top">
                            <select name="lines[{{ $index }}][account_id]" class="field line-account" required>
                                <option value="">Select account</option>
                                @foreach($accountOptions as $option)
                                    <option value="{{ $option['id'] }}" {{ (string) $line['account_id'] === (string) $option['id'] ? 'selected' : '' }}>
                                        {{ $option['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-3 py-2 align-top">
                            <input type="text" name="lines[{{ $index }}][description]" maxlength="255"
                                   value="{{ $line['description'] }}" class="field" placeholder="Optional note">
                        </td>
                        <td class="px-3 py-2 align-top">
                            <input type="number" step="0.01" min="0" name="lines[{{ $index }}][debit]"
                                   value="{{ $line['debit'] > 0 ? $line['debit'] : '' }}" class="field line-debit text-right" placeholder="0.00">
                        </td>
                        <td class="px-3 py-2 align-top">
                            <input type="number" step="0.01" min="0" name="lines[{{ $index }}][credit]"
                                   value="{{ $line['credit'] > 0 ? $line['credit'] : '' }}" class="field line-credit text-right" placeholder="0.00">
                        </td>
                        <td class="px-3 py-2 align-top text-right">
                            <button type="button" class="remove-line rounded-lg px-2 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50">Remove</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-slate-50">
                <tr>
                    <td class="px-3 py-3 text-right text-sm font-semibold text-slate-700" colspan="2">Totals</td>
                    <td class="px-3 py-3 text-right text-sm font-bold text-brand-700" id="total-debit">৳ 0.00</td>
                    <td class="px-3 py-3 text-right text-sm font-bold text-sky-700" id="total-credit">৳ 0.00</td>
                    <td></td>
                </tr>
                <tr>
                    <td class="px-3 pb-3 text-right text-xs text-slate-500" colspan="4">
                        <span id="balance-message" class="font-semibold">Add debit and credit amounts.</span>
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="mt-8 flex items-center gap-4 border-t border-slate-100 pt-6">
    <button type="submit" class="btn-primary">{{ $submitLabel ?? 'Save entry' }}</button>
    <a href="{{ $cancelUrl ?? route('finances.index') }}" class="btn-secondary">Cancel</a>
</div>

@push('scripts')
<script>
    (function () {
        const rows = document.getElementById('line-rows');
        const addButton = document.getElementById('add-line');
        const totalDebit = document.getElementById('total-debit');
        const totalCredit = document.getElementById('total-credit');
        const balanceMessage = document.getElementById('balance-message');

        if (!rows || !addButton) return;

        const money = (value) => '৳ ' + value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        const recalculate = () => {
            let debit = 0;
            let credit = 0;

            rows.querySelectorAll('.line-row').forEach((row) => {
                debit += parseFloat(row.querySelector('.line-debit')?.value || 0) || 0;
                credit += parseFloat(row.querySelector('.line-credit')?.value || 0) || 0;
            });

            totalDebit.textContent = money(debit);
            totalCredit.textContent = money(credit);

            const difference = Math.round((debit - credit) * 100) / 100;

            if (debit === 0 && credit === 0) {
                balanceMessage.textContent = 'Add debit and credit amounts.';
                balanceMessage.className = 'font-semibold text-slate-500';
            } else if (Math.abs(difference) < 0.005) {
                balanceMessage.textContent = 'Balanced — debits equal credits.';
                balanceMessage.className = 'font-semibold text-emerald-600';
            } else if (difference > 0) {
                balanceMessage.textContent = 'Out of balance: credits are short by ' + money(difference) + '.';
                balanceMessage.className = 'font-semibold text-rose-600';
            } else {
                balanceMessage.textContent = 'Out of balance: debits are short by ' + money(Math.abs(difference)) + '.';
                balanceMessage.className = 'font-semibold text-rose-600';
            }
        };

        const nextIndex = () => {
            const indexes = Array.from(rows.querySelectorAll('select[name^="lines["]')).map((select) => {
                const match = select.name.match(/^lines\[(\d+)\]/);
                return match ? parseInt(match[1], 10) : -1;
            });

            return indexes.length ? Math.max(...indexes) + 1 : 0;
        };

        const bindRow = (row) => {
            row.querySelectorAll('.line-debit, .line-credit').forEach((input) => {
                input.addEventListener('input', () => {
                    const debit = row.querySelector('.line-debit');
                    const credit = row.querySelector('.line-credit');

                    if (input === debit && parseFloat(debit.value || 0) > 0) {
                        credit.value = '';
                    }

                    if (input === credit && parseFloat(credit.value || 0) > 0) {
                        debit.value = '';
                    }

                    recalculate();
                });
            });

            row.querySelector('.remove-line')?.addEventListener('click', () => {
                if (rows.querySelectorAll('.line-row').length <= 2) {
                    row.querySelectorAll('input, select').forEach((field) => {
                        if (field.tagName === 'SELECT') {
                            field.value = '';
                        } else {
                            field.value = '';
                        }
                    });
                } else {
                    row.remove();
                }

                recalculate();
            });
        };

        addButton.addEventListener('click', () => {
            const index = nextIndex();
            const row = document.createElement('tr');
            row.className = 'line-row';
            row.innerHTML = `
                <td class="px-3 py-2 align-top">
                    <select name="lines[${index}][account_id]" class="field line-account" required>
                        <option value="">Select account</option>
                        @foreach($accountOptions as $option)
                            <option value="{{ $option['id'] }}">{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                </td>
                <td class="px-3 py-2 align-top">
                    <input type="text" name="lines[${index}][description]" maxlength="255" class="field" placeholder="Optional note">
                </td>
                <td class="px-3 py-2 align-top">
                    <input type="number" step="0.01" min="0" name="lines[${index}][debit]" class="field line-debit text-right" placeholder="0.00">
                </td>
                <td class="px-3 py-2 align-top">
                    <input type="number" step="0.01" min="0" name="lines[${index}][credit]" class="field line-credit text-right" placeholder="0.00">
                </td>
                <td class="px-3 py-2 align-top text-right">
                    <button type="button" class="remove-line rounded-lg px-2 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50">Remove</button>
                </td>
            `;

            rows.appendChild(row);
            bindRow(row);
            row.querySelector('.line-account')?.focus();
            recalculate();
        });

        rows.querySelectorAll('.line-row').forEach(bindRow);
        recalculate();
    })();
</script>
@endpush
