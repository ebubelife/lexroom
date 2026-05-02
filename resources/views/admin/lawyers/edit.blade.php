@extends('admin.layouts.app')
@section('title', 'Edit Lawyer')
@section('page-title', 'Edit Lawyer')

@section('content')
<div class="max-w-2xl">
    <div class="rounded-xl overflow-hidden" style="background: var(--bg-card); border: 1px solid var(--border-color);">
        <div class="px-5 py-4" style="border-bottom: 1px solid var(--border-color);">
            <h2 class="text-sm font-semibold">Edit — {{ $lawyer->name }}</h2>
        </div>
        <form method="POST" action="{{ route('admin.lawyers.update', $lawyer) }}" class="p-5 space-y-4">
            @csrf @method('PUT')
            @include('admin.lawyers._form')
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-5 py-2 rounded-lg text-sm font-semibold hover:opacity-90"
                        style="background: var(--gold); color: #0D1B2A;">Save Changes</button>
                <a href="{{ route('admin.lawyers.index') }}" class="px-5 py-2 rounded-lg text-sm"
                   style="border: 1px solid var(--border-color); color: var(--text-secondary);">Cancel</a>
                <form method="POST" action="{{ route('admin.lawyers.destroy', $lawyer) }}"
                      onsubmit="return confirm('Delete this lawyer?')" class="ml-auto">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-5 py-2 rounded-lg text-sm"
                            style="background: rgba(220,38,38,0.1); color: #DC2626; border: 1px solid rgba(220,38,38,0.2);">Delete</button>
                </form>
            </div>
        </form>
    </div>
</div>
@endsection
