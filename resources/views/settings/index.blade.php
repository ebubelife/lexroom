@extends('layouts.app')

@section('title', 'Settings — First Mediator')
@section('page-title', 'Settings')

@section('content')
<div class="max-w-4xl mx-auto px-3 md:px-0" x-data="{ activeTab: 'profile' }">
    <!-- Tabs -->
    <div class="flex space-x-1 mb-6 overflow-x-auto" style="border-bottom: 1px solid var(--border-color);">
        <button 
            @click="activeTab = 'profile'"
            :class="activeTab === 'profile' ? 'border-b-2 font-medium' : ''"
            class="px-4 py-2 text-sm whitespace-nowrap transition-colors"
            :style="activeTab === 'profile' ? 'border-color: var(--gold); color: var(--gold);' : 'color: var(--text-secondary);'">
            Profile
        </button>
        <button 
            @click="activeTab = 'security'"
            :class="activeTab === 'security' ? 'border-b-2 font-medium' : ''"
            class="px-4 py-2 text-sm whitespace-nowrap transition-colors"
            :style="activeTab === 'security' ? 'border-color: var(--gold); color: var(--gold);' : 'color: var(--text-secondary);'">
            Security
        </button>
        <button 
            @click="activeTab = 'subscription'"
            :class="activeTab === 'subscription' ? 'border-b-2 font-medium' : ''"
            class="px-4 py-2 text-sm whitespace-nowrap transition-colors"
            :style="activeTab === 'subscription' ? 'border-color: var(--gold); color: var(--gold);' : 'color: var(--text-secondary);'">
            Subscription
        </button>
    </div>

    <!-- Profile Tab -->
    <div x-show="activeTab === 'profile'" x-transition>
        <div class="rounded-xl shadow-sm border p-4 md:p-6 mb-6"
             style="background-color: var(--bg-secondary); border-color: var(--border-color);"
             x-data="{ 
                imagePreview: '{{ auth()->user()->profile_image_url }}',
                isUploading: false,
                uploadProgress: 0,
                async uploadImage(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    // Show preview immediately
                    this.imagePreview = URL.createObjectURL(file);
                    this.isUploading = true;
                    this.uploadProgress = 0;

                    const formData = new FormData();
                    formData.append('profile_image', file);

                    try {
                        const response = await axios.post('{{ route('settings.avatar') }}', formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            },
                            onUploadProgress: (progressEvent) => {
                                this.uploadProgress = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                            }
                        });

                        if (response.data.success) {
                            showToast(response.data.message, 'success');
                            // Update all avatars on the page
                            const newUrl = response.data.url;
                            this.imagePreview = newUrl;
                            document.querySelectorAll('img[alt=\'{{ auth()->user()->name }}\']').forEach(img => img.src = newUrl);
                        }
                    } catch (error) {
                        showToast(error.response?.data?.message || 'oops! could not update', 'error');
                    } finally {
                        this.isUploading = false;
                        this.uploadProgress = 0;
                    }
                }
             }">
            
            <h2 class="text-lg md:text-xl font-serif mb-4 md:mb-6" style="color: var(--text-primary);">Profile Information</h2>

            <!-- Profile Image -->
            <div class="mb-4 md:mb-6">
                <label class="block text-sm font-medium mb-3" style="color: var(--text-primary);">Profile Image</label>
                <div class="flex flex-col sm:flex-row items-center sm:space-x-4 space-y-3 sm:space-y-0">
                    <div class="relative group shrink-0 aspect-square">
                        <template x-if="imagePreview">
                            <div class="relative w-20 h-20 md:w-24 md:h-24 rounded-full overflow-hidden">
                                <img :src="imagePreview" alt="Profile" class="w-full h-full object-cover border-2 rounded-full" :style="isUploading ? 'border-color: var(--gold); opacity: 0.5;' : 'border-color: var(--gold);'">
                                <!-- Progress Overlay -->
                                <div x-show="isUploading" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-30">
                                    <span class="text-white text-xs md:text-sm font-bold" x-text="uploadProgress + '%'"></span>
                                </div>
                            </div>
                        </template>
                        <template x-if="!imagePreview">
                            <div class="relative w-20 h-20 md:w-24 md:h-24 rounded-full overflow-hidden">
                                <div class="w-full h-full flex items-center justify-center text-white text-xl md:text-2xl font-bold leading-none" style="background-color: var(--gold);">
                                    {{ auth()->user()->initials }}
                                </div>
                                <!-- Progress Overlay -->
                                <div x-show="isUploading" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-30">
                                    <span class="text-white text-xs md:text-sm font-bold" x-text="uploadProgress + '%'"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="text-center sm:text-left">
                        <input type="file" name="profile_image" id="profile_image" class="hidden" accept="image/*"
                               @change="uploadImage($event)">
                        <button type="button" @click="document.getElementById('profile_image').click()"
                                :disabled="isUploading"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors hover:opacity-90 disabled:opacity-50"
                                style="background-color: var(--gold); color: var(--white);">
                            <span x-show="!isUploading">Change Photo</span>
                            <span x-show="isUploading">Uploading...</span>
                        </button>
                        <p class="text-xs mt-2" style="color: var(--text-secondary);">JPG, PNG or GIF. Max 2MB</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('settings.update') }}" method="POST">
                @csrf
                @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">First Name</label>
                    <input type="text" name="first_name" value="{{ old('first_name', auth()->user()->first_name) }}" required
                           class="w-full px-3 md:px-4 py-2 md:py-3 text-sm md:text-base rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold"
                           style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name', auth()->user()->last_name) }}" required
                           class="w-full px-3 md:px-4 py-2 md:py-3 text-sm md:text-base rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold"
                           style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Email</label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                       class="w-full px-3 md:px-4 py-2 md:py-3 text-sm md:text-base rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold"
                       style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
            </div>

            <div class="mb-4">
                <div class="flex justify-between items-end mb-2">
                    <label class="block text-sm font-medium" style="color: var(--text-primary);">Phone (Optional)</label>
                    @if(auth()->user()->phone && !auth()->user()->hasVerifiedPhone())
                        <a href="{{ route('verification.notice') }}" class="text-xs hover:underline" style="color: var(--gold);">Verify Phone</a>
                    @elseif(auth()->user()->hasVerifiedPhone())
                        <span class="text-xs text-green-600 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            Verified
                        </span>
                    @endif
                </div>
                <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}"
                       class="w-full px-3 md:px-4 py-2 md:py-3 text-sm md:text-base rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold"
                       style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
            </div>



            <button type="submit" class="w-full md:w-auto px-6 py-2 md:py-3 rounded-lg text-white text-sm md:text-base font-medium transition-colors hover:opacity-90"
                    style="background-color: var(--gold);">
                Save Changes
            </button>
        </form>
    </div>

    <!-- Security Tab -->
    <div x-show="activeTab === 'security'" x-transition>
        <form action="{{ route('settings.password') }}" method="POST" 
              class="rounded-xl shadow-sm border p-4 md:p-6"
              style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            @csrf
            @method('PUT')

            <h2 class="text-lg md:text-xl font-serif mb-4 md:mb-6" style="color: var(--text-primary);">Change Password</h2>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Current Password</label>
                <input type="password" name="current_password" required
                       class="w-full px-3 md:px-4 py-2 md:py-3 text-sm md:text-base rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold"
                       style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">New Password</label>
                <input type="password" name="password" required
                       class="w-full px-3 md:px-4 py-2 md:py-3 text-sm md:text-base rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold"
                       style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
            </div>

            <div class="mb-4 md:mb-6">
                <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Confirm New Password</label>
                <input type="password" name="password_confirmation" required
                       class="w-full px-3 md:px-4 py-2 md:py-3 text-sm md:text-base rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold"
                       style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
            </div>

            <button type="submit" class="w-full md:w-auto px-6 py-2 md:py-3 rounded-lg text-white text-sm md:text-base font-medium transition-colors hover:opacity-90"
                    style="background-color: var(--gold);">
                Update Password
            </button>
        </form>
    </div>

    <!-- Subscription Tab -->
    <div x-show="activeTab === 'subscription'" x-transition>

        {{-- Current plan --}}
        <div class="rounded-xl border p-4 md:p-6 mb-4"
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <h2 class="text-lg font-serif mb-4" style="color: var(--text-primary);">Subscription</h2>

            @if($sub)
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-lg font-bold" style="color: var(--text-primary);">{{ $sub->plan->name }}</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold"
                                  style="background: rgba(74,222,128,0.12); color: #4ADE80;">Active</span>
                        </div>
                        <p class="text-sm" style="color: var(--text-secondary);">
                            {{ ucfirst($sub->billing_cycle) }} · Renews {{ $sub->current_period_end?->format('d M Y') }}
                        </p>
                    </div>
                    <form method="POST" action="{{ route('subscription.cancel') }}">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('Cancel at end of billing period?')"
                                class="px-4 py-2 rounded-lg text-sm font-medium hover:opacity-80"
                                style="background: rgba(239,68,68,0.1); color: #F87171; border: 1px solid rgba(239,68,68,0.25);">
                            Cancel Plan
                        </button>
                    </form>
                </div>

                {{-- Credits bar --}}
                @php
                    $balance = auth()->user()->wallet?->credits_balance ?? 0;
                    $max     = $sub->plan->credits_per_cycle;
                    $pct     = $max > 0 ? min(100, round(($balance / $max) * 100)) : 0;
                @endphp
                <div class="p-4 rounded-xl" style="background: var(--bg-primary); border: 1px solid var(--border-color);">
                    <div class="flex justify-between text-sm mb-2">
                        <span style="color: var(--text-secondary);">Credits remaining</span>
                        <span class="font-bold" style="color: var(--gold);">£{{ number_format($balance, 2) }} / £{{ number_format($max, 2) }}</span>
                    </div>
                    <div class="h-2 rounded-full" style="background: var(--border-color);">
                        <div class="h-2 rounded-full transition-all" style="width: {{ $pct }}%; background: var(--gold);"></div>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-sm mb-4" style="color: var(--text-secondary);">You don't have an active subscription.</p>
                    <a href="{{ route('pricing') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold hover:opacity-90"
                       style="background: var(--gold); color: #0D1B2A;">
                        View Plans
                    </a>
                </div>
            @endif
        </div>

        {{-- Top-up packages --}}
        @if($topups->isNotEmpty())
        <div class="rounded-xl border p-4 md:p-6 mb-4"
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <h3 class="text-base font-semibold mb-4" style="color: var(--text-primary);">Top-up Credits</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($topups as $pkg)
                    <div class="rounded-xl p-3 text-center" style="background: var(--bg-primary); border: 1px solid var(--border-color);">
                        <p class="text-xs font-semibold mb-1" style="color: var(--text-primary);">{{ $pkg->label }}</p>
                        <p class="text-xl font-bold" style="color: var(--gold);">£{{ number_format($pkg->price, 0) }}</p>
                        <p class="text-xs mb-2" style="color: var(--text-secondary);">
                            £{{ number_format($pkg->credits, 0) }} credits
                            @if($pkg->bonus_credits > 0)
                                <span style="color: #4ADE80;">+£{{ number_format($pkg->bonus_credits, 0) }}</span>
                            @endif
                        </p>
                        <form method="GET" action="{{ route('subscription.topup') }}">
                            <input type="hidden" name="package_id" value="{{ $pkg->id }}">
                            <button type="submit"
                                    class="w-full py-1.5 rounded-lg text-xs font-bold hover:opacity-80"
                                    style="background: rgba(201,168,76,0.12); color: var(--gold); border: 1px solid rgba(201,168,76,0.3);">
                                Buy
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Transaction history --}}
        @if($transactions->isNotEmpty())
        <div class="rounded-xl border p-4 md:p-6"
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <h3 class="text-base font-semibold mb-4" style="color: var(--text-primary);">Credit History</h3>
            <div class="space-y-2">
                @foreach($transactions as $tx)
                    <div class="flex items-center justify-between py-2 border-b last:border-0"
                         style="border-color: var(--border-color);">
                        <div>
                            <p class="text-sm" style="color: var(--text-primary);">{{ $tx->description }}</p>
                            <p class="text-xs" style="color: var(--text-secondary);">{{ $tx->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <span class="text-sm font-bold {{ $tx->amount > 0 ? 'text-green-400' : 'text-red-400' }}">
                            {{ $tx->amount > 0 ? '+' : '' }}£{{ number_format(abs($tx->amount), 2) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
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
