@extends('layouts.app')

@section('title', 'Edit Finance Entry')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Edit Finance Entry</h1>
    <p class="mt-1 text-sm text-slate-500">Update the details of this entry.</p>
</div>

<div class="card p-6">
    <form action="{{ route('finances.update', $finance) }}" method="POST">
        @csrf
        @method('PUT')

        @include('finances.form', ['finance' => $finance])

        <div class="mt-8 flex items-center gap-4 border-t border-slate-100 pt-6">
            <button type="submit" class="btn-primary">Update Entry</button>
            <a href="{{ route('finances.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
