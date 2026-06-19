<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AgentController extends Controller
{
    public function index()
    {
        $activeProvider = Setting::get('active_ai_provider', 'claude');
        $claudeModel    = Setting::get('active_claude_model', config('services.claude.model', 'claude-sonnet-4-6'));
        $openaiModel    = Setting::get('active_openai_model', config('services.openai.model', 'gpt-4o'));

        $claudeKeySet = !empty(config('services.claude.api_key'));
        $openaiKeySet = !empty(config('services.openai.api_key'));

        $claudeModels = $this->fetchClaudeModels();
        $openaiModels = $this->fetchOpenAIModels();

        return view('admin.agents.index', compact(
            'activeProvider',
            'claudeModel',
            'openaiModel',
            'claudeKeySet',
            'openaiKeySet',
            'claudeModels',
            'openaiModels',
        ));
    }

    public function update(Request $request)
    {
        $request->validate([
            'active_provider' => ['required', 'in:claude,openai'],
            'claude_model'    => ['required', 'string', 'max:100'],
            'openai_model'    => ['required', 'string', 'max:100'],
        ]);

        Setting::set('active_ai_provider', $request->active_provider);
        Setting::set('active_claude_model', $request->claude_model);
        Setting::set('active_openai_model', $request->openai_model);

        config(['services.claude.model' => $request->claude_model]);
        config(['services.openai.model' => $request->openai_model]);

        return back()->with('success', 'AI agent settings saved.');
    }

    private function fetchClaudeModels(): array
    {
        $fallback = [
            'claude-opus-4-8',
            'claude-sonnet-4-6',
            'claude-haiku-4-5-20251001',
            'claude-3-5-sonnet-20241022',
            'claude-3-5-haiku-20241022',
            'claude-3-opus-20240229',
        ];

        $apiKey = config('services.claude.api_key');
        if (empty($apiKey)) {
            return $fallback;
        }

        try {
            $response = Http::timeout(6)
                ->withHeaders([
                    'x-api-key'         => $apiKey,
                    'anthropic-version' => '2023-06-01',
                ])
                ->get('https://api.anthropic.com/v1/models');

            if ($response->successful()) {
                $ids = collect($response->json('data', []))
                    ->pluck('id')
                    ->filter()
                    ->sort()
                    ->values()
                    ->all();

                return !empty($ids) ? $ids : $fallback;
            }
        } catch (\Throwable) {
            // fall through to fallback
        }

        return $fallback;
    }

    private function fetchOpenAIModels(): array
    {
        $fallback = [
            'gpt-4o',
            'gpt-4o-mini',
            'gpt-4-turbo',
            'gpt-4',
            'o1',
            'o1-mini',
            'o3',
            'o3-mini',
        ];

        $apiKey = config('services.openai.api_key');
        if (empty($apiKey)) {
            return $fallback;
        }

        try {
            $response = Http::timeout(6)
                ->withToken($apiKey)
                ->get('https://api.openai.com/v1/models');

            if ($response->successful()) {
                $ids = collect($response->json('data', []))
                    ->pluck('id')
                    ->filter(fn ($id) => preg_match('/^(gpt-|o\d)/i', $id))
                    ->sort()
                    ->values()
                    ->all();

                return !empty($ids) ? $ids : $fallback;
            }
        } catch (\Throwable) {
            // fall through to fallback
        }

        return $fallback;
    }
}
