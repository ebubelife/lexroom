@extends('admin.layouts.app')

@section('title', 'Edit Admin')
@section('page-title', 'Edit Admin')

@section('content')
<div class="max-w-xl mx-auto px-4 py-8">

    <div class="mb-8">
        <h1 class="text-2xl font-serif" style="color: var(--gold);">Edit Admin</h1>
        <p class="mt-1 text-sm" style="color: var(--text-secondary);">Update account details or role for {{ $adminUser->name }}.</p>
    </div>

    @if($errors->any())
        <div class="mb-6 px-4 py-3 rounded-lg text-sm" style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #f87171;">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.admin-users.update', $adminUser) }}">
        @csrf
        @method('PUT')

        <div class="rounded-xl p-6 space-y-5" style="background: var(--bg-card); border: 1px solid var(--border-color);">

            <div>
                <label class="block text-xs font-semibold uppercase tracking-widest mb-1.5" style="color: var(--text-secondary);">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $adminUser->name) }}" required
                       class="w-full px-4 py-2.5 rounded-lg text-sm"
                       style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary); outline: none;">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-widest mb-1.5" style="color: var(--text-secondary);">Email Address</label>
                <input type="email" name="email" value="{{ old('email', $adminUser->email) }}" required
                       class="w-full px-4 py-2.5 rounded-lg text-sm"
                       style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary); outline: none;">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-widest mb-1.5" style="color: var(--text-secondary);">Role</label>
                <select name="role" required
                        class="w-full px-4 py-2.5 rounded-lg text-sm"
                        style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary); outline: none;">
                    @foreach($roles as $role)
                        <option value="{{ $role }}" {{ old('role', $adminUser->role) === $role ? 'selected' : '' }}>
                            {{ \App\Support\AdminPermissions::ROLE_LABELS[$role] ?? ucfirst(str_replace('_', ' ', $role)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="pt-2" style="border-top: 1px solid var(--border-color);">
                <p class="text-xs mb-3" style="color: var(--text-secondary);">Leave password blank to keep the current password.</p>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-widest mb-1.5" style="color: var(--text-secondary);">New Password</label>
                        <input type="password" name="password" minlength="8"
                               class="w-full px-4 py-2.5 rounded-lg text-sm"
                               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary); outline: none;">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-widest mb-1.5" style="color: var(--text-secondary);">Confirm New Password</label>
                        <input type="password" name="password_confirmation"
                               class="w-full px-4 py-2.5 rounded-lg text-sm"
                               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary); outline: none;">
                    </div>
                </div>
            </div>

        </div>

        <div class="flex items-center justify-between mt-6">
            <a href="{{ route('admin.admin-users.index') }}"
               class="text-sm" style="color: var(--text-secondary);">← Back</a>
            <button type="submit"
                    class="px-5 py-2.5 rounded-lg text-sm font-semibold transition-all hover:opacity-90"
                    style="background: var(--gold); color: var(--navy);">
                Save Changes
            </button>
        </div>

    </form>
</div>
@endsection
