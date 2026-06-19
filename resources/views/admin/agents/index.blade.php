@extends('admin.layouts.app')

@section('title', 'AI Agents')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-serif" style="color: var(--gold);">AI Agents</h1>
        <p class="mt-1 text-sm" style="color: var(--text-secondary);">
            Choose which AI provider powers FM, the mediation assistant. Changes take effect immediately for all new messages.
        </p>
    </div>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-lg text-sm font-medium"
             style="background: rgba(34,197,94,0.12); border: 1px solid rgba(34,197,94,0.3); color: #4ade80;">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.agents.update') }}">
        @csrf
        @method('PUT')

        {{-- Provider selector --}}
        <div class="mb-6">
            <p class="text-xs font-semibold uppercase tracking-widest mb-3" style="color: var(--text-secondary);">Active Provider</p>
            <div class="grid grid-cols-2 gap-4">

                {{-- Claude card --}}
                <label class="relative cursor-pointer">
                    <input type="radio" name="active_provider" value="claude"
                           class="sr-only peer" {{ $activeProvider === 'claude' ? 'checked' : '' }}>
                    <div class="p-5 rounded-xl border-2 transition-all peer-checked:border-yellow-500/60 peer-checked:bg-yellow-500/5"
                         style="background: var(--bg-card); border-color: var(--border-color);">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                                     style="background: rgba(201,168,76,0.15);">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="#C9A84C" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold" style="color: var(--text-primary);">Claude</p>
                                    <p class="text-xs" style="color: var(--text-secondary);">Anthropic</p>
                                </div>
                            </div>
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $claudeKeySet ? 'text-green-400' : 'text-red-400' }}"
                                  style="{{ $claudeKeySet ? 'background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.2);' : 'background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2);' }}">
                                {{ $claudeKeySet ? 'Key set' : 'No key' }}
                            </span>
                        </div>
                        <div class="peer-checked:block">
                            <p class="text-xs mb-1" style="color: var(--text-secondary);">Model</p>
                            <input type="text" name="claude_model" value="{{ old('claude_model', $claudeModel) }}"
                                   placeholder="claude-sonnet-4-6"
                                   class="w-full px-3 py-2 rounded-lg text-sm font-mono"
                                   style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary); outline: none;">
                        </div>
                    </div>
                    {{-- Selected indicator --}}
                    <div class="absolute top-4 right-4 hidden peer-checked:flex w-4 h-4 rounded-full items-center justify-center"
                         style="background: var(--gold);">
                        <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </label>

                {{-- OpenAI card --}}
                <label class="relative cursor-pointer">
                    <input type="radio" name="active_provider" value="openai"
                           class="sr-only peer" {{ $activeProvider === 'openai' ? 'checked' : '' }}>
                    <div class="p-5 rounded-xl border-2 transition-all peer-checked:border-green-500/60 peer-checked:bg-green-500/5"
                         style="background: var(--bg-card); border-color: var(--border-color);">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                                     style="background: rgba(16,185,129,0.15);">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold" style="color: var(--text-primary);">ChatGPT</p>
                                    <p class="text-xs" style="color: var(--text-secondary);">OpenAI</p>
                                </div>
                            </div>
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $openaiKeySet ? 'text-green-400' : 'text-red-400' }}"
                                  style="{{ $openaiKeySet ? 'background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.2);' : 'background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2);' }}">
                                {{ $openaiKeySet ? 'Key set' : 'No key' }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs mb-1" style="color: var(--text-secondary);">Model</p>
                            <input type="text" name="openai_model" value="{{ old('openai_model', $openaiModel) }}"
                                   placeholder="gpt-4o"
                                   class="w-full px-3 py-2 rounded-lg text-sm font-mono"
                                   style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary); outline: none;">
                        </div>
                    </div>
                    <div class="absolute top-4 right-4 hidden peer-checked:flex w-4 h-4 rounded-full items-center justify-center"
                         style="background: #10b981;">
                        <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </label>

            </div>
        </div>

        {{-- API key hint --}}
        <div class="mb-6 px-4 py-3 rounded-lg text-xs" style="background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); color: var(--text-secondary);">
            API keys are set via environment variables (<code class="font-mono text-xs" style="color: var(--gold);">CLAUDE_API_KEY</code> / <code class="font-mono text-xs" style="color: var(--gold);">OPENAI_API_KEY</code>) — not stored here. Model names and the active provider are saved to the database and override the env defaults.
        </div>

        @if($errors->any())
            <div class="mb-4 px-4 py-3 rounded-lg text-sm" style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #f87171;">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex justify-end">
            <button type="submit"
                    class="px-5 py-2.5 rounded-lg text-sm font-semibold transition-all hover:opacity-90"
                    style="background: var(--gold); color: var(--navy);">
                Save Changes
            </button>
        </div>

    </form>
</div>
@endsection
