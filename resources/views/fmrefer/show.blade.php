@extends('layouts.app')
@section('title', $lawyer->name . ' — FM Refer')
@section('page-title', 'FM Refer')

@section('content')
<div class="max-w-2xl mx-auto">

    <a href="{{ route('fmrefer.index') }}" class="inline-flex items-center gap-2 text-sm mb-5 hover:opacity-80"
       style="color: var(--text-secondary);">
        ← Back to directory
    </a>

    {{-- Lawyer Card --}}
    <div class="rounded-xl border p-6 mb-5" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
        <div class="flex items-start gap-4 mb-4">
            <div class="w-16 h-16 rounded-full flex items-center justify-center text-white font-bold text-2xl flex-shrink-0"
                 style="background-color: #0D1B2A;">
                {{ strtoupper(substr($lawyer->name, 0, 1)) }}
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="font-serif text-xl" style="color: var(--text-primary);">{{ $lawyer->name }}</h1>
                    @if($lawyer->verified)
                        <span class="text-xs px-2 py-0.5 rounded-full" style="background-color: rgba(21,128,61,0.1); color: #15803D;">✓ Verified</span>
                    @endif
                </div>
                <p class="text-sm mt-1" style="color: var(--text-secondary);">{{ $lawyer->speciality }} · {{ $lawyer->jurisdiction }}</p>
                <p class="text-sm" style="color: var(--text-secondary);">{{ $lawyer->years_experience }} years experience</p>
            </div>
        </div>

        @if($lawyer->bio)
            <p class="text-sm mb-4" style="color: var(--text-secondary);">{{ $lawyer->bio }}</p>
        @endif

        <div class="grid grid-cols-2 gap-3 text-sm">
            <div class="p-3 rounded-lg" style="background-color: var(--bg-primary);">
                <p class="text-xs mb-1" style="color: var(--text-secondary);">Bar Number</p>
                <p style="color: var(--text-primary);">{{ $lawyer->bar_number ?? 'N/A' }}</p>
            </div>
            <div class="p-3 rounded-lg" style="background-color: var(--bg-primary);">
                <p class="text-xs mb-1" style="color: var(--text-secondary);">Response Time</p>
                <p style="color: var(--text-primary);">48–72 hours</p>
            </div>
        </div>
    </div>

    {{-- Success/Error messages --}}
    @if(session('success'))
        <div class="p-4 rounded-lg mb-5 text-sm" style="background-color: rgba(21,128,61,0.1); color: #15803D;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-lg mb-5 text-sm" style="background-color: rgba(220,38,38,0.1); color: #DC2626;">
            {{ session('error') }}
        </div>
    @endif

    {{-- Escalation Form --}}
    <div class="rounded-xl border p-6" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
        <h2 class="font-serif text-lg mb-1" style="color: var(--text-primary);">Escalate a Case</h2>
        <p class="text-sm mb-5" style="color: var(--text-secondary);">
            Select a completed session and describe what you need. {{ $lawyer->name }} will review and contact you directly.
        </p>

        @if($rooms->isEmpty())
            <div class="text-center py-8 rounded-lg" style="background-color: var(--bg-primary);">
                <p class="text-sm" style="color: var(--text-secondary);">You need a completed mediation session to escalate a case.</p>
                <a href="{{ route('rooms.create') }}" class="inline-block mt-3 px-4 py-2 rounded-lg text-sm font-medium"
                   style="background-color: var(--gold); color: #0D1B2A;">Create a Session</a>
            </div>
        @else
            <form method="POST" action="{{ route('escalation.store', $lawyer) }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Select Session *</label>
                    <select name="room_id" required class="w-full px-4 py-3 rounded-lg border text-sm outline-none"
                            style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);">
                        <option value="">Choose a completed session...</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                {{ $room->title ?? ucfirst($room->category) . ' Dispute' }} — {{ $room->created_at->format('M d, Y') }}
                            </option>
                        @endforeach
                    </select>
                    @error('room_id') <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p> @enderror
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Your Message *</label>
                    <textarea name="message" rows="5" required minlength="20" maxlength="1000"
                              placeholder="Briefly describe your situation and what legal assistance you need..."
                              class="w-full px-4 py-3 rounded-lg border text-sm outline-none resize-none"
                              style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);">{{ old('message') }}</textarea>
                    @error('message') <p class="text-xs mt-1" style="color: #DC2626;">{{ $message }}</p> @enderror
                </div>

                <div class="p-3 rounded-lg mb-5 text-xs" style="background-color: rgba(201,168,76,0.08); color: var(--text-secondary);">
                    By submitting, you agree that FirstMediator may share your session transcript and report with {{ $lawyer->name }} to facilitate this referral.
                </div>

                <button type="submit" class="w-full py-3 rounded-lg font-medium hover:opacity-90"
                        style="background-color: var(--gold); color: #0D1B2A;">
                    Send to {{ $lawyer->name }}
                </button>
            </form>
        @endif
    </div>
</div>
@endsection
