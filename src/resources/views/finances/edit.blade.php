@extends('layouts.app')

@section('title', 'Edit Journal Entry')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Edit Journal Entry</h1>
    <p class="mt-1 text-sm text-slate-500">{{ $journalEntry->entry_no }} · {{ $journalEntry->entry_date?->format('d M Y') }}</p>
</div>

@include('finances._tabs')

<div class="card p-6">
    <form action="{{ route('finances.update', $journalEntry) }}" method="POST">
        @csrf
        @method('PUT')

        @include('finances.form', [
            'journalEntry' => $journalEntry,
            'submitLabel' => 'Update entry',
            'cancelUrl' => route('finances.show', $journalEntry),
        ])
    </form>
</div>
@endsection
