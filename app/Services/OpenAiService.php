<?php

namespace App\Services;

use App\Contracts\AiProviderInterface;
use App\Traits\BuildsMediatorPrompt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiService implements AiProviderInterface
{
    use BuildsMediatorPrompt;

    protected string $apiKey;
    protected string $model;
    protected string $baseUrl = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key', '');
        $this->model = config('services.openai.model', 'gpt-4o');
    }

    public function generateResponse(array $conversationHistory, array $context = []): array
    {
        $systemPrompt = $this->buildSystemPrompt($context);
        $messages = $this->formatMessages($conversationHistory);

        // OpenAI expects the system prompt as the first message
        array_unshift($messages, [
            'role' => 'system',
            'content' => $systemPrompt,
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 2048,
                'messages' => $messages,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message' => $data['choices'][0]['message']['content'] ?? '',
                    'usage' => $data['usage'] ?? null,
                ];
            }

            Log::error('OpenAI API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => 'Failed to get response from OpenAI API',
            ];
        } catch (\Exception $e) {
            Log::error('OpenAI API Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function analyzeEvidence(string $evidenceText, array $context = []): array
    {
        $prompt = "Analyze the following evidence document and extract key facts, dates, and relevant information:\n\n{$evidenceText}";

        return $this->generateResponse([
            ['sender_type' => 'party_a', 'content' => $prompt],
        ], $context);
    }

    public function generateMediationReport(array $conversationHistory, array $evidence, array $context = []): array
    {
        $reportPrompt = <<<PROMPT
Based on the entire mediation session, generate a comprehensive Mediation Report with the following sections:

1. **Case Summary**: Brief overview of the dispute
2. **Party A's Position**: Key arguments and evidence presented
3. **Party B's Position**: Key arguments and evidence presented
4. **Evidence Reviewed**: List and summary of all uploaded documents
5. **Factual Findings**: Objective facts established during the session
6. **Contradictions Identified**: Any inconsistencies between statements and evidence
7. **Legal Framework**: Relevant laws and principles from {$context['jurisdiction']}
8. **Resolution Recommendation**: Suggested fair resolution
9. **Confidence Score**: Your confidence in the recommendation (0-100%)
10. **Next Steps**: Recommended actions for both parties

**Important**: Include a disclaimer that this is AI-generated guidance, not legal advice, and parties should consult qualified lawyers for binding legal opinions.

Format the report professionally and comprehensively.
PROMPT;

        $messages = $this->formatMessages($conversationHistory);
        $messages[] = [
            'role' => 'user',
            'content' => $reportPrompt,
        ];

        return $this->generateResponse($messages, $context);
    }
}
