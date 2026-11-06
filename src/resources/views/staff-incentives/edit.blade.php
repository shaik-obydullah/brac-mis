@extends('layouts.app')

@section('title', 'Edit Staff Incentive')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Edit Staff Incentive</h1>
    <p class="mt-1 text-sm text-slate-500">Update the details of this incentive.</p>
</div>

<div class="card p-6">
    <form action="{{ route('staff-incentives.update', $incentive) }}" method="POST">
        @csrf
        @method('PUT')

        @include('staff-incentives.form', ['incentive' => $incentive])

        <div class="mt-8 flex items-center gap-4 border-t border-slate-100 pt-6">
            <button type="submit" class="btn-primary">Update Incentive</button>
            <a href="{{ route('staff-incentives.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
