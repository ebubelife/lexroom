@extends('layouts.app')

@section('title', 'Room Session — First Mediator')
@section('page-title', 'Live Session')

@section('content')
<div class="max-w-7xl mx-auto" x-data="liveRoom('{{ $room->uuid }}', '{{ request('token') }}')" x-init="init()">
    <!-- Session Header -->
    <div class="rounded-2xl shadow-lg border p-4 md:p-6 mb-6 transition-all duration-300 hover:shadow-xl"
         style="background: linear-gradient(135deg, var(--bg-secondary) 0%, rgba(201, 168, 76, 0.05) 100%); 
                border: 1px solid var(--border-color);
                backdrop-filter: blur(10px);">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
            <div class="flex-1 space-y-3">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-opacity-10" style="background-color: var(--gold);">
                        <svg class="w-5 h-5 md:w-6 md:h-6" style="color: var(--gold);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base md:text-lg font-serif leading-tight truncate max-w-md" style="color: var(--text-primary);" title="{{ $room->case_summary }}">
                            {{ $room->case_summary ? Str::limit($room->case_summary, 80) : ucfirst($room->category) . ' Dispute' }}
                        </h2>
                        <div class="flex items-center gap-3 mt-1">
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
                            <p class="text-xs font-bold" style="color: var(--text-primary);">{{ optional($room->partyB)->name ?? $room->party_b_email ?? 'Unknown' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-row lg:flex-col items-center lg:items-end gap-6 w-full lg:w-auto pt-4 lg:pt-0 border-t lg:border-t-0 border-opacity-10 border-white">
                <!-- Status & Phase -->
                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-[10px] uppercase tracking-wider opacity-60" style="color: var(--text-secondary);">Current Phase</p>
                        <span class="text-xs font-bold uppercase tracking-wide" style="color: var(--gold);">{{ str_replace('_', ' ', $room->current_phase ?: 'Opening') }}</span>
                    </div>
                    <div class="h-8 w-px bg-white bg-opacity-10 hidden sm:block"></div>
                    <span class="px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-sm" 
                          :style="getStatusStyle(status)">
                        <span x-text="status"></span>
                    </span>
                </div>

                <!-- Timer & Action -->
                <div class="flex items-center gap-4 ml-auto lg:ml-0">
                    <div class="text-right">
                        <div class="text-2xl md:text-4xl font-bold font-mono tracking-tighter" style="color: var(--gold);" x-text="formatTime(timer.remaining_seconds)"></div>
                        <p class="text-[10px] uppercase tracking-wider opacity-60 mt-[-4px]" style="color: var(--text-secondary);">Time Remaining</p>
                    </div>
                    
                    <button x-show="status === 'pending'" 
                            @click="openStartModal"
                            class="px-6 py-3 rounded-xl text-white text-sm font-bold uppercase tracking-widest shadow-lg transition-all hover:scale-105 active:scale-95"
                            style="background: linear-gradient(135deg, var(--gold) 0%, #b38f36 100%);">
                        Start Session
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
        <!-- Main Chat Area -->
        <div class="lg:col-span-3">
            <div class="rounded-xl shadow-sm border flex flex-col"
                 style="background-color: var(--bg-secondary); border-color: var(--border-color); height: 600px;">
                
                <!-- Chat Messages -->
                <div class="flex-1 overflow-y-auto p-3 md:p-4 space-y-3 md:space-y-4" id="chat-messages" x-ref="chatContainer">
                    <template x-for="message in messages" :key="message.id">
                        <div>
                            <!-- Lex Message -->
                            <div x-show="message.sender_type === 'lex'" class="w-full">
                                <div class="p-3 md:p-4 rounded-lg" style="background-color: rgba(201, 168, 76, 0.1); border-left: 4px solid var(--gold);">
                                    <div class="flex items-center mb-2">
                                        <div class="w-6 h-6 md:w-8 md:h-8 rounded-full flex items-center justify-center text-white text-xs md:text-sm font-bold mr-2"
                                             style="background-color: var(--gold);">L</div>
                                        <span class="text-sm md:text-base font-medium" style="color: var(--gold);">First Mediator AI</span>
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
                    
                    <!-- Lex Processing Indicator -->
                    <div x-show="lexProcessing" class="w-full">
                        <div class="p-3 rounded-lg flex items-center space-x-2" style="background-color: rgba(201, 168, 76, 0.05);">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-bold"
                                 style="background-color: var(--gold);">L</div>
                            <span class="text-sm" style="color: var(--text-secondary);">First Mediator is analyzing...</span>
                            <div class="flex space-x-1">
                                <div class="w-2 h-2 rounded-full animate-bounce" style="background-color: var(--gold); animation-delay: 0ms;"></div>
                                <div class="w-2 h-2 rounded-full animate-bounce" style="background-color: var(--gold); animation-delay: 150ms;"></div>
                                <div class="w-2 h-2 rounded-full animate-bounce" style="background-color: var(--gold); animation-delay: 300ms;"></div>
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
                               :disabled="status !== 'active'"
                               placeholder="Type your message..."
                               class="flex-1 px-3 md:px-4 py-2 md:py-3 text-sm md:text-base rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold disabled:opacity-50"
                               style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
                        <button @click="sendMessage"
                                :disabled="status !== 'active' || !messageInput.trim()"
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
                        :disabled="status === 'completed'"
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
                                <button x-show="status === 'pending'" 
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
    </div>
</div>

<script>
function liveRoom(roomUuid, token) {
    return {
        roomUuid: roomUuid,
        token: token,
        messageInput: '',
        messages: [],
        lastMessageId: 0,
        timer: { remaining_seconds: 0, total_seconds: 0 },
        phase: 'opening',
        status: 'pending',
        lexProcessing: false,
        files: [],
        pollInterval: null,
        
        // Progress & Modal State
        uploading: false,
        uploadProgress: 0,
        showStartModal: false,
        
        init() {
            this.startPolling();
            this.loadFiles();
        },
        
        startPolling() {
            this.poll();
            this.pollInterval = setInterval(() => this.poll(), 2000);
        },
        
        async poll() {
            try {
                const url = `/rooms/${this.roomUuid}/poll?since=${this.lastMessageId}${this.token ? '&token=' + this.token : ''}`;
                const response = await fetch(url);
                const data = await response.json();
                
                if (data.messages && data.messages.length > 0) {
                    this.messages.push(...data.messages);
                    this.lastMessageId = data.messages[data.messages.length - 1].id;
                    this.$nextTick(() => this.scrollToBottom());
                }
                
                this.timer = data.timer;
                this.phase = data.phase;
                this.status = data.status;
                this.lexProcessing = data.lex_processing;
            } catch (error) {
                console.error('Poll error:', error);
            }
        },
        
        async sendMessage() {
            if (!this.messageInput.trim() || this.status !== 'active') return;
            
            const content = this.messageInput;
            this.messageInput = '';
            
            try {
                const response = await fetch(`/rooms/${this.roomUuid}/messages`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        content: content,
                        sender_type: '{{ auth()->check() && auth()->id() === $room->party_a_id ? "party_a" : "party_b" }}'
                    })
                });
                
                const data = await response.json();
                if (data.success) {
                    this.poll();
                }
            } catch (error) {
                console.error('Send error:', error);
                this.messageInput = content;
            }
        },
        
        openStartModal() {
            if (this.status !== 'pending') return;
            this.showStartModal = true;
        },

        async startSession() {
            this.showStartModal = false;
            try {
                const response = await fetch(`/rooms/${this.roomUuid}/start`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                if (data.success) {
                    window.showToast('Session started successfully');
                    this.poll();
                }
            } catch (error) {
                console.error('Start error:', error);
                window.showToast('Failed to start session', 'error');
            }
        },
        
        async loadFiles() {
            try {
                const url = `/rooms/${this.roomUuid}/evidence${this.token ? '?token=' + this.token : ''}`;
                const response = await fetch(url);
                const data = await response.json();
                
                if (data.success) {
                    this.files = data.files;
                }
            } catch (error) {
                console.error('Load files error:', error);
            }
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

        async removeFile(fileId) {
            if (!confirm('Are you sure you want to remove this evidence?')) return;
            
            try {
                const response = await fetch(`/rooms/${this.roomUuid}/evidence/${fileId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        token: this.token
                    })
                });
                
                const data = await response.json();
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
        
        formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        },
        
        formatTimestamp(timestamp) {
            return new Date(timestamp).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        },
        
        scrollToBottom() {
            this.$nextTick(() => {
                if (this.$refs.chatContainer) {
                    this.$refs.chatContainer.scrollTop = this.$refs.chatContainer.scrollHeight;
                }
            });
        },
        
        getStatusStyle(status) {
            const styles = {
                'pending': 'background-color: rgba(245, 158, 11, 0.1); color: #B45309;',
                'active': 'background-color: rgba(34, 197, 94, 0.1); color: #15803D;',
                'completed': 'background-color: rgba(107, 107, 104, 0.1); color: #6B6B68;'
            };
            return styles[status] || styles.pending;
        }
    }
}
</script>
@endsection
