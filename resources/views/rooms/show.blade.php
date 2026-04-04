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
                        <h2 class="text-lg md:text-2xl font-serif leading-tight" style="color: var(--text-primary);">{{ $room->case_summary ?: ucfirst($room->category) . ' Dispute' }}</h2>
                        <p class="text-xs md:text-sm font-medium opacity-80" style="color: var(--gold);">{{ $room->jurisdiction }} &bull; {{ ucfirst($room->language) }}</p>
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
                            @click="startSession"
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

                <!-- Uploaded Files -->
                <div class="space-y-2" x-show="files.length > 0">
                    <template x-for="file in files" :key="file.id">
                        <div class="p-2 md:p-3 rounded-lg border" style="background-color: var(--bg-primary); border-color: var(--border-color);">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start flex-1 min-w-0">
                                    <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 flex-shrink-0" style="color: var(--gold);" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs md:text-sm font-medium truncate" style="color: var(--text-primary);" x-text="file.filename"></p>
                                        <p class="text-xs" style="color: var(--text-secondary);" x-text="file.party"></p>
                                    </div>
                                </div>
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
        
        async startSession() {
            try {
                const response = await fetch(`/rooms/${this.roomUuid}/start`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                if (data.success) {
                    this.poll();
                }
            } catch (error) {
                console.error('Start error:', error);
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
        
        async uploadFile(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            if (file.size > 20 * 1024 * 1024) {
                alert('File size must be less than 20MB');
                return;
            }
            
            const formData = new FormData();
            formData.append('file', file);
            if (this.token) formData.append('token', this.token);
            
            try {
                const response = await fetch(`/rooms/${this.roomUuid}/evidence`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });
                
                const data = await response.json();
                if (data.success) {
                    this.loadFiles();
                    event.target.value = '';
                }
            } catch (error) {
                console.error('Upload error:', error);
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
            this.$refs.chatContainer.scrollTop = this.$refs.chatContainer.scrollHeight;
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
