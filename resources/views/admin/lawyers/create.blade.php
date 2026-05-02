@extends('admin.layouts.app')
@section('title', 'Add Lawyer')
@section('page-title', 'Add Lawyer')

@section('content')
<div class="max-w-2xl">
    <div class="rounded-xl overflow-hidden" style="background: var(--bg-card); border: 1px solid var(--border-color);">
        <div class="px-5 py-4" style="border-bottom: 1px solid var(--border-color);">
            <h2 class="text-sm font-semibold">Lawyer Details</h2>
        </div>
        <form method="POST" action="{{ route('admin.lawyers.store') }}" class="p-5 space-y-4">
            @csrf
            @include('admin.lawyers._form')
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-5 py-2 rounded-lg text-sm font-semibold hover:opacity-90"
                        style="background: var(--gold); color: #0D1B2A;">Add Lawyer</button>
                <a href="{{ route('admin.lawyers.index') }}" class="px-5 py-2 rounded-lg text-sm"
                   style="border: 1px solid var(--border-color); color: var(--text-secondary);">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
