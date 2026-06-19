<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function index()
    {
        $activeProvider = Setting::get('active_ai_provider', 'claude');
        $claudeModel    = Setting::get('active_claude_model', config('services.claude.model', 'claude-sonnet-4-6'));
        $openaiModel    = Setting::get('active_openai_model', config('services.openai.model', 'gpt-4o'));

        $claudeKeySet = !empty(config('services.claude.api_key'));
        $openaiKeySet = !empty(config('services.openai.api_key'));

        return view('admin.agents.index', compact(
            'activeProvider',
            'claudeModel',
            'openaiModel',
            'claudeKeySet',
            'openaiKeySet'
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

        // Sync the runtime config so the active session picks up the chosen model
        config(['services.claude.model' => $request->claude_model]);
        config(['services.openai.model' => $request->openai_model]);

        return back()->with('success', 'AI agent settings saved.');
    }
}
