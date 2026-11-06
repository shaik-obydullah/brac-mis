@extends('layouts.app')

@section('title', 'Edit Designation')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Edit Designation</h1>
    <p class="mt-1 text-sm text-slate-500">Update the details of this designation.</p>
</div>

<div class="card p-6">
    <form action="{{ route('designations.update', $designation) }}" method="POST">
        @csrf
        @method('PUT')

        @include('designations.form', ['designation' => $designation])

        <div class="mt-8 flex items-center gap-4 border-t border-slate-100 pt-6">
            <button type="submit" class="btn-primary">Update Designation</button>
            <a href="{{ route('designations.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
