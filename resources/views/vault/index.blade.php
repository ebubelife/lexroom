@extends('layouts.app')

@section('title', 'Evidence Vault — First Mediator')
@section('page-title', 'Vaults')

@section('content')
<div class="max-w-7xl mx-auto animate-fade-up">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-serif" style="color: var(--text-primary);">Evidence Vault</h1>
            <p class="text-sm mt-0.5" style="color: var(--text-secondary);">All files uploaded across your cases</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-3 py-1.5 rounded-full text-xs font-medium" style="background-color: rgba(201,168,76,0.15); color: var(--gold);">
                {{ $totalFiles }} {{ Str::plural('file', $totalFiles) }}
            </span>
            <span class="px-3 py-1.5 rounded-full text-xs font-medium" style="background-color: var(--bg-secondary); color: var(--text-secondary);">
                @php
                    $mb = $totalSize / 1048576;
                    echo $mb >= 1024 ? number_format($mb / 1024, 1) . ' GB' : number_format($mb, 1) . ' MB';
                @endphp total
            </span>
        </div>
    </div>

    {{-- Search + Filters --}}
    <form method="GET" action="{{ route('vault.index') }}" class="flex flex-col sm:flex-row gap-3 mb-6">
        {{-- Search --}}
        <div class="relative flex-1">
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2" style="color: var(--text-secondary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Search files by name…"
                class="w-full pl-9 pr-4 py-2.5 rounded-lg text-sm border focus:outline-none focus:ring-2"
                style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
            >
        </div>

        {{-- File type filter --}}
        <select name="type" onchange="this.form.submit()" class="px-3 py-2.5 rounded-lg text-sm border focus:outline-none" style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
            <option value="">All Types</option>
            <option value="pdf"   {{ request('type') === 'pdf'   ? 'selected' : '' }}>PDF</option>
            <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>Images</option>
            <option value="doc"   {{ request('type') === 'doc'   ? 'selected' : '' }}>Documents</option>
            <option value="video" {{ request('type') === 'video' ? 'selected' : '' }}>Videos</option>
        </select>

        {{-- Case filter --}}
        <select name="room_id" onchange="this.form.submit()" class="px-3 py-2.5 rounded-lg text-sm border focus:outline-none" style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
            <option value="">All Cases</option>
            @foreach($rooms as $room)
            <option value="{{ $room->id }}" {{ request('room_id') == $room->id ? 'selected' : '' }}>
                {{ Str::limit($room->case_summary ?? ucfirst($room->category) . ' Dispute', 40) }}
            </option>
            @endforeach
        </select>

        @if(request()->hasAny(['q', 'type', 'room_id']))
        <a href="{{ route('vault.index') }}" class="px-3 py-2.5 rounded-lg text-sm flex items-center gap-1.5" style="border: 1px solid var(--border-color); color: var(--text-secondary);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            Clear
        </a>
        @endif
    </form>

    {{-- Files Grid --}}
    @if($files->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-6">
        @foreach($files as $file)
        @php
            $ext = strtolower(pathinfo($file->original_filename, PATHINFO_EXTENSION));
            $iconColors = [
                'pdf'  => ['bg' => 'rgba(220,38,38,0.1)',  'text' => '#DC2626', 'label' => 'PDF'],
                'doc'  => ['bg' => 'rgba(37,99,235,0.1)',  'text' => '#2563EB', 'label' => 'DOC'],
                'docx' => ['bg' => 'rgba(37,99,235,0.1)',  'text' => '#2563EB', 'label' => 'DOCX'],
                'png'  => ['bg' => 'rgba(5,150,105,0.1)',  'text' => '#059669', 'label' => 'IMG'],
                'jpg'  => ['bg' => 'rgba(5,150,105,0.1)',  'text' => '#059669', 'label' => 'IMG'],
                'jpeg' => ['bg' => 'rgba(5,150,105,0.1)',  'text' => '#059669', 'label' => 'IMG'],
                'mp4'  => ['bg' => 'rgba(124,58,237,0.1)', 'text' => '#7C3AED', 'label' => 'VID'],
            ];
            $ic = $iconColors[$ext] ?? ['bg' => 'rgba(107,107,104,0.12)', 'text' => '#6B6B68', 'label' => strtoupper($ext)];
        @endphp
        <div class="flex flex-col rounded-2xl overflow-hidden hover-lift" style="background-color: var(--bg-secondary); border: 1px solid var(--border-color);">
            {{-- File type header --}}
            <div class="flex items-center justify-center h-24" style="background-color: {{ $ic['bg'] }};">
                <div class="text-center">
                    <span class="block text-xl font-bold" style="color: {{ $ic['text'] }};">{{ $ic['label'] }}</span>
                    <svg class="w-6 h-6 mx-auto mt-1" style="color: {{ $ic['text'] }}; opacity: .6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>

            <div class="p-4 flex flex-col flex-1">
                {{-- Filename --}}
                <p class="text-sm font-medium mb-1 truncate" title="{{ $file->original_filename }}" style="color: var(--text-primary);">
                    {{ $file->original_filename }}
                </p>

                {{-- Meta --}}
                <p class="text-xs mb-1" style="color: var(--text-secondary);">
                    {{ $file->formatted_size }} · {{ $file->created_at->format('M j, Y') }}
                </p>

                {{-- Case link --}}
                <a href="{{ route('rooms.show', $file->room->uuid) }}" class="text-xs truncate hover:underline mb-3" style="color: var(--gold);" title="{{ $file->room->case_summary }}">
                    <svg class="w-3 h-3 inline mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M10.5 3L12 2l1.5 1H21v6H3V3h7.5z"/></svg>
                    {{ Str::limit($file->room->case_summary ?? ucfirst($file->room->category) . ' Dispute', 36) }}
                </a>

                {{-- Party badge --}}
                <span class="inline-block mb-3 px-2 py-0.5 rounded-full text-xs w-fit" style="background-color: var(--bg-primary); color: var(--text-secondary);">
                    Uploaded by {{ $file->party_label }}
                </span>

                {{-- Download button --}}
                <a href="{{ route('vault.download', $file->id) }}"
                   class="mt-auto flex items-center justify-center gap-2 w-full py-2 rounded-lg text-xs font-medium transition-opacity hover:opacity-85"
                   style="border: 1px solid var(--border-color); color: var(--text-primary);">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download
                </a>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    {{ $files->appends(request()->query())->links() }}

    @else
    {{-- Empty state --}}
    <div class="flex flex-col items-center justify-center py-20 rounded-2xl" style="background-color: var(--bg-secondary); border: 1px dashed var(--border-color);">
        <svg class="w-16 h-16 mb-5" style="color: var(--gold); opacity: .4;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/>
        </svg>
        <p class="text-base font-semibold mb-1" style="color: var(--text-primary);">No files found</p>
        <p class="text-sm text-center max-w-xs" style="color: var(--text-secondary);">
            @if(request()->hasAny(['q', 'type', 'room_id']))
            No files match your filters. <a href="{{ route('vault.index') }}" style="color: var(--gold);">Clear filters</a>
            @else
            Upload evidence files from inside a room and they'll appear here.
            @endif
        </p>
    </div>
    @endif

</div>
@endsection
