@extends('admin.layouts.app')
@section('title', 'Newsletter')
@section('page-title', 'Newsletter Subscribers')

@section('content')
<div class="space-y-4">

    {{-- Toolbar --}}
    <div class="flex flex-wrap items-center gap-3">
        <form method="GET" action="{{ route('admin.newsletter.index') }}" class="flex flex-wrap gap-3 flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search email..."
                   class="flex-1 min-w-[180px] px-3 py-2 rounded-lg text-sm outline-none"
                   style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">
            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium"
                    style="background: var(--gold); color: #0D1B2A;">Search</button>
            @if(request('search'))
                <a href="{{ route('admin.newsletter.index') }}" class="px-4 py-2 rounded-lg text-sm"
                   style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-secondary);">Clear</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="rounded-xl overflow-hidden" style="background: var(--bg-card); border: 1px solid var(--border-color);">
        <div class="px-4 py-3" style="border-bottom: 1px solid var(--border-color);">
            <p class="text-sm" style="color: var(--text-secondary);">
                {{ number_format($subscribers->total()) }} subscriber{{ $subscribers->total() !== 1 ? 's' : '' }}
            </p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full data-table">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Source</th>
                        <th>Subscribed</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscribers as $subscriber)
                    <tr>
                        <td class="text-sm font-medium">{{ $subscriber->email }}</td>
                        <td class="text-sm" style="color: var(--text-secondary);">{{ ucfirst($subscriber->source) }}</td>
                        <td class="text-xs" style="color: var(--text-secondary);">
                            <span title="{{ $subscriber->created_at->format('d M Y H:i') }}">
                                {{ $subscriber->created_at->diffForHumans() }}
                            </span>
                        </td>
                        <td>
                            @if(auth('admin')->user()->hasAbility('admin.newsletter.manage'))
                            <form method="POST" action="{{ route('admin.newsletter.destroy', $subscriber) }}" onsubmit="return confirm('Remove {{ $subscriber->email }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-3 py-1 rounded text-xs font-medium"
                                        style="background: rgba(220,38,38,0.1); color: #DC2626;">Remove</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-12" style="color: var(--text-secondary);">No newsletter signups yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($subscribers->hasPages())
            <div class="px-4 py-3" style="border-top: 1px solid var(--border-color);">
                {{ $subscribers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
