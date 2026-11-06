@extends('layouts.app')

@section('title', 'Create Account')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Create Account</h1>
    <p class="mt-1 text-sm text-slate-500">Add an account to the chart of accounts.</p>
</div>

@include('finances._tabs')

<div class="card p-6">
    <form action="{{ route('finances.accounts.store') }}" method="POST">
        @csrf

        @include('finances.accounts.form', [
            'account' => null,
            'submitLabel' => 'Create account',
            'cancelUrl' => route('finances.accounts.index'),
        ])
    </form>
</div>
@endsection
