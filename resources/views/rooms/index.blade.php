@extends('layouts.app')

@section('title', 'Cases — First Mediator')
@section('page-title', 'Cases')

@section('content')
<div class="max-w-7xl mx-auto animate-fade-up">

    {{-- Header Row --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-serif" style="color: var(--text-primary);">All Cases</h1>
            <p class="text-sm mt-0.5" style="color: var(--text-secondary);">Every dispute you've opened or been invited to</p>
        </div>
        <a href="{{ route('rooms.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition-opacity" style="background-color: var(--gold); color: var(--white);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Case
        </a>
    </div>

    {{-- Search + Filter Bar --}}
    <form method="GET" action="{{ route('rooms.index') }}" class="flex flex-col sm:flex-row gap-3 mb-6">
        <div class="relative flex-1">
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2" style="color: var(--text-secondary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Search cases by summary or category…"
                class="w-full pl-9 pr-4 py-2.5 rounded-lg text-sm border focus:outline-none focus:ring-2"
                style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary); --tw-ring-color: var(--gold);"
            >
        </div>
        <select name="status" onchange="this.form.submit()" class="px-3 py-2.5 rounded-lg text-sm border focus:outline-none" style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
            <option value="">All Statuses</option>
            <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
            <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>Active</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
        </select>
    </form>

    {{-- Quick-stat pills --}}
    <div class="flex flex-wrap gap-2 mb-8">
        @php
            $totalMy     = $myRooms->total();
            $totalInvite = $invitedRooms->total();
        @endphp
        <span class="px-3 py-1 rounded-full text-xs font-medium" style="background-color: rgba(201,168,76,0.15); color: var(--gold);">
            {{ $totalMy }} created by me
        </span>
        <span class="px-3 py-1 rounded-full text-xs font-medium" style="background-color: rgba(107,107,104,0.12); color: var(--text-secondary);">
            {{ $totalInvite }} invitations received
        </span>
    </div>

    {{-- Tabs Navigation --}}
    <div class="flex overflow-x-auto border-b mb-6 no-scrollbar" style="border-color: var(--border-color);">
        <button onclick="switchTab('my-cases')" id="tab-btn-my-cases" class="whitespace-nowrap flex items-center gap-2 px-5 py-3 text-sm font-medium border-b-2 transition-colors" style="color: var(--gold); border-color: var(--gold);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Cases I Created
        </button>
        <button onclick="switchTab('invited-cases')" id="tab-btn-invited-cases" class="whitespace-nowrap flex items-center gap-2 px-5 py-3 text-sm font-medium border-b-2 border-transparent transition-colors" style="color: var(--text-secondary);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Cases I Was Invited To
        </button>
    </div>

    {{-- ====== MY CASES ====== --}}
    <section id="tab-my-cases" class="tab-content block pb-10">
        @if($myRooms->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mb-4">
            @foreach($myRooms as $room)
            @include('rooms._case_card', ['room' => $room, 'role' => 'creator'])
            @endforeach
        </div>
        {{ $myRooms->appends(request()->query())->links() }}
        @else
        <div class="flex flex-col items-center justify-center py-14 rounded-2xl" style="background-color: var(--bg-secondary); border: 1px dashed var(--border-color);">
            <svg class="w-12 h-12 mb-4" style="color: var(--gold); opacity: .5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
            </svg>
            <p class="text-sm font-medium mb-1" style="color: var(--text-primary);">No cases yet</p>
            <p class="text-xs mb-4" style="color: var(--text-secondary);">Start your first dispute session now</p>
            <a href="{{ route('rooms.create') }}" class="px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition-opacity" style="background-color: var(--gold); color: var(--white);">
                Create a Case
            </a>
        </div>
        @endif
    </section>

    {{-- ====== INVITED CASES ====== --}}
    <section id="tab-invited-cases" class="tab-content hidden pb-10">
        @if($invitedRooms->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mb-4">
            @foreach($invitedRooms as $room)
            @include('rooms._case_card', ['room' => $room, 'role' => 'invited'])
            @endforeach
        </div>
        {{ $invitedRooms->appends(request()->query())->links() }}
        @else
        <div class="flex flex-col items-center justify-center py-14 rounded-2xl" style="background-color: var(--bg-secondary); border: 1px dashed var(--border-color);">
            <svg class="w-12 h-12 mb-4" style="color: #3B82F6; opacity: .4;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <p class="text-sm font-medium mb-1" style="color: var(--text-primary);">No invitations yet</p>
            <p class="text-xs" style="color: var(--text-secondary);">When someone adds you as party B you'll see it here</p>
        </div>
        @endif
    </section>

    <script>
        function switchTab(tabId) {
            // Hide all tab content
            document.querySelectorAll('.tab-content').forEach(el => {
                el.classList.add('hidden');
                el.classList.remove('block');
            });
            // Show targeted tab
            document.getElementById('tab-' + tabId).classList.remove('hidden');
            document.getElementById('tab-' + tabId).classList.add('block');

            // Reset buttons
            document.querySelectorAll('[id^=tab-btn-]').forEach(btn => {
                btn.style.color = 'var(--text-secondary)';
                btn.style.borderColor = 'transparent';
            });

            // Activate button
            const activeColor = tabId === 'my-cases' ? 'var(--gold)' : '#3B82F6';
            document.getElementById('tab-btn-' + tabId).style.color = activeColor;
            document.getElementById('tab-btn-' + tabId).style.borderColor = activeColor;
        }

        // Init based on url query for pagination
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('invited_rooms')) {
                switchTab('invited-cases');
            } else {
                switchTab('my-cases');
            }
        });
    </script>

</div>
@endsection