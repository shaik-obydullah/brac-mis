@extends('layouts.app')

@section('title', 'Create Staff Incentive')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Create Staff Incentive</h1>
    <p class="mt-1 text-sm text-slate-500">Record a new incentive for a staff member.</p>
</div>

<div class="card p-6">
    <form action="{{ route('staff-incentives.store') }}" method="POST">
        @csrf

        @include('staff-incentives.form', ['incentive' => null])

        <div class="mt-8 flex items-center gap-4 border-t border-slate-100 pt-6">
            <button type="submit" class="btn-primary">Save Incentive</button>
            <a href="{{ route('staff-incentives.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
