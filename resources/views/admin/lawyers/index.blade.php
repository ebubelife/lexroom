@extends('admin.layouts.app')
@section('title', 'Lawyers')
@section('page-title', 'Lawyers')

@section('content')
<div class="space-y-4">

    {{-- Toolbar --}}
    <div class="flex flex-wrap items-center gap-3">
        <form method="GET" action="{{ route('admin.lawyers.index') }}" class="flex flex-wrap gap-3 flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email..."
                   class="flex-1 min-w-[180px] px-3 py-2 rounded-lg text-sm outline-none"
                   style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">
            <select name="jurisdiction" class="px-3 py-2 rounded-lg text-sm outline-none"
                    style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">
                <option value="">All Jurisdictions</option>
                @foreach($jurisdictions as $j)
                    <option value="{{ $j }}" {{ request('jurisdiction') === $j ? 'selected' : '' }}>{{ $j }}</option>
                @endforeach
            </select>
            <select name="speciality" class="px-3 py-2 rounded-lg text-sm outline-none"
                    style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">
                <option value="">All Specialities</option>
                @foreach($specialities as $s)
                    <option value="{{ $s }}" {{ request('speciality') === $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium"
                    style="background: var(--gold); color: #0D1B2A;">Filter</button>
            @if(request()->hasAny(['search','jurisdiction','speciality']))
                <a href="{{ route('admin.lawyers.index') }}" class="px-4 py-2 rounded-lg text-sm"
                   style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-secondary);">Clear</a>
            @endif
        </form>
        <a href="{{ route('admin.lawyers.create') }}"
           class="px-4 py-2 rounded-lg text-sm font-medium flex-shrink-0"
           style="background: var(--gold); color: #0D1B2A;">+ Add Lawyer</a>
    </div>

    {{-- Table --}}
    <div class="rounded-xl overflow-hidden" style="background: var(--bg-card); border: 1px solid var(--border-color);">
        <div class="px-4 py-3" style="border-bottom: 1px solid var(--border-color);">
            <p class="text-sm" style="color: var(--text-secondary);">{{ $lawyers->total() }} lawyer{{ $lawyers->total() !== 1 ? 's' : '' }}</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Jurisdiction</th>
                        <th>Speciality</th>
                        <th>Experience</th>
                        <th>Commission</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lawyers as $lawyer)
                    <tr>
                        <td>
                            <p class="text-sm font-medium">{{ $lawyer->name }}</p>
                            <p class="text-xs" style="color: var(--text-secondary);">{{ $lawyer->email }}</p>
                        </td>
                        <td class="text-sm">{{ $lawyer->jurisdiction }}</td>
                        <td class="text-sm">{{ $lawyer->speciality }}</td>
                        <td class="text-sm">{{ $lawyer->years_experience }} yrs</td>
                        <td class="text-sm">{{ $lawyer->commission_rate }}%</td>
                        <td>
                            <div class="flex gap-1">
                                @if($lawyer->verified)
                                    <span class="badge" style="background: rgba(21,128,61,0.1); color: #15803D;">✓ Verified</span>
                                @endif
                                <span class="badge" style="background: {{ $lawyer->active ? 'rgba(21,128,61,0.1)' : 'rgba(220,38,38,0.1)' }}; color: {{ $lawyer->active ? '#15803D' : '#DC2626' }};">
                                    {{ $lawyer->active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="flex gap-2">
                                <a href="{{ route('admin.lawyers.edit', $lawyer) }}"
                                   class="px-3 py-1 rounded text-xs font-medium"
                                   style="background: rgba(201,168,76,0.1); color: var(--gold);">Edit</a>
                                <form method="POST" action="{{ route('admin.lawyers.toggle', $lawyer) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 rounded text-xs font-medium"
                                            style="background: {{ $lawyer->active ? 'rgba(220,38,38,0.1)' : 'rgba(21,128,61,0.1)' }}; color: {{ $lawyer->active ? '#DC2626' : '#15803D' }};">
                                        {{ $lawyer->active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-12" style="color: var(--text-secondary);">No lawyers yet. <a href="{{ route('admin.lawyers.create') }}" style="color: var(--gold);">Add one →</a></td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($lawyers->hasPages())
            <div class="px-4 py-3" style="border-top: 1px solid var(--border-color);">
                {{ $lawyers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
