@extends('layouts.app')

@section('title', 'Create Journal Entry')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Create Journal Entry</h1>
    <p class="mt-1 text-sm text-slate-500">Record a balanced double-entry transaction.</p>
</div>

@include('finances._tabs')

<div class="card p-6">
    <form action="{{ route('finances.store') }}" method="POST">
        @csrf

        @include('finances.form', [
            'journalEntry' => null,
            'submitLabel' => 'Create entry',
            'cancelUrl' => route('finances.index'),
        ])
    </form>
</div>
@endsection
