@extends('layouts.app')

@section('title', 'Edit ' . $categoryLabel)

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Edit {{ $categoryLabel }}</h1>
    <p class="text-gray-600 mt-1">Update the support record</p>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <form action="{{ route('support.update', ['category' => $category, 'immediateSupport' => $support]) }}" method="POST">
        @csrf
        @method('PUT')
        @include('immediate-supports._form')

        <div class="flex items-center gap-4 mt-8 pt-6 border-t border-gray-200">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-lg font-medium">Update Support</button>
            <a href="{{ route('support.show', ['category' => $category, 'immediateSupport' => $support]) }}" class="text-gray-600 hover:text-gray-800 px-4 py-2.5">Cancel</a>
        </div>
    </form>
</div>
@endsection
