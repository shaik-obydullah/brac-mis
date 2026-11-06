@extends('layouts.app')

@section('title', 'Create Designation')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Create Designation</h1>
    <p class="mt-1 text-sm text-slate-500">Add a new job title to the organisation.</p>
</div>

<div class="card p-6">
    <form action="{{ route('designations.store') }}" method="POST">
        @csrf

        @include('designations.form', ['designation' => null])

        <div class="mt-8 flex items-center gap-4 border-t border-slate-100 pt-6">
            <button type="submit" class="btn-primary">Save Designation</button>
            <a href="{{ route('designations.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
