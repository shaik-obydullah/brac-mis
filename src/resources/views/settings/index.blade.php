@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Settings</h1>
    <p class="mt-1 text-sm text-slate-500">Manage system-wide configuration values.</p>
</div>

<form action="{{ route('settings.update') }}" method="POST">
    @csrf
    @method('PUT')

    @error('settings')
        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ $message }}</div>
    @enderror

    <div class="space-y-6">
        @foreach($settings as $group => $items)
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">{{ ucfirst($group ?: 'general') }}</h2>
                        <p class="mt-0.5 text-xs text-slate-500">{{ $items->count() }} configuration {{ Str::plural('value', $items->count()) }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">
                    @foreach($items as $setting)
                        @php($isLong = strlen((string) $setting->value) > 60)
                        <div class="{{ $isLong ? 'md:col-span-2' : '' }}">
                            <label class="field-label" for="setting-{{ $setting->id }}">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                            @if($isLong)
                                <textarea id="setting-{{ $setting->id }}" name="settings[{{ $setting->key }}]" rows="3" class="field">{{ old('settings.' . $setting->key, $setting->value) }}</textarea>
                            @else
                                <input id="setting-{{ $setting->id }}" type="text" name="settings[{{ $setting->key }}]" value="{{ old('settings.' . $setting->key, $setting->value) }}" class="field">
                            @endif
                            <p class="mt-1 text-[11px] text-slate-400">{{ $setting->key }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6 flex items-center gap-3">
        <button type="submit" class="btn-primary">Save settings</button>
        <a href="{{ url('/') }}" class="btn-secondary">Cancel</a>
    </div>
</form>
@endsection
