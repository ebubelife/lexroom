@extends('admin.layouts.app')

@section('title', 'Evidence Files')
@section('page-title', 'Evidence Files')

@section('content')
<div x-data="{ deleteId: null, deleteModal: false }" class="space-y-4">

    {{-- Storage overview --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider mb-1" style="color: var(--text-secondary);">Total Files</p>
                    <p class="text-2xl font-semibold">{{ number_format($storage['total_files']) }}</p>
                </div>
                <div class="p-2 rounded-lg" style="background: rgba(168,85,247,0.12);">
                    <svg class="w-5 h-5" style="color: #C084FC;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider mb-1" style="color: var(--text-secondary);">Storage Used</p>
                    <p class="text-2xl font-semibold">
                        @php
                            $bytes = $storage['total_size'];
                            if ($bytes >= 1073741824)  echo number_format($bytes / 1073741824, 2) . ' GB';
                            elseif ($bytes >= 1048576) echo number_format($bytes / 1048576, 2) . ' MB';
                            elseif ($bytes >= 1024)    echo number_format($bytes / 1024, 2) . ' KB';
                            else                       echo $bytes . ' B';
                        @endphp
                    </p>
                </div>
                <div class="p-2 rounded-lg" style="background: rgba(59,130,246,0.12);">
                    <svg class="w-5 h-5" style="color: #60A5FA;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <p class="text-xs font-medium uppercase tracking-wider mb-2" style="color: var(--text-secondary);">By File Type</p>
            <div class="space-y-1.5">
                @foreach($storage['by_type']->take(4) as $type)
                    @php
                        $ext = explode('/', $type->mime_type)[1] ?? $type->mime_type;
                        $pct = $storage['total_files'] > 0 ? round(($type->count / $storage['total_files']) * 100) : 0;
                    @endphp
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-mono w-12 flex-shrink-0" style="color: var(--gold);">{{ strtoupper($ext) }}</span>
                        <div class="flex-1 h-1.5 rounded-full" style="background: var(--border-color);">
                            <div class="h-1.5 rounded-full" style="width: {{ $pct }}%; background: var(--gold);"></div>
                        </div>
                        <span class="text-xs w-6 text-right flex-shrink-0" style="color: var(--text-secondary);">{{ $type->count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Toolbar --}}
    <form method="GET" action="{{ route('admin.files.index') }}" class="flex flex-col sm:flex-row gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search filename or case ID…"
               class="flex-1 px-3 py-2 rounded-lg text-sm outline-none"
               style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">

        <select name="party" class="px-3 py-2 rounded-lg text-sm outline-none"
                style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">
            <option value="">All parties</option>
            <option value="party_a" {{ request('party') === 'party_a' ? 'selected' : '' }}>Party A</option>
            <option value="party_b" {{ request('party') === 'party_b' ? 'selected' : '' }}>Party B</option>
        </select>

        <select name="type" class="px-3 py-2 rounded-lg text-sm outline-none"
                style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">
            <option value="">All types</option>
            <option value="pdf"   {{ request('type') === 'pdf'   ? 'selected' : '' }}>PDF</option>
            <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>Images</option>
            <option value="word"  {{ request('type') === 'word'  ? 'selected' : '' }}>Word Docs</option>
            <option value="video" {{ request('type') === 'video' ? 'selected' : '' }}>Video</option>
            <option value="text"  {{ request('type') === 'text'  ? 'selected' : '' }}>Text</option>
        </select>

        <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium flex-shrink-0"
                style="background: var(--gold); color: #0D1B2A;">Filter</button>

        @if(request()->hasAny(['search', 'party', 'type']))
            <a href="{{ route('admin.files.index') }}"
               class="px-4 py-2 rounded-lg text-sm font-medium flex-shrink-0 text-center"
               style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-secondary);">Clear</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="rounded-xl overflow-hidden" style="background: var(--bg-card); border: 1px solid var(--border-color);">
        <div class="px-4 py-3" style="border-bottom: 1px solid var(--border-color);">
            <p class="text-sm" style="color: var(--text-secondary);">
                {{ number_format($files->total()) }} file{{ $files->total() !== 1 ? 's' : '' }}
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full data-table">
                <thead>
                    <tr>
                        <th>File</th>
                        <th>Room</th>
                        <th>Party</th>
                        <th>Type</th>
                        <th>Size</th>
                        <th>Locked</th>
                        <th>Uploaded</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($files as $file)
                        <tr>
                            <td>
                                <div class="flex items-center gap-2.5">
                                    @php
                                        $ext = strtolower(pathinfo($file->original_filename, PATHINFO_EXTENSION));
                                        $ic = [
                                            'pdf'  => ['rgba(220,38,38,0.12)',  '#F87171', 'PDF'],
                                            'doc'  => ['rgba(37,99,235,0.12)',  '#60A5FA', 'DOC'],
                                            'docx' => ['rgba(37,99,235,0.12)',  '#60A5FA', 'DOC'],
                                            'png'  => ['rgba(5,150,105,0.12)',  '#34D399', 'IMG'],
                                            'jpg'  => ['rgba(5,150,105,0.12)',  '#34D399', 'IMG'],
                                            'jpeg' => ['rgba(5,150,105,0.12)',  '#34D399', 'IMG'],
                                            'mp4'  => ['rgba(124,58,237,0.12)', '#A78BFA', 'VID'],
                                            'txt'  => ['rgba(107,114,128,0.12)','#9CA3AF', 'TXT'],
                                            'csv'  => ['rgba(107,114,128,0.12)','#9CA3AF', 'CSV'],
                                        ][$ext] ?? ['rgba(107,114,128,0.12)', '#9CA3AF', strtoupper($ext) ?: 'FILE'];
                                    @endphp
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0"
                                         style="background: {{ $ic[0] }}; color: {{ $ic[1] }};">
                                        {{ $ic[2] }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium truncate max-w-[180px]">{{ $file->original_filename }}</p>
                                        <p class="text-xs font-mono truncate max-w-[180px]" style="color: var(--text-secondary);">{{ $file->filename }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($file->room)
                                    <a href="{{ route('admin.rooms.show', $file->room) }}"
                                       class="font-mono text-xs hover:underline" style="color: var(--gold);">
                                        {{ $file->room->case_id }}
                                    </a>
                                @else
                                    <span class="text-xs" style="color: var(--text-secondary);">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge"
                                      style="background: {{ $file->party === 'party_a' ? 'rgba(59,130,246,0.12)' : 'rgba(168,85,247,0.12)' }};
                                             color: {{ $file->party === 'party_a' ? '#60A5FA' : '#C084FC' }};">
                                    {{ $file->party_label }}
                                </span>
                            </td>
                            <td class="text-xs" style="color: var(--text-secondary);">{{ $file->mime_type }}</td>
                            <td class="text-xs" style="color: var(--text-secondary);">{{ $file->formatted_size }}</td>
                            <td>
                                @if($file->is_locked)
                                    <span class="badge" style="background: rgba(251,191,36,0.12); color: #FCD34D;">🔒 Yes</span>
                                @else
                                    <span class="badge" style="background: rgba(107,114,128,0.1); color: #9CA3AF;">No</span>
                                @endif
                            </td>
                            <td class="text-xs" style="color: var(--text-secondary);">
                                {{ $file->created_at->format('d M Y') }}<br>{{ $file->created_at->format('H:i') }}
                            </td>
                            <td>
                                <div class="flex items-center gap-1.5">
                                    <a href="{{ route('admin.files.download', $file) }}"
                                       class="p-1.5 rounded-lg hover:opacity-80"
                                       style="background: rgba(74,222,128,0.1); color: #4ADE80;" title="Download">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                    </a>
                                    <button @click="deleteId = {{ $file->id }}; deleteModal = true"
                                            class="p-1.5 rounded-lg hover:opacity-80"
                                            style="background: rgba(239,68,68,0.1); color: #F87171;" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12" style="color: var(--text-secondary);">No files found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($files->hasPages())
            <div class="px-4 py-3 flex items-center justify-between" style="border-top: 1px solid var(--border-color);">
                <p class="text-xs" style="color: var(--text-secondary);">
                    Showing {{ $files->firstItem() }}–{{ $files->lastItem() }} of {{ $files->total() }}
                </p>
                <div class="flex gap-1">
                    @if($files->onFirstPage())
                        <span class="px-3 py-1.5 rounded-lg text-xs opacity-30" style="background: var(--bg-card); border: 1px solid var(--border-color);">← Prev</span>
                    @else
                        <a href="{{ $files->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg text-xs hover:opacity-80" style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">← Prev</a>
                    @endif
                    @if($files->hasMorePages())
                        <a href="{{ $files->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg text-xs hover:opacity-80" style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">Next →</a>
                    @else
                        <span class="px-3 py-1.5 rounded-lg text-xs opacity-30" style="background: var(--bg-card); border: 1px solid var(--border-color);">Next →</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Delete Modal --}}
    <div x-show="deleteModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.75);">
        <div class="w-full max-w-sm rounded-xl p-5 space-y-4"
             style="background: var(--bg-card); border: 1px solid rgba(239,68,68,0.3);" @click.stop>
            <h3 class="text-base font-semibold" style="color: #F87171;">Delete File?</h3>
            <p class="text-sm" style="color: var(--text-secondary);">
                This permanently deletes the file from disk and removes its record. Cannot be undone.
            </p>
            @foreach($files as $file)
                <form x-show="deleteId === {{ $file->id }}"
                      method="POST" action="{{ route('admin.files.destroy', $file) }}">
                    @csrf
                    @method('DELETE')
                    <div class="flex gap-2">
                        <button type="button" @click="deleteModal = false; deleteId = null"
                                class="flex-1 py-2 rounded-lg text-sm font-medium"
                                style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-secondary);">
                            Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 py-2 rounded-lg text-sm font-medium hover:opacity-80"
                                style="background: rgba(239,68,68,0.15); color: #F87171; border: 1px solid rgba(239,68,68,0.3);">
                            Delete
                        </button>
                    </div>
                </form>
            @endforeach
        </div>
    </div>

</div>
@endsection
