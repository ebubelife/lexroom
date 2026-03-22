@extends('layouts.app')

@section('title', 'Room Session — LexRoom')
@section('page-title', 'Live Session')

@section('content')
<div class="max-w-7xl mx-auto" x-data="liveRoom()">
    <!-- Session Header -->
    <div class="rounded-xl shadow-sm border p-4 mb-4"
         style="background-color: var(--bg-secondary); border-color: var(--border-color);">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-serif" style="color: var(--text-primary);">{{ ucfirst($room->category) }} Dispute</h2>
                <p class="text-sm" style="color: var(--text-secondary);">{{ $room->jurisdiction }}</p>
            </div>
            
            <!-- Timer -->
            <div class="text-center">
                <div class="text-3xl font-bold font-mono" style="color: var(--gold);" x-text="formatTime(timeRemaining)"></div>
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
        <div class="lg:col-span-3">
            <div class="rounded-xl shadow-sm border flex flex-col"
                 style="background-color: var(--bg-secondary); border-color: var(--border-color); height: calc(100vh - 250px);">
                
                <!-- Chat Messages -->
                <div class="flex-1 overflow-y-auto p-4 space-y-4" id="chat-messages">
                    <!-- Lex Welcome Message -->
                    <div class="w-full">
                        <div class="p-4 rounded-lg" style="background-color: rgba(201, 168, 76, 0.1); border-left: 4px solid var(--gold);">
                            <div class="flex items-center mb-2">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold mr-2"
                                     style="background-color: var(--gold);">L</div>
                                <span class="font-medium" style="color: var(--gold);">Lex AI Mediator</span>
                            </div>
                            <p style="color: var(--text-primary);">
                                Welcome to your mediation session. I'm Lex, your AI mediator. I've reviewed both case summaries. 
                                Let's begin with opening statements. <strong>Party A</strong>, please present your case first.
                            </p>
                        </div>
                    </div>

                    <!-- Party A Message (Blue - Left) -->
                    <div class="flex justify-start">
                        <div class="max-w-[70%]">
                            <div class="flex items-center mb-1">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-bold mr-2"
                                     style="background-color: #1D4ED8;">A</div>
                                <span class="text-xs font-medium" style="color: var(--text-secondary);">Party A</span>
                            </div>
                            <div class="p-3 rounded-lg rounded-tl-none" style="background-color: rgba(29, 78, 216, 0.1); border: 1px solid rgba(29, 78, 216, 0.2);">
                                <p class="text-sm" style="color: var(--text-primary);">
                                    I hired the defendant to build a website for my business. We agreed on ₦150,000 for the project...
                                </p>
                                <span class="text-xs" style="color: var(--text-secondary);">10:23 AM</span>
                            </div>
                        </div>
                    </div>

                    <!-- Party B Message (Purple - Right) -->
                    <div class="flex justify-end">
                        <div class="max-w-[70%]">
                            <div class="flex items-center justify-end mb-1">
                                <span class="text-xs font-medium mr-2" style="color: var(--text-secondary);">Party B</span>
                                <div class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-bold"
                                     style="background-color: #7E22CE;">B</div>
                            </div>
                            <div class="p-3 rounded-lg rounded-tr-none" style="background-color: rgba(126, 34, 206, 0.1); border: 1px solid rgba(126, 34, 206, 0.2);">
                                <p class="text-sm" style="color: var(--text-primary);">
                                    That's not accurate. The agreement was for ₦100,000, and I completed all the work as specified...
                                </p>
                                <span class="text-xs" style="color: var(--text-secondary);">10:25 AM</span>
                            </div>
                        </div>
                    </div>

                    <!-- Lex Analysis Message -->
                    <div class="w-full">
                        <div class="p-4 rounded-lg" style="background-color: rgba(201, 168, 76, 0.1); border-left: 4px solid var(--gold);">
                            <div class="flex items-center mb-2">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold mr-2"
                                     style="background-color: var(--gold);">L</div>
                                <span class="font-medium" style="color: var(--gold);">Lex AI Mediator</span>
                            </div>
                            <p style="color: var(--text-primary);">
                                I notice a discrepancy regarding the agreed amount. Party A claims ₦150,000 while Party B states ₦100,000. 
                                Do either of you have written documentation of the agreement? Please upload any contracts or communications to the Evidence Vault.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Message Input -->
                <div class="border-t p-4" style="border-color: var(--border-color);">
                    <div class="flex items-center space-x-2">
                        <input type="text" 
                               x-model="messageInput"
                               @keyup.enter="sendMessage"
                               placeholder="Type your message..."
                               class="flex-1 px-4 py-3 rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold"
                               style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
                        <button @click="sendMessage"
                                class="px-6 py-3 rounded-lg text-white font-medium transition-colors hover:opacity-90"
                                style="background-color: var(--gold);">
                            Send
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Evidence Vault Sidebar -->
        <div class="lg:col-span-1">
            <div class="rounded-xl shadow-sm border p-4"
                 style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                
                <h3 class="text-lg font-serif mb-4" style="color: var(--text-primary);">Evidence Vault</h3>
                
                <!-- Upload Button -->
                <button @click="$refs.fileInput.click()"
                        class="w-full px-4 py-3 rounded-lg border-2 border-dashed transition-colors hover:border-gold mb-4"
                        style="border-color: var(--border-color); color: var(--text-primary);">
                    <svg class="w-6 h-6 mx-auto mb-2" style="color: var(--gold);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <span class="text-sm font-medium">Upload Evidence</span>
                    <p class="text-xs mt-1" style="color: var(--text-secondary);">PDF, DOCX, Images, Video</p>
                </button>
                <input type="file" x-ref="fileInput" class="hidden" @change="uploadFile">

                <!-- Uploaded Files -->
                <div class="space-y-2">
                    <div class="p-3 rounded-lg border" style="background-color: var(--bg-primary); border-color: var(--border-color);">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start flex-1">
                                <svg class="w-5 h-5 mr-2 flex-shrink-0" style="color: #DC2626;" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate" style="color: var(--text-primary);">contract.pdf</p>
                                    <p class="text-xs" style="color: var(--text-secondary);">Party A • 2.3 MB</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 rounded-lg border" style="background-color: var(--bg-primary); border-color: var(--border-color);">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start flex-1">
                                <svg class="w-5 h-5 mr-2 flex-shrink-0" style="color: #2563EB;" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate" style="color: var(--text-primary);">invoice.docx</p>
                                    <p class="text-xs" style="color: var(--text-secondary);">Party B • 1.1 MB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- File Limit Info -->
                <div class="mt-4 p-3 rounded-lg" style="background-color: rgba(201, 168, 76, 0.1);">
                    <p class="text-xs" style="color: var(--text-secondary);">
                        <strong style="color: var(--text-primary);">2 of 20</strong> files uploaded<br>
                        Max 20MB per file
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function liveRoom() {
    return {
        messageInput: '',
        timeRemaining: 3600, // 60 minutes in seconds
        
        init() {
            // Start countdown timer
            setInterval(() => {
                if (this.timeRemaining > 0) {
                    this.timeRemaining--;
                }
            }, 1000);
        },
        
        formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        },
        
        sendMessage() {
            if (this.messageInput.trim()) {
                // TODO: Send message via WebSocket
                console.log('Sending:', this.messageInput);
                this.messageInput = '';
            }
        },
        
        uploadFile(event) {
            const file = event.target.files[0];
            if (file) {
                // TODO: Upload file
                console.log('Uploading:', file.name);
            }
        }
    }
}
</script>
@endsection
