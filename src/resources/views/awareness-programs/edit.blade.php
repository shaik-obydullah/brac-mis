@extends('layouts.app')

@section('title', 'Edit Awareness Program')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Edit Awareness Program</h1>
    <p class="mt-1 text-sm text-slate-500">Update the details of this awareness program.</p>
</div>

<div class="card p-6">
    <form action="{{ route('awareness-programs.update', $program) }}" method="POST">
        @csrf
        @method('PUT')

        @include('awareness-programs.form', ['program' => $program])

        <div class="mt-8 flex items-center gap-4 border-t border-slate-100 pt-6">
            <button type="submit" class="btn-primary">Update Program</button>
            <a href="{{ route('awareness-programs.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
