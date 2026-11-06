@extends('layouts.app')

@section('title', 'Edit Certificate')

@section('content')
<div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Edit Certificate</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $certificate->certificate_number }}</p>
    </div>
    <a href="{{ route('certificates.show', $certificate) }}" class="btn-secondary">Back to certificate</a>
</div>

<div class="card">
    <form method="POST" action="{{ route('certificates.update', $certificate) }}" class="p-5">
        @csrf
        @method('PUT')
        <div class="mb-5 rounded-xl bg-slate-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Course</p>
            <p class="mt-1 font-semibold text-slate-900">{{ $certificate->course->title ?? 'N/A' }}</p>
            <p class="text-sm text-slate-500">{{ $certificate->course->code ?? '' }}</p>
        </div>
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div>
                <label class="field-label" for="certificate_number">Certificate number</label>
                <input id="certificate_number" type="text" name="certificate_number" value="{{ old('certificate_number', $certificate->certificate_number) }}" class="field" required>
                @error('certificate_number')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="field-label" for="recipient_name">Recipient name</label>
                <input id="recipient_name" type="text" name="recipient_name" value="{{ old('recipient_name', $certificate->recipient_name) }}" class="field" required>
                @error('recipient_name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="field-label" for="issued_date">Issued date</label>
                <input id="issued_date" type="date" name="issued_date" value="{{ old('issued_date', optional($certificate->issued_date)->format('Y-m-d')) }}" class="field" required>
                @error('issued_date')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="field-label" for="expiry_date">Expiry date</label>
                <input id="expiry_date" type="date" name="expiry_date" value="{{ old('expiry_date', optional($certificate->expiry_date)->format('Y-m-d')) }}" class="field">
                @error('expiry_date')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="field-label" for="status">Status</label>
                <select id="status" name="status" class="field" required>
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" {{ old('status', $certificate->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <label class="field-label" for="remarks">Remarks</label>
                <textarea id="remarks" name="remarks" rows="3" class="field">{{ old('remarks', $certificate->remarks) }}</textarea>
                @error('remarks')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="mt-6 flex items-center gap-3 border-t border-slate-100 pt-5">
            <button type="submit" class="btn-primary">Update Certificate</button>
            <a href="{{ route('certificates.show', $certificate) }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
