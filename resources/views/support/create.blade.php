@extends('layouts.app')

@section('title', 'New Support Ticket — First Mediator')
@section('page-title', 'New Support Ticket')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6">

    <div class="mb-6">
        <h2 class="text-xl font-semibold" style="color: var(--text-primary);">Submit a Support Request</h2>
        <p class="text-sm mt-0.5" style="color: var(--text-secondary);">We typically respond within 24 hours.</p>
    </div>

    @if(session('success'))
        <div class="mb-5 px-4 py-3 rounded-lg text-sm font-medium"
             style="background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.2); color: #4ade80;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-5 px-4 py-3 rounded-lg text-sm" style="background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); color: #f87171;">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('support.store') }}">
        @csrf
        <div class="rounded-xl p-6 space-y-5" style="background: var(--bg-secondary); border: 1px solid var(--border-color);">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--text-secondary);">Your Name</label>
                    <input type="text" name="name" value="{{ old('name', $user?->name) }}" required
                           class="w-full px-4 py-2.5 rounded-lg text-sm"
                           style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary); outline: none;">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--text-secondary);">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $user?->email) }}" required
                           class="w-full px-4 py-2.5 rounded-lg text-sm"
                           style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary); outline: none;">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--text-secondary);">Subject</label>
                <input type="text" name="subject" value="{{ old('subject') }}" required maxlength="200"
                       placeholder="Briefly describe your issue"
                       class="w-full px-4 py-2.5 rounded-lg text-sm"
                       style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary); outline: none;">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--text-secondary);">Category</label>
                <select name="type" required
                        class="w-full px-4 py-2.5 rounded-lg text-sm"
                        style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary); outline: none;">
                    @foreach($types as $value => $label)
                        <option value="{{ $value }}" {{ old('type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--text-secondary);">Message</label>
                <textarea name="message" required rows="6" maxlength="5000"
                          placeholder="Describe your issue in detail…"
                          class="w-full px-4 py-2.5 rounded-lg text-sm resize-none"
                          style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary); outline: none;">{{ old('message') }}</textarea>
                <p class="text-xs mt-1" style="color: var(--text-secondary);">Max 5,000 characters</p>
            </div>

        </div>

        <div class="flex items-center justify-between mt-5">
            @auth
                <a href="{{ route('support.index') }}" class="text-sm" style="color: var(--text-secondary);">← My Tickets</a>
            @else
                <span></span>
            @endauth
            <button type="submit"
                    class="px-6 py-2.5 rounded-lg text-sm font-semibold hover:opacity-90 transition-all"
                    style="background: var(--gold); color: #fff;">
                Submit Ticket
            </button>
        </div>
    </form>
</div>
@endsection
