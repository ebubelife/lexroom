@extends('layouts.app')

@section('title', 'Settings — LexRoom')
@section('page-title', 'Settings')

@section('content')
<div class="max-w-4xl mx-auto" x-data="{ activeTab: 'profile' }">
    <!-- Tabs -->
    <div class="flex space-x-1 mb-6" style="border-bottom: 1px solid var(--border-color);">
        <button 
            @click="activeTab = 'profile'"
            :class="activeTab === 'profile' ? 'border-b-2 font-medium' : ''"
            class="px-4 py-2 text-sm transition-colors"
            :style="activeTab === 'profile' ? 'border-color: var(--gold); color: var(--gold);' : 'color: var(--text-secondary);'">
            Profile
        </button>
        <button 
            @click="activeTab = 'security'"
            :class="activeTab === 'security' ? 'border-b-2 font-medium' : ''"
            class="px-4 py-2 text-sm transition-colors"
            :style="activeTab === 'security' ? 'border-color: var(--gold); color: var(--gold);' : 'color: var(--text-secondary);'">
            Security
        </button>
    </div>

    <!-- Profile Tab -->
    <div x-show="activeTab === 'profile'" x-transition>
        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" 
              class="rounded-xl shadow-sm border p-6"
              style="background-color: var(--bg-secondary); border-color: var(--border-color);"
              x-data="{ imagePreview: '{{ auth()->user()->profile_image_url }}' }">
            @csrf
            @method('PUT')

            <h2 class="text-xl font-serif mb-6" style="color: var(--text-primary);">Profile Information</h2>

            <!-- Profile Image -->
            <div class="mb-6">
                <label class="block text-sm font-medium mb-3" style="color: var(--text-primary);">Profile Image</label>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <template x-if="imagePreview">
                            <img :src="imagePreview" alt="Profile" class="w-24 h-24 rounded-full object-cover border-2" style="border-color: var(--gold);">
                        </template>
                        <template x-if="!imagePreview">
                            <div class="w-24 h-24 rounded-full flex items-center justify-center text-white text-2xl font-bold" style="background-color: var(--gold);">
                                {{ auth()->user()->initials }}
                            </div>
                        </template>
                    </div>
                    <div>
                        <input type="file" name="profile_image" id="profile_image" class="hidden" accept="image/*"
                               @change="imagePreview = URL.createObjectURL($event.target.files[0])">
                        <button type="button" onclick="document.getElementById('profile_image').click()"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors hover:opacity-90"
                                style="background-color: var(--gold); color: var(--white);">
                            Change Photo
                        </button>
                        <p class="text-xs mt-2" style="color: var(--text-secondary);">JPG, PNG or GIF. Max 2MB</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">First Name</label>
                    <input type="text" name="first_name" value="{{ old('first_name', auth()->user()->first_name) }}" required
                           class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold"
                           style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name', auth()->user()->last_name) }}" required
                           class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold"
                           style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Email</label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                       class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold"
                       style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}" required
                       class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold"
                       style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">BVN (Optional)</label>
                    <input type="text" name="bvn" value="{{ old('bvn', auth()->user()->bvn) }}" maxlength="11"
                           class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold"
                           style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">NIN (Optional)</label>
                    <input type="text" name="nin" value="{{ old('nin', auth()->user()->nin) }}" maxlength="11"
                           class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold"
                           style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
                </div>
            </div>

            <button type="submit" class="px-6 py-3 rounded-lg text-white font-medium transition-colors hover:opacity-90"
                    style="background-color: var(--gold);">
                Save Changes
            </button>
        </form>
    </div>

    <!-- Security Tab -->
    <div x-show="activeTab === 'security'" x-transition>
        <form action="{{ route('settings.password') }}" method="POST" 
              class="rounded-xl shadow-sm border p-6"
              style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            @csrf
            @method('PUT')

            <h2 class="text-xl font-serif mb-6" style="color: var(--text-primary);">Change Password</h2>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Current Password</label>
                <input type="password" name="current_password" required
                       class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold"
                       style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">New Password</label>
                <input type="password" name="password" required
                       class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold"
                       style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Confirm New Password</label>
                <input type="password" name="password_confirmation" required
                       class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold"
                       style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
            </div>

            <button type="submit" class="px-6 py-3 rounded-lg text-white font-medium transition-colors hover:opacity-90"
                    style="background-color: var(--gold);">
                Update Password
            </button>
        </form>
    </div>

    @if(session('success'))
    <script>
        showToast('{{ session('success') }}', 'success');
    </script>
    @endif

    @if($errors->any())
    <script>
        showToast('{{ $errors->first() }}', 'error');
    </script>
    @endif
</div>
@endsection
