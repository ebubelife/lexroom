@extends('layouts.app')

@section('title', 'Room Session — FirstMediator')
@section('page-title', 'Live Session')

@section('content')
<div class="max-w-7xl mx-auto" x-data="liveRoom('{{ $room->uuid }}', '{{ request('token') }}')" x-init="init()">
    <!-- Session Header -->
    <div class="rounded-xl shadow-sm border p-3 md:p-4 mb-4"
         style="background-color: var(--bg-secondary); border-color: var(--border-color);">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
            <div class="flex-1">
                <h2 class="text-base md:text-lg font-serif" style="color: var(--text-primary);">{{ ucfirst($room->category) }} Dispute</h2>
                <p class="text-xs md:text-sm" style="color: var(--text-secondary);">{{ $room->jurisdiction }}</p>
            </div>
            
            <!-- Timer -->
            <div class="text-center">
                <div class="text-2xl md:text-3xl font-bold font-mono" style="color: var(--gold);" x-text="formatTime(timeRemaining)"></div>
                <p class="text-xs" style="color: var(--text-secondary);">Time Remaining</p>
            </div>
            
            <div class="flex items-center space-x-3">
                <span class="px-3 py-1 rounded-full text-xs font-medium" 
                      style="background-color: rgba(34, 197, 94, 0.1); color: #15803D;">
                    {{ ucfirst($room->status) }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
        <!-- Main Chat Area -->
        <div class="lg:col-span-3 order-2 lg:order-1">
            <div class="rounded-xl shadow-sm border flex flex-col"
                 style="background-color: var(--bg-secondary); border-color: var(--border-color); height: 500px; lg:height: calc(100vh - 250px);">
                
                <!-- Chat Messages -->
                <div class="flex-1 overflow-y-auto p-3 md:p-4 space-y-3 md:space-y-4" id="chat-messages">
                    <!-- Lex Welcome Message -->
                    <div class="w-full">
                        <div class="p-3 md:p-4 rounded-lg" style="background-color: rgba(201, 168, 76, 0.1); border-left: 4px solid var(--gold);">
                            <div class="flex items-center mb-2">
                                <div class="w-6 h-6 md:w-8 md:h-8 rounded-full flex items-center justify-center text-white text-xs md:text-sm font-bold mr-2"
                                     style="background-color: var(--gold);">L</div>
                                <span class="text-sm md:text-base font-medium" style="color: var(--gold);">Lex AI Mediator</span>
                            </div>
                            <p class="text-sm md:text-base" style="color: var(--text-primary);">
                                Welcome to your mediation session. I'm Lex, your AI mediator. I've reviewed both case summaries. 
                                Let's begin with opening statements. <strong>Party A</strong>, please present your case first.
                            </p>
                        </div>
                    </div>

                    <!-- Party A Message (Blue - Left) -->
                    <div class="flex justify-start">
                        <div class="max-w-[85%] md:max-w-[70%]">
                            <div class="flex items-center mb-1">
                                <div class="w-5 h-5 md:w-6 md:h-6 rounded-full flex items-center justify-center text-white text-xs font-bold mr-2"
                                     style="background-color: #1D4ED8;">A</div>
                                <span class="text-xs font-medium" style="color: var(--text-secondary);">Party A</span>
                            </div>
                            <div class="p-2 md:p-3 rounded-lg rounded-tl-none" style="background-color: rgba(29, 78, 216, 0.1); border: 1px solid rgba(29, 78, 216, 0.2);">
                                <p class="text-xs md:text-sm" style="color: var(--text-primary);">
                                    I hired the defendant to build a website for my business. We agreed on $1,500 for the project...
                                </p>
                                <span class="text-xs" style="color: var(--text-secondary);">10:23 AM</span>
                            </div>
                        </div>
                    </div>

                    <!-- Party B Message (Purple - Right) -->
                    <div class="flex justify-end">
                        <div class="max-w-[85%] md:max-w-[70%]">
                            <div class="flex items-center justify-end mb-1">
                                <span class="text-xs font-medium mr-2" style="color: var(--text-secondary);">Party B</span>
                                <div class="w-5 h-5 md:w-6 md:h-6 rounded-full flex items-center justify-center text-white text-xs font-bold"
                                     style="background-color: #7E22CE;">B</div>
                            </div>
                            <div class="p-2 md:p-3 rounded-lg rounded-tr-none" style="background-color: rgba(126, 34, 206, 0.1); border: 1px solid rgba(126, 34, 206, 0.2);">
                                <p class="text-xs md:text-sm" style="color: var(--text-primary);">
                                    That's not accurate. The agreement was for $1,000, and I completed all the work as specified...
                                </p>
                                <span class="text-xs" style="color: var(--text-secondary);">10:25 AM</span>
                            </div>
                        </div>
                    </div>

                    <!-- Lex Analysis Message -->
                    <div class="w-full">
                        <div class="p-3 md:p-4 rounded-lg" style="background-color: rgba(201, 168, 76, 0.1); border-left: 4px solid var(--gold);">
                            <div class="flex items-center mb-2">
                                <div class="w-6 h-6 md:w-8 md:h-8 rounded-full flex items-center justify-center text-white text-xs md:text-sm font-bold mr-2"
                                     style="background-color: var(--gold);">L</div>
                                <span class="text-sm md:text-base font-medium" style="color: var(--gold);">Lex AI Mediator</span>
                            </div>
                            <p class="text-sm md:text-base" style="color: var(--text-primary);">
                                I notice a discrepancy regarding the agreed amount. Party A claims $1,500 while Party B states $1,000. 
                                Do either of you have written documentation of the agreement? Please upload any contracts or communications to the Evidence Vault.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Message Input -->
                <div class="border-t p-3 md:p-4" style="border-color: var(--border-color);">
                    <div class="flex items-center space-x-2">
                        <input type="text" 
                               x-model="messageInput"
                               @keyup.enter="sendMessage"
                               placeholder="Type your message..."
                               class="flex-1 px-3 md:px-4 py-2 md:py-3 text-sm md:text-base rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold"
                               style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
                        <button @click="sendMessage"
                                class="px-4 md:px-6 py-2 md:py-3 rounded-lg text-white text-sm md:text-base font-medium transition-colors hover:opacity-90"
                                style="background-color: var(--gold);">
                            Send
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Evidence Vault Sidebar -->
        <div class="lg:col-span-1 order-1 lg:order-2">
            <div class="rounded-xl shadow-sm border p-3 md:p-4"
                 style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                
                <h3 class="text-base md:text-lg font-serif mb-3 md:mb-4" style="color: var(--text-primary);">Evidence Vault</h3>
                
                <!-- Upload Button -->
                <button @click="$refs.fileInput.click()"
                        class="w-full px-3 md:px-4 py-2 md:py-3 rounded-lg border-2 border-dashed transition-colors hover:border-gold mb-3 md:mb-4"
                        style="border-color: var(--border-color); color: var(--text-primary);">
                    <svg class="w-5 h-5 md:w-6 md:h-6 mx-auto mb-1 md:mb-2" style="color: var(--gold);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <span class="text-xs md:text-sm font-medium">Upload Evidence</span>
                    <p class="text-xs mt-1" style="color: var(--text-secondary);">PDF, DOCX, Images, Video</p>
                </button>
                <input type="file" x-ref="fileInput" class="hidden" @change="uploadFile">

                <!-- Uploaded Files -->
                <div class="space-y-2" x-show="files.length > 0">
                    <template x-for="file in files" :key="file.id">
                        <div class="p-2 md:p-3 rounded-lg border" style="background-color: var(--bg-primary); border-color: var(--border-color);">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start flex-1 min-w-0">
                                    <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 flex-shrink-0" :style="`color: ${file.icon.color};`" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs md:text-sm font-medium truncate" style="color: var(--text-primary);" x-text="file.filename"></p>
                                        <p class="text-xs" style="color: var(--text-secondary);" x-text="file.party + ' • ' + file.size"></p>
                                    </div>
                                </div>
                                <button @click="deleteFile(file.id)" 
                                        x-show="!file.is_locked"
                                        class="ml-2 p-1 rounded hover:bg-red-100 transition-colors flex-shrink-0"
                                        title="Delete file">
                                    <svg class="w-3 h-3 md:w-4 md:h-4" style="color: #DC2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                <!-- File Limit Info -->
                <div class="mt-3 md:mt-4 p-2 md:p-3 rounded-lg" style="background-color: rgba(201, 168, 76, 0.1);">
                    <p class="text-xs" style="color: var(--text-secondary);">
                        <strong style="color: var(--text-primary);" x-text="files.length + ' of 20'"></strong> files uploaded<br>
                        Max 20MB per file
                    </p>
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
        timeRemaining: 3600,
        files: [],
        uploading: false,
        
        init() {
            this.loadFiles();
            
            setInterval(() => {
                if (this.timeRemaining > 0) {
                    this.timeRemaining--;
                }
            }, 1000);
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
                console.error('Error loading files:', error);
            }
        },
        
        formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        },
        
        sendMessage() {
            if (this.messageInput.trim()) {
                console.log('Sending:', this.messageInput);
                this.messageInput = '';
            }
        },
        
        async uploadFile(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            if (file.size > 20 * 1024 * 1024) {
                showToast('File size must be less than 20MB', 'error');
                return;
            }
            
            const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/png', 'image/jpeg', 'video/mp4'];
            if (!allowedTypes.includes(file.type)) {
                showToast('Invalid file type. Allowed: PDF, DOCX, PNG, JPG, MP4', 'error');
                return;
            }
            
            this.uploading = true;
            
            const formData = new FormData();
            formData.append('file', file);
            if (this.token) {
                formData.append('token', this.token);
            }
            
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
                    this.files.push(data.file);
                    event.target.value = '';
                    showToast('File uploaded successfully!', 'success');
                } else {
                    showToast(data.message || 'Upload failed', 'error');
                }
            } catch (error) {
                console.error('Upload error:', error);
                showToast('Upload failed. Please try again.', 'error');
            } finally {
                this.uploading = false;
            }
        },
        
        async deleteFile(fileId) {
            if (!confirm('Are you sure you want to delete this file?')) return;
            
            try {
                const url = `/rooms/${this.roomUuid}/evidence/${fileId}${this.token ? '?token=' + this.token : ''}`;
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.files = this.files.filter(f => f.id !== fileId);
                    showToast('File deleted successfully', 'success');
                } else {
                    showToast(data.message || 'Delete failed', 'error');
                }
            } catch (error) {
                console.error('Delete error:', error);
                showToast('Delete failed. Please try again.', 'error');
            }
        }
    }
}
</script>
@endsection
