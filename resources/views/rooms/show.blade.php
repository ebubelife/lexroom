@extends('layouts.app')

@section('title', 'Room Session — First Mediator')
@section('page-title', 'Live Session')

@php
    $initialMessages = $room->messages->map(fn($m) => [
        'id' => $m->id,
        'sender_type' => $m->sender_type,
        'content' => $m->content,
        'phase' => $m->phase,
        'created_at' => $m->created_at->toIso8601String(),
    ]);
    
    // Calculate remaining seconds for JS init
    $totalSeconds = ($room->duration + $room->extended_minutes) * 60;
    $remainingSeconds = 0;
    if (in_array($room->status, ['active', 'pause_requested'])) {
        $elapsed = now()->diffInSeconds($room->started_at) - (int)$room->total_paused_seconds;
        $remainingSeconds = max(0, $totalSeconds - $elapsed);
    } elseif ($room->status === 'paused' && $room->paused_at) {
        $elapsed = $room->paused_at->diffInSeconds($room->started_at) - (int)$room->total_paused_seconds;
        $remainingSeconds = max(0, $totalSeconds - $elapsed);
    } elseif ($room->status === 'pending') {
        $remainingSeconds = $totalSeconds;
    }
@endphp

@section('content')
<div class="max-w-7xl mx-auto" x-data="liveRoom('{{ $room->uuid }}', '{{ request('token') }}')" x-init="init()">
    <!-- Session Header -->
    <div class="rounded-2xl shadow-lg border p-4 md:p-6 mb-6 transition-all duration-300 hover:shadow-xl"
         style="background: linear-gradient(135deg, var(--bg-secondary) 0%, rgba(201, 168, 76, 0.05) 100%); 
                border: 1px solid var(--border-color);
                backdrop-filter: blur(10px);">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
            <div class="flex-1 space-y-3">
                <div class="flex items-start gap-4">
                    <div class="flex-1 w-full max-w-2xl">
                        <h2 class="text-lg md:text-xl font-serif font-bold uppercase tracking-wide leading-tight break-words mb-2" style="color: var(--gold);">
                            {{ $room->title ?: ucfirst($room->category) . ' Dispute' }}
                        </h2>
                        <p class="text-sm md:text-base leading-relaxed line-clamp-2 opacity-90 break-words mb-3" style="color: var(--text-primary);" title="{{ $room->case_summary }}">
                            {{ $room->case_summary }}
                        </p>
                        <div class="flex items-center flex-wrap gap-3">
                            <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold tracking-wider" style="background-color: {{ $room->category_badge_color['bg'] }}; color: {{ $room->category_badge_color['text'] }};">{{ ucfirst($room->category) }}</span>
                            <p class="text-xs md:text-sm font-medium opacity-80" style="color: var(--gold);">{{ $room->jurisdiction }} &bull; {{ ucfirst($room->language) }}</p>
                            @if($room->case_summary)
                            <button onclick="document.getElementById('caseSummaryModal').classList.remove('hidden')" class="text-xs underline underline-offset-2 opacity-60 hover:opacity-100 transition-opacity" style="color: var(--gold);">View full summary</button>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                    <div class="flex items-center gap-2 p-2 rounded-lg bg-black bg-opacity-5 dark:bg-white dark:bg-opacity-5">
                        <svg class="w-4 h-4 opacity-60" style="color: var(--text-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16"></path></svg>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider opacity-60" style="color: var(--text-secondary);">Case ID</p>
                            <p class="text-xs font-mono font-bold" style="color: var(--text-primary);">{{ $room->case_id }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 p-2 rounded-lg bg-black bg-opacity-5 dark:bg-white dark:bg-opacity-5">
                        <svg class="w-4 h-4 opacity-60" style="color: var(--text-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider opacity-60" style="color: var(--text-secondary);">Initiator</p>
                            <p class="text-xs font-bold" style="color: var(--text-primary);">{{ optional($room->partyA)->name ?? 'Unknown' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 p-2 rounded-lg bg-black bg-opacity-5 dark:bg-white dark:bg-opacity-5">
                        <svg class="w-4 h-4 opacity-60" style="color: var(--text-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider opacity-60" style="color: var(--text-secondary);">Invited Party</p>
                            <div class="flex items-center gap-2">
                                <p class="text-xs font-bold" style="color: var(--text-primary);">{{ optional($room->partyB)->name ?? $room->party_b_email ?? 'Unknown' }}</p>
                                @if(auth()->check() && auth()->id() === $room->party_a_id && $room->status === 'pending')
                                    <button @click="copyInviteLink" 
                                            class="p-1 rounded bg-gold bg-opacity-10 hover:bg-opacity-20 transition-all group"
                                            title="Copy Invite Link for Party B">
                                        <svg class="w-3 h-3" style="color: var(--gold);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-2 lg:flex lg:flex-col items-start lg:items-end gap-4 lg:gap-6 w-full lg:w-auto pt-4 lg:pt-0 border-t lg:border-t-0 border-opacity-10 border-white">
                <!-- Status & Phase -->
                <div class="flex items-center gap-3 mt-1 lg:mt-0 justify-start">
                    <div class="text-right hidden sm:block">
                        <p class="text-[10px] uppercase tracking-wider opacity-60" style="color: var(--text-secondary);">Current Phase</p>
                        <span class="text-xs font-bold uppercase tracking-wide" style="color: var(--gold);">{{ str_replace('_', ' ', $room->current_phase ?: 'Opening') }}</span>
                    </div>
                    <div class="h-8 w-px bg-white bg-opacity-10 hidden sm:block"></div>
                    <span class="px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-sm" 
                          :style="getStatusStyle(roomSessionStatus)">
                        <span x-text="roomSessionStatus"></span>
                    </span>
                </div>

                <!-- Timer -->
                <div class="text-right justify-self-end lg:mt-0">
                    <div class="text-2xl md:text-4xl font-bold font-mono tracking-tighter" style="color: var(--gold);" x-text="roomSessionStatus === 'completed' ? '00:00' : formatTime(remainingSeconds)"></div>
                    <p class="text-[10px] uppercase tracking-wider opacity-60 mt-[-4px]" style="color: var(--text-secondary);">Time Remaining</p>
                </div>
                
                <!-- Action Buttons -->
                @if(auth()->check() && auth()->id() == $room->party_a_id)
                    <div x-show="roomSessionStatus === 'pending'" class="w-full sm:w-auto flex flex-col items-center">
                        <button @click="openStartModal"
                                :disabled="!clockedIn"
                                class="w-full px-6 py-3 rounded-xl text-white text-sm font-bold uppercase tracking-widest shadow-lg transition-all text-center"
                                :class="clockedIn ? 'hover:scale-105 active:scale-95 cursor-pointer' : 'opacity-50 cursor-not-allowed'"
                                :style="clockedIn ? 'background: linear-gradient(135deg, var(--gold) 0%, #b38f36 100%);' : 'background: #4B5563;'">
                            <span x-text="clockedIn ? 'Start Session' : 'Waiting for Party B...'"></span>
                        </button>
                        <button x-show="!clockedIn" @click="showResendModal = true" class="text-[10px] sm:text-xs uppercase tracking-wider underline mt-2 hover:opacity-100 opacity-70 transition-opacity" style="color: var(--gold);">
                            Resend / Correct Invite Email
                        </button>
                    </div>
                    
                    <!-- Pause/Resume Actions for Party A -->
                    <button x-show="roomSessionStatus === 'active'"
                            @click="showPauseModal = true"
                            class="w-full sm:w-auto px-4 py-3 sm:py-2 rounded-lg text-sm font-bold shadow transition-all border border-yellow-600 text-yellow-600 hover:bg-yellow-600 hover:text-white text-center">
                        Pause Session
                    </button>
                    <button x-show="roomSessionStatus === 'paused'"
                            @click="resumeSession"
                            class="w-full sm:w-auto px-4 py-3 sm:py-2 rounded-lg text-white text-sm font-bold shadow transition-all bg-green-600 hover:bg-green-700 text-center">
                        Resume Session
                    </button>
                    <button x-show="roomSessionStatus === 'active' || roomSessionStatus === 'completed'"
                            @click="showExtendModal = true"
                            class="w-full sm:w-auto px-4 py-3 sm:py-2 rounded-lg text-white text-sm font-bold shadow transition-all bg-blue-600 hover:bg-blue-700 text-center">
                        Extend Session
                    </button>
                    <span x-show="roomSessionStatus === 'pause_requested'" class="w-full sm:w-auto text-xs text-yellow-600 font-bold px-2 text-center flex items-center justify-center">Waiting for Party B to accept pause...</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Clock-In Overlay for Party B (Google Meet/Zoom Style) -->
    <template x-if="isPartyB && !clockedIn && roomStatus === 'pending'">
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black bg-opacity-95 backdrop-blur-xl overflow-y-auto">
            <div class="max-w-5xl w-full flex flex-col gap-8 my-auto py-8">
                <!-- Branding Header -->
                <div class="text-center">
                    <div class="inline-flex items-center justify-center px-4 py-1 rounded-full mb-4" style="background-color: rgba(201, 168, 76, 0.1); border: 1px solid rgba(201, 168, 76, 0.2);">
                        <span class="text-[10px] uppercase tracking-widest font-bold font-sans" style="color: var(--gold);">Mediation Room Invitation</span>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-serif text-white mb-2">First Mediator <span class="text-xs align-top opacity-50">TM</span></h1>
                </div>

                <!-- Main Join Card -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-0 rounded-3xl overflow-hidden shadow-2xl border" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                    <!-- Left Pane: Case Brief (Scrollable) -->
                    <div class="md:col-span-2 p-8 md:p-12 border-b md:border-b-0 md:border-r flex flex-col" style="border-color: var(--border-color);">
                        <h2 class="text-xs uppercase tracking-widest font-bold mb-6 opacity-50 flex-shrink-0" style="color: var(--gold);">Case Summary briefing</h2>
                        <div class="max-h-[300px] md:max-h-[450px] overflow-y-auto pr-4 custom-scrollbar flex-1">
                            <h3 class="text-2xl font-serif text-white mb-6 leading-tight">{{ $room->case_id }}</h3>
                            
                            <div class="mb-8 pl-4 border-l-[3px]" style="border-color: var(--gold);">
                                <h4 class="text-[10px] uppercase tracking-widest font-bold mb-3 opacity-60 text-white">Dispute Category</h4>
                                <div class="inline-flex px-4 py-1.5 rounded-full text-[11px] font-bold font-sans text-white shadow-sm" style="background-color: var(--gold);">
                                    {{ ucfirst($room->category ?? 'General') }}
                                </div>
                            </div>

                            <div class="prose prose-invert max-w-none">
                                <p class="text-lg leading-relaxed italic opacity-90" style="color: var(--text-primary);">
                                    "{{ $room->case_summary }}"
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Right Pane: Actions & Info -->
                    <div class="p-8 md:p-12 flex flex-col justify-between space-y-8 bg-black bg-opacity-20 backdrop-blur-sm">
                        <div class="space-y-8">
                            <!-- Inviter Info -->
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center text-white text-lg font-bold shadow-inner" style="background: linear-gradient(135deg, var(--gold) 0%, #b38f36 100%);">
                                    {{ strtoupper(substr($room->partyA->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-[10px] uppercase tracking-widest opacity-40 font-bold">Host Invited You</p>
                                    <p class="text-lg font-serif text-white">{{ $room->partyA->name }}</p>
                                </div>
                            </div>

                            <!-- Meeting Metadata -->
                            <div class="space-y-4">
                                <div class="flex items-center justify-between py-3 border-b" style="border-color: rgba(255,255,255,0.05);">
                                    <span class="text-xs opacity-50">Mediator Identity</span>
                                    <span class="text-xs font-bold font-mono tracking-tighter" style="color: var(--gold);">FM (FIRST MEDIATOR)</span>
                                </div>
                                <div class="flex items-center justify-between py-3 border-b" style="border-color: rgba(255,255,255,0.05);">
                                    <span class="text-xs opacity-50">Est. Duration</span>
                                    <span class="text-xs font-bold text-white">{{ $room->duration }} Mins</span>
                                </div>
                                <div class="flex items-center justify-between py-3 border-b" style="border-color: rgba(255,255,255,0.05);">
                                    <span class="text-xs opacity-50">Security Level</span>
                                    <span class="text-xs font-bold text-green-400">ENCRYPTED</span>
                                </div>
                            </div>
                        </div>

                        <!-- Main Action -->
                        <div class="space-y-4 pt-8">
                            <button @click="clockIn" 
                                    class="w-full py-5 rounded-2xl text-white font-bold uppercase tracking-widest shadow-xl transition-all hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-3"
                                    style="background: linear-gradient(135deg, var(--gold) 0%, #b38f36 100%);">
                                <span>Join Mediation Now</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </button>
                            <p class="text-[9px] text-center opacity-30 px-4 leading-tight uppercase font-bold tracking-widest">
                                By joining, you agree to the confidentiality of this session.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <style>
            .custom-scrollbar::-webkit-scrollbar { width: 4px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.02); }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: var(--gold); border-radius: 10px; }
        </style>
    </template>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
        <!-- Main Chat Area -->
        <div class="lg:col-span-3">
            <div class="rounded-xl shadow-sm border flex flex-col"
                 style="background-color: var(--bg-secondary); border-color: var(--border-color); height: 600px;">
                
                <!-- Chat Messages -->
                <div class="flex-1 overflow-y-auto p-3 md:p-4 space-y-3 md:space-y-4" id="chat-messages" x-ref="chatContainer">
                    <template x-for="message in messages" :key="message.id">
                        <div>
                            <!-- FM Message -->
                            <div x-show="message.sender_type === 'lex'" class="w-full">
                                <div class="p-3 md:p-4 rounded-lg" style="background-color: rgba(201, 168, 76, 0.1); border-left: 4px solid var(--gold);">
                                    <div class="flex items-center mb-2">
                                        <div class="w-6 h-6 md:w-8 md:h-8 rounded-full flex items-center justify-center text-white text-xs md:text-sm font-bold mr-2"
                                             style="background-color: var(--gold);">FM</div>
                                        <span class="text-[10px] font-bold tracking-widest uppercase opacity-50" style="color: var(--gold);">FM Mediator</span>
                                    </div>
                                    <p class="text-sm md:text-base whitespace-pre-wrap" style="color: var(--text-primary);" x-text="message.content"></p>
                                    <span class="text-xs mt-2 block" style="color: var(--text-secondary);" x-text="formatTimestamp(message.created_at)"></span>
                                </div>
                            </div>

                            <!-- Party A Message (Blue - Left) -->
                            <div x-show="message.sender_type === 'party_a'" class="flex justify-start">
                                <div class="max-w-[85%] md:max-w-[70%]">
                                    <div class="flex items-center mb-1">
                                        <div class="w-5 h-5 md:w-6 md:h-6 rounded-full flex items-center justify-center text-white text-xs font-bold mr-2"
                                             style="background-color: #1D4ED8;">A</div>
                                        <span class="text-xs font-medium" style="color: var(--text-secondary);">Party A</span>
                                    </div>
                                    <div class="p-2 md:p-3 rounded-lg rounded-tl-none" style="background-color: rgba(29, 78, 216, 0.1); border: 1px solid rgba(29, 78, 216, 0.2);">
                                        <p class="text-xs md:text-sm whitespace-pre-wrap" style="color: var(--text-primary);" x-text="message.content"></p>
                                        <span class="text-xs mt-1 block" style="color: var(--text-secondary);" x-text="formatTimestamp(message.created_at)"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Party B Message (Purple - Right) -->
                            <div x-show="message.sender_type === 'party_b'" class="flex justify-end">
                                <div class="max-w-[85%] md:max-w-[70%]">
                                    <div class="flex items-center justify-end mb-1">
                                        <span class="text-xs font-medium mr-2" style="color: var(--text-secondary);">Party B</span>
                                        <div class="w-5 h-5 md:w-6 md:h-6 rounded-full flex items-center justify-center text-white text-xs font-bold"
                                             style="background-color: #7E22CE;">B</div>
                                    </div>
                                    <div class="p-2 md:p-3 rounded-lg rounded-tr-none" style="background-color: rgba(126, 34, 206, 0.1); border: 1px solid rgba(126, 34, 206, 0.2);">
                                        <p class="text-xs md:text-sm whitespace-pre-wrap" style="color: var(--text-primary);" x-text="message.content"></p>
                                        <span class="text-xs mt-1 block" style="color: var(--text-secondary);" x-text="formatTimestamp(message.created_at)"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    
                    <!-- FM Processing Indicator -->
                    <div x-show="lexProcessing" class="w-full">
                        <div class="p-3 rounded-lg" style="background-color: rgba(201, 168, 76, 0.05);">
                            <p class="text-[10px] uppercase tracking-wider font-bold mb-1 opacity-50" style="color: var(--gold);">FM is analyzing...</p>
                            <div class="flex space-x-1.5 p-2 bg-opacity-50 rounded-lg inline-flex" style="background-color: var(--bg-secondary);">
                                <div class="w-1.5 h-1.5 rounded-full animate-bounce" style="background-color: var(--gold); border-radius: 4px; border: 2px solid var(--gold); animation-delay: 0s"></div>
                                <div class="w-1.5 h-1.5 rounded-full animate-bounce" style="background-color: var(--gold); border-radius: 4px; border: 2px solid var(--gold); animation-delay: 0.2s"></div>
                                <div class="w-1.5 h-1.5 rounded-full animate-bounce" style="background-color: var(--gold); border-radius: 4px; border: 2px solid var(--gold); animation-delay: 0.4s"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Message Input -->
                <div class="border-t p-3 md:p-4" style="border-color: var(--border-color);">
                    <div class="flex items-center space-x-2">
                        <input type="text" 
                               x-model="messageInput"
                               @keyup.enter="sendMessage"
                               :disabled="roomSessionStatus !== 'active'"
                               :placeholder="roomSessionStatus === 'pending' ? 'Waiting for session to start...' : 'Type your message...'"
                               class="flex-1 px-3 md:px-4 py-2 md:py-3 text-sm md:text-base rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold disabled:opacity-50"
                               style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
                        <button @click="sendMessage"
                                :disabled="roomSessionStatus !== 'active' || !messageInput.trim()"
                                class="px-4 md:px-6 py-2 md:py-3 rounded-lg text-white text-sm md:text-base font-medium transition-colors hover:opacity-90 disabled:opacity-50"
                                style="background-color: var(--gold);">
                            Send
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Evidence Vault Sidebar -->
        <div class="lg:col-span-1">
            <div class="rounded-xl shadow-sm border p-3 md:p-4"
                 style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                
                <h3 class="text-base md:text-lg font-serif mb-3 md:mb-4" style="color: var(--text-primary);">Evidence Vault</h3>
                
                <!-- Upload Button -->
                <button @click="$refs.fileInput.click()"
                        :disabled="roomSessionStatus === 'completed'"
                        class="w-full px-3 md:px-4 py-2 md:py-3 rounded-lg border-2 border-dashed transition-colors hover:border-gold mb-3 md:mb-4 disabled:opacity-50"
                        style="border-color: var(--border-color); color: var(--text-primary);">
                    <svg class="w-5 h-5 md:w-6 md:h-6 mx-auto mb-1 md:mb-2" style="color: var(--gold);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <span class="text-xs md:text-sm font-medium">Upload Evidence</span>
                    <p class="text-xs mt-1" style="color: var(--text-secondary);">PDF, DOCX, Images</p>
                </button>
                <input type="file" x-ref="fileInput" class="hidden" @change="uploadFile">

                <!-- Upload Progress -->
                <div x-show="uploading" class="mb-4 p-3 rounded-lg bg-black bg-opacity-5 dark:bg-white dark:bg-opacity-5">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[10px] uppercase tracking-wider font-bold" style="color: var(--gold);">Uploading...</span>
                        <span class="text-[10px] font-mono" style="color: var(--text-primary);" x-text="uploadProgress + '%'"></span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                        <div class="h-full transition-all duration-300" 
                             style="background-color: var(--gold);" 
                             :style="{ width: uploadProgress + '%' }"></div>
                    </div>
                </div>

                <!-- Uploaded Files -->
                <div class="space-y-2" x-show="files.length > 0">
                    <template x-for="file in files" :key="file.id">
                        <div class="p-2 md:p-3 rounded-lg border group transition-all" style="background-color: var(--bg-primary); border-color: var(--border-color);">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start flex-1 min-w-0">
                                    <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 flex-shrink-0" style="color: var(--gold);" x-html="file.icon" fill="currentColor" viewBox="0 0 20 20">
                                    </svg>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs md:text-sm font-medium truncate" style="color: var(--text-primary);" x-text="file.filename"></p>
                                        <p class="text-[10px] uppercase tracking-wider opacity-60" style="color: var(--text-secondary);" x-text="file.party"></p>
                                    </div>
                                </div>
                                <button x-show="roomSessionStatus === 'pending'" 
                                        @click="removeFile(file.id)"
                                        class="p-1 rounded-md opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-500 hover:bg-opacity-10 text-red-500"
                                        title="Remove Evidence">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <div x-show="files.length === 0" class="text-center py-6 md:py-8">
                    <p class="text-xs md:text-sm" style="color: var(--text-secondary);">No files uploaded yet</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Case Summary Modal -->
    <div id="caseSummaryModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black bg-opacity-60 backdrop-blur-sm" onclick="document.getElementById('caseSummaryModal').classList.add('hidden')"></div>
        <div class="relative w-full max-w-lg p-8 rounded-2xl shadow-2xl border" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-serif" style="color: var(--text-primary);">Case Summary</h3>
                <button onclick="document.getElementById('caseSummaryModal').classList.add('hidden')" class="p-2 rounded-lg hover:bg-opacity-10 hover:bg-gray-500 transition-colors" style="color: var(--text-secondary);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-4 rounded-xl text-sm leading-relaxed max-h-80 overflow-y-auto" style="background-color: var(--bg-primary); color: var(--text-primary); border: 1px solid var(--border-color);">
                {{ $room->case_summary }}
            </div>
            <div class="mt-4 flex items-center gap-2 text-xs" style="color: var(--text-secondary);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Visible only to parties in this room
            </div>
        </div>
    </div>

    <!-- Start Session Confirmation Modal -->
    <div x-show="showStartModal" 
         x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black bg-opacity-60 backdrop-blur-sm" @click="showStartModal = false"></div>
        
        <!-- Modal Content -->
        <div class="relative w-full max-w-md p-8 rounded-2xl shadow-2xl border"
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            
            <div class="text-center">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6"
                     style="background-color: rgba(201, 168, 76, 0.1);">
                    <svg class="w-8 h-8" style="color: var(--gold);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                
                <h3 class="text-2xl font-serif mb-2" style="color: var(--text-primary);">Begin Mediation Session?</h3>
                <p class="text-sm opacity-70 mb-8" style="color: var(--text-secondary);">
                    The official countdown will begin, and First Mediator AI will initiate the opening statements. 
                    <span class="block mt-2 font-bold text-red-500">Note: Evidence removal will be disabled once started.</span>
                </p>
                
                <div class="flex flex-col gap-3">
                    <button @click="startSession"
                            class="w-full py-4 rounded-xl text-white font-bold uppercase tracking-widest shadow-lg hover:scale-105 transition-transform"
                            style="background: linear-gradient(135deg, var(--gold) 0%, #b38f36 100%);">
                        Yes, Start Session
                    </button>
                    <button @click="showStartModal = false"
                            class="w-full py-3 text-sm font-medium opacity-60 hover:opacity-100 transition-opacity"
                            style="color: var(--text-secondary);">
                        Wait, Go Back
                    </button>
                </div>
            </div>
        </div>
        <!-- Pause Request Alert for Party B -->
        <div x-show="roomSessionStatus === 'pause_requested' && isPartyB" class="fixed top-4 right-4 z-50 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-lg max-w-sm">
            <div class="font-bold mb-1">Pause Requested</div>
            <p class="text-sm mb-3">Party A has requested to pause this session. The countdown will stop once accepted.</p>
            <div class="flex gap-2">
                <button @click="acceptPause" class="px-3 py-1 bg-red-600 text-white rounded text-sm font-bold hover:bg-red-700">Accept Pause</button>
            </div>
        </div>

        <!-- Pause Confirmation Modal for Party A -->
        <div x-show="showPauseModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black bg-opacity-60 backdrop-blur-sm" @click="showPauseModal = false"></div>
            <div class="relative w-full max-w-sm p-6 rounded-2xl shadow-2xl border bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-serif mb-2 text-gray-900 dark:text-white">Pause Session?</h3>
                <p class="text-sm opacity-80 mb-6 text-gray-600 dark:text-gray-300">
                    A pause request will be sent to Party B. Once accepted, the session is paused and timer stops. 
                    A paused session expires and ends automatically after 24 hours.
                </p>
                <div class="flex gap-2 w-full">
                    <button @click="requestPause" class="flex-1 py-2 bg-yellow-600 text-white rounded-lg font-bold">Request Pause</button>
                    <button @click="showPauseModal = false" class="flex-1 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg font-bold text-gray-800 dark:text-white">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Resend/Correct Invite Modal -->
    <template x-if="showResendModal">
        <div class="fixed inset-0 z-[80] flex items-center justify-center p-4 bg-black bg-opacity-50 backdrop-blur-sm">
            <div @click.away="showResendModal = false" class="bg-white dark:bg-gray-800 rounded-3xl p-6 md:p-8 max-w-sm w-full shadow-2xl relative border border-gray-100 dark:border-gray-700">
                <button @click="showResendModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                <div class="text-center mb-6">
                    <div class="w-12 h-12 rounded-full mx-auto mb-4 flex items-center justify-center" style="background-color: rgba(201, 168, 76, 0.1);">
                        <svg class="w-6 h-6" style="color: var(--gold);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-serif font-bold text-gray-900 dark:text-white mb-2">Resend Invitation</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Current Party B Email Address:</p>
                </div>
                <div class="mb-6">
                    <input type="email" x-model="resendEmail" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-yellow-600 focus:border-transparent transition-all text-center">
                </div>
                <button @click="submitResendInvite()" 
                        :disabled="isResending"
                        class="w-full py-3 rounded-xl text-white text-sm font-bold tracking-widest uppercase transition-all shadow-lg"
                        :class="isResending ? 'opacity-50 cursor-not-allowed' : 'hover:scale-105 active:scale-95 cursor-pointer'"
                        style="background: linear-gradient(135deg, var(--gold) 0%, #b38f36 100%);">
                    <span x-text="isResending ? 'Sending...' : 'Resend Invite'"></span>
                </button>
            </div>
        </div>
    </template>
    
    <!-- Extend Session Modal -->
    <div x-show="showExtendModal" 
         x-cloak
         class="fixed inset-0 z-[110] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black bg-opacity-60 backdrop-blur-sm" @click="showExtendModal = false"></div>
        <div class="relative w-full max-w-sm p-6 rounded-3xl shadow-2xl border"
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            
            <div class="text-center mb-6">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4"
                     style="background-color: rgba(99, 102, 241, 0.1);">
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-serif text-white">Extend Session</h3>
                <p class="text-xs opacity-70 mt-2">Purchase more time to continue the mediation. Each 30-min block is ₦5,000.</p>
            </div>

            <div class="space-y-4 mb-8">
                <label class="block text-xs uppercase tracking-widest font-bold opacity-50 mb-2">Select Duration</label>
                <div class="grid grid-cols-2 gap-3">
                    <template x-for="mins in [30, 60, 90, 120]">
                        <button @click="extendingMinutes = mins"
                                :class="extendingMinutes === mins ? 'border-blue-500 bg-blue-500 bg-opacity-10' : 'border-gray-700 hover:border-gray-500'"
                                class="p-3 rounded-xl border text-center transition-all">
                            <span class="block text-sm font-bold text-white" x-text="mins + ' Min'"></span >
                            <span class="block text-[10px] opacity-60" x-text="'₦' + (mins/30 * 5000).toLocaleString()"></span>
                        </button>
                    </template>
                </div>
            </div>

            <button @click="buyTime"
                    :disabled="isExtending"
                    class="w-full py-4 rounded-xl text-white font-bold uppercase tracking-widest shadow-lg transition-all"
                    :class="isExtending ? 'opacity-50' : 'hover:scale-[1.02] active:scale-95'"
                    style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                <span x-text="isExtending ? 'Processing...' : 'Purchase Extension'"></span>
            </button>
            <button @click="showExtendModal = false" class="w-full mt-3 text-xs opacity-50 hover:opacity-100 transition-opacity">Cancel</button>
        </div>
    </div>
    </div>
    
<script>
function liveRoom(roomUuid, token) {
    return {
        roomUuid: roomUuid,
        token: token,
        messageInput: '',
        messages: {!! json_encode($initialMessages) !!},
        lastMessageId: parseInt('{{ $room->messages->last()?->id ?? 0 }}') || 0,
        remainingSeconds: parseInt('{{ $remainingSeconds }}') || 0,
        totalSeconds: parseInt('{{ $totalSeconds }}') || 0,
        roomSessionStatus: '{{ $room->status }}',
        timerSynced: false,
        lexProcessing: false,
        files: [],
        pollInterval: null,
        timerInterval: null,
        isPolling: false,
        isPartyA: parseInt('{{ (auth()->check() && auth()->id() == $room->party_a_id) ? "1" : "0" }}') === 1,
        isPartyB: parseInt('{{ ((auth()->check() && auth()->id() == $room->party_b_id) || (request()->hasValidSignature() && request('token') === $room->invite_token)) ? "1" : "0" }}') === 1,
        clockedIn: parseInt('{{ $room->party_b_clocked_in_at ? "1" : "0" }}') === 1,
        inviteUrl: "{{ route('rooms.show', ['uuid' => $room->uuid, 'token' => $room->invite_token]) }}",
        
        // Progress & Modal State
        uploading: false,
        uploadProgress: 0,
        showStartModal: false,
        showPauseModal: false,
        
        // Resend Invite State
        showResendModal: false,
        resendEmail: '{{ $room->party_b_email }}',
        isResending: false,

        // Extension & Warning State
        showExtendModal: false,
        extendingMinutes: 30,
        isExtending: false,
        notified10Min: false,
        notified5Min: false,
        notified1Min: false,
        
        submitResendInvite: function() {
            var self = this;
            this.isResending = true;
            fetch(`/rooms/${this.roomUuid}/resend-invite`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ email: this.resendEmail })
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                self.isResending = false;
                if (data.success) {
                    self.showResendModal = false;
                    alert('Invitation sent successfully!');
                } else {
                    alert(data.message || 'Failed to resend invitation.');
                }
            })
            .catch(function(err) {
                self.isResending = false;
                alert('An error occurred while resending the invitation.');
            });
        },
        
        init: function() {
            var self = this;
            this.scrollToBottom();
            this.pollInterval = setInterval(function() { self.poll(); }, 2000);
            
            if (this.roomSessionStatus === 'active') {
                this.timerSynced = true;
                this.startLocalTimer();
            }
            this.loadFiles();
        },
        
        startLocalTimer: function() {
            var self = this;
            if (this.timerInterval) clearInterval(this.timerInterval);
            this.timerInterval = setInterval(function() {
                if (self.roomSessionStatus === 'active') {
                    if (self.remainingSeconds > 0) {
                        self.remainingSeconds--;
                        self.checkWarnings();
                    } else {
                        self.lockSession();
                    }
                }
            }, 1000);
        },

        checkWarnings: function() {
            if (this.remainingSeconds === 600 && !this.notified10Min) {
                window.showToast('10 minutes remaining in this session.', 'info');
                this.notified10Min = true;
            }
            if (this.remainingSeconds === 300 && !this.notified5Min) {
                window.showToast('5 minutes remaining. Please start concluding.', 'warning');
                this.notified5Min = true;
            }
            if (this.remainingSeconds === 60 && !this.notified1Min) {
                this.notified1Min = true;
                alert('Only 1 minute left! Please round up your discussion.');
            }
        },

        lockSession: function() {
            var self = this;
            if (this.timerInterval) {
                clearInterval(this.timerInterval);
                this.timerInterval = null;
            }

            if (this.roomSessionStatus === 'completed') return;

            fetch(`/rooms/${this.roomUuid}/lock`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.success) {
                    self.roomSessionStatus = 'completed';
                    window.showToast('Session time expired. Room locked.', 'error');
                }
            })
            .catch(function(err) {
                console.error('Lock error:', err);
            });
        },

        buyTime: function() {
            var self = this;
            this.isExtending = true;
            fetch(`/rooms/${this.roomUuid}/extend`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ minutes: this.extendingMinutes })
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                self.isExtending = false;
                if (data.success) {
                    self.showExtendModal = false;
                    self.roomSessionStatus = 'active';
                    self.remainingSeconds = data.timer.remaining_seconds;
                    self.totalSeconds = data.timer.total_seconds;
                    self.timerSynced = true;

                    self.notified10Min = false;
                    self.notified5Min = false;
                    self.notified1Min = false;

                    self.startLocalTimer();
                    window.showToast(`Session extended by ${self.extendingMinutes} minutes!`);
                } else if (data.error) {
                    alert(data.error);
                }
            })
            .catch(function(err) {
                self.isExtending = false;
                console.error('Extend error:', err);
                alert('Failed to extend session. Check connection.');
            });
        },
        
        poll: function() {
            var self = this;
            if (this.isPolling) return;
            this.isPolling = true;
            
            var url = `/rooms/${this.roomUuid}/poll?since=${this.lastMessageId}${this.token ? '&token=' + this.token : ''}`;
            fetch(url)
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.messages && data.messages.length > 0) {
                    for (var i = 0; i < data.messages.length; i++) {
                        self.messages.push(data.messages[i]);
                    }
                    self.lastMessageId = data.messages[data.messages.length - 1].id;
                    self.$nextTick(function() { self.scrollToBottom(); });
                }
                
                self.roomSessionStatus = data.status;
                self.lexProcessing = data.lex_processing;
                self.clockedIn = !!data.party_b_clocked_in_at;

                if (self.roomSessionStatus === 'active') {
                    if (!self.timerSynced) {
                        if (data.timer && data.timer.remaining_seconds > 0) {
                            self.remainingSeconds = Math.floor(data.timer.remaining_seconds);
                            self.totalSeconds = Math.floor(data.timer.total_seconds);
                        }
                        self.timerSynced = true;
                        self.startLocalTimer();
                    }
                } else if (self.roomSessionStatus === 'paused' || self.roomSessionStatus === 'pause_requested') {
                    if (self.timerInterval) {
                        clearInterval(self.timerInterval);
                        self.timerInterval = null;
                    }
                    if (data.timer && data.timer.remaining_seconds > 0) {
                        self.remainingSeconds = Math.floor(data.timer.remaining_seconds);
                    }
                    self.timerSynced = false;
                }
            })
            .catch(function(err) {
                console.error('Poll error:', err);
            })
            .finally(function() {
                self.isPolling = false;
            });
        },
        
        sendMessage: function() {
            var self = this;
            if (!this.messageInput.trim() || this.roomSessionStatus !== 'active') return;
            
            var content = this.messageInput;
            this.messageInput = '';
            
            fetch(`/rooms/${this.roomUuid}/messages`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    content: content,
                    sender_type: this.isPartyA ? 'party_a' : 'party_b'
                })
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.success && data.message) {
                    self.messages.push(data.message);
                    if (data.message.id > self.lastMessageId) {
                        self.lastMessageId = data.message.id;
                    }
                    self.$nextTick(function() { self.scrollToBottom(); });
                    setTimeout(function() { self.poll(); }, 500);
                }
            })
            .catch(function(err) {
                console.error('Send error:', err);
                self.messageInput = content;
            });
        },
        
        openStartModal: function() {
            if (this.roomSessionStatus !== 'pending') return;
            this.showStartModal = true;
        },

        startSession: async function() {
            var self = this;
            this.showStartModal = false;
            try {
                var response = await fetch(`/rooms/${this.roomUuid}/start`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                var data = await response.json();
                if (data.success) {
                    window.showToast('Session started successfully');
                    this.roomSessionStatus = 'active';
                    this.remainingSeconds = data.timer.remaining_seconds;
                    this.totalSeconds = data.timer.total_seconds;
                    this.timerSynced = true;
                    this.startLocalTimer();
                }
            } catch (error) {
                console.error('Start error:', error);
                window.showToast('Failed to start session', 'error');
            }
        },

        clockIn: async function() {
            try {
                var response = await fetch(`/rooms/${this.roomUuid}/clock-in`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        token: this.token
                    })
                });
                
                var data = await response.json();
                if (data.success) {
                    this.clockedIn = true;
                    window.showToast('You have joined the mediation room');
                    this.poll();
                } else {
                    window.showToast(data.error || 'Failed to join mediation', 'error');
                }
            } catch (error) {
                console.error('Clock-in error:', error);
                window.showToast('Error joining mediation', 'error');
            }
        },

        requestPause: async function() {
            this.showPauseModal = false;
            try {
                var response = await fetch(`/rooms/${this.roomUuid}/pause-request`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                var data = await response.json();
                if (data.success) {
                    window.showToast('Pause requested');
                    this.poll();
                } else {
                    window.showToast(data.error || 'Failed to request pause', 'error');
                }
            } catch (error) {
                console.error(error);
                window.showToast('Error requesting pause', 'error');
            }
        },

        acceptPause: async function() {
            try {
                var response = await fetch(`/rooms/${this.roomUuid}/pause-accept`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                var data = await response.json();
                if (data.success) {
                    window.showToast('Session paused');
                    this.poll();
                } else {
                    window.showToast(data.error || 'Failed to accept pause', 'error');
                }
            } catch (error) {
                console.error(error);
                window.showToast('Error accepting pause', 'error');
            }
        },

        resumeSession: async function() {
            try {
                var response = await fetch(`/rooms/${this.roomUuid}/resume`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                var data = await response.json();
                if (data.success) {
                    window.showToast('Session resumed');
                    this.poll();
                } else {
                    window.showToast(data.error || 'Failed to resume session', 'error');
                }
            } catch (error) {
                console.error(error);
                window.showToast('Error resuming session', 'error');
            }
        },
        
        loadFiles: async function() {
            try {
                var url = `/rooms/${this.roomUuid}/evidence${this.token ? '?token=' + this.token : ''}`;
                var response = await fetch(url);
                var data = await response.json();
                
                if (data.success) {
                    this.files = data.files;
                }
            } catch (error) {
                console.error('Load files error:', error);
            }
        },

        copyInviteLink() {
            navigator.clipboard.writeText(this.inviteUrl).then(() => {
                window.showToast('Invite link copied! Open this in an Incognito window to join as Party B.');
            }).catch(err => {
                console.error('Copy failed:', err);
                window.showToast('Failed to copy link', 'error');
            });
        },
        
        uploadFile(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            if (file.size > 20 * 1024 * 1024) {
                window.showToast('File size must be less than 20MB', 'error');
                return;
            }
            
            this.uploading = true;
            this.uploadProgress = 0;
            
            const formData = new FormData();
            formData.append('file', file);
            if (this.token) formData.append('token', this.token);
            
            const xhr = new XMLHttpRequest();
            xhr.open('POST', `/rooms/${this.roomUuid}/evidence`);
            xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
            
            xhr.upload.onprogress = (e) => {
                if (e.lengthComputable) {
                    this.uploadProgress = Math.round((e.loaded / e.total) * 100);
                }
            };
            
            xhr.onload = () => {
                this.uploading = false;
                const data = JSON.parse(xhr.responseText);
                if (xhr.status >= 200 && xhr.status < 300 && data.success) {
                    window.showToast('Evidence uploaded successfully');
                    this.loadFiles();
                    event.target.value = '';
                } else {
                    window.showToast(data.message || 'Upload failed', 'error');
                }
            };
            
            xhr.onerror = () => {
                this.uploading = false;
                window.showToast('Upload failed due to network error', 'error');
            };
            
            xhr.send(formData);
        },

        removeFile: async function(fileId) {
            if (!confirm('Are you sure you want to remove this evidence?')) return;
            
            try {
                var response = await fetch(`/rooms/${this.roomUuid}/evidence/${fileId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        token: this.token
                    })
                });
                
                var data = await response.json();
                if (data.success) {
                    window.showToast('Evidence removed');
                    this.loadFiles();
                } else {
                    window.showToast(data.message || 'Failed to remove evidence', 'error');
                }
            } catch (error) {
                console.error('Remove error:', error);
                window.showToast('Error removing evidence', 'error');
            }
        },
        
        formatTime: function(seconds) {
            var s = Math.max(0, Math.floor(seconds));
            var mins = Math.floor(s / 60);
            var secs = s % 60;
            return (mins < 10 ? '0' + mins : mins) + ":" + (secs < 10 ? '0' + secs : secs);
        },
        
        formatTimestamp: function(timestamp) {
            return new Date(timestamp).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        },
        
        scrollToBottom: function() {
            var self = this;
            this.$nextTick(function() {
                if (self.$refs.chatContainer) {
                    self.$refs.chatContainer.scrollTop = self.$refs.chatContainer.scrollHeight;
                }
            });
        },
        
        getStatusStyle: function(currentSessionStatus) {
            var styles = {
                'pending': 'background-color: rgba(245, 158, 11, 0.1); color: #B45309;',
                'active': 'background-color: rgba(34, 197, 94, 0.1); color: #15803D;',
                'completed': 'background-color: rgba(107, 107, 104, 0.1); color: #6B6B68;',
                'pause_requested': 'background-color: rgba(185, 28, 28, 0.1); color: #B91C1C;',
                'paused': 'background-color: rgba(75, 85, 99, 0.1); color: #4B5563;'
            };
            return styles[currentSessionStatus] || styles.pending;
        }
    };
}
</script>
@endsection
