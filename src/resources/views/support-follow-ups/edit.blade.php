@extends('layouts.app')

@section('title', 'Edit Support Follow-up')

@section('content')
<div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Edit Support Follow-up</h1>
        <p class="mt-1 text-sm text-slate-500">Update the follow-up details and outcome.</p>
    </div>
    <a href="{{ route('support-follow-ups.index') }}" class="btn-secondary">Back to follow-ups</a>
</div>

@if($support)
    <div class="card mb-6 p-5">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Following up on</p>
        <p class="mt-1 text-lg font-semibold text-slate-900">{{ $support->type_label }}</p>
        <p class="text-sm text-slate-500">
            {{ optional($support->support_date)->format('d M Y') }}
            &middot;
            {{ $support->user?->name ?? $support->recipient_name ?? 'N/A' }}
        </p>
    </div>
@endif

<div class="card">
    <form method="POST" action="{{ route('support-follow-ups.update', $followUp) }}" class="p-5">
        @csrf
        @method('PUT')
        @include('support-follow-ups._form')

        <div class="mt-6 flex items-center gap-3 border-t border-slate-100 pt-5">
            <button type="submit" class="btn-primary">Update Follow-up</button>
            <a href="{{ route('support-follow-ups.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
