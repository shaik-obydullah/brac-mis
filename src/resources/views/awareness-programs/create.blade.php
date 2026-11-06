@extends('layouts.app')

@section('title', 'Create Awareness Program')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Create Awareness Program</h1>
    <p class="mt-1 text-sm text-slate-500">Add a new awareness program with its location details.</p>
</div>

<div class="card p-6">
    <form action="{{ route('awareness-programs.store') }}" method="POST">
        @csrf

        @include('awareness-programs.form', ['program' => null])

        <div class="mt-8 flex items-center gap-4 border-t border-slate-100 pt-6">
            <button type="submit" class="btn-primary">Save Program</button>
            <a href="{{ route('awareness-programs.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
