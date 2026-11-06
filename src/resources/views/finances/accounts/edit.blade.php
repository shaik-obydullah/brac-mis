@extends('layouts.app')

@section('title', 'Edit Account')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Edit Account</h1>
    <p class="mt-1 text-sm text-slate-500">{{ $account->code }} · {{ $account->name }}</p>
</div>

@include('finances._tabs')

<div class="card p-6">
    <form action="{{ route('finances.accounts.update', $account) }}" method="POST">
        @csrf
        @method('PUT')

        @include('finances.accounts.form', [
            'account' => $account,
            'submitLabel' => 'Update account',
            'cancelUrl' => route('finances.accounts.index'),
        ])
    </form>
</div>
@endsection
