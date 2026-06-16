<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClaudeService
{
    protected $apiKey;
    protected $model;
    protected $baseUrl = 'https://api.anthropic.com/v1/messages';

    public function __construct()
    {
        $this->apiKey = config('services.claude.api_key');
        $this->model = config('services.claude.model', 'claude-3-7-sonnet-20250219');
    }

    /**
     * Generate Lex AI response based on conversation history
     */
    public function generateResponse(array $conversationHistory, array $context = [])
    {
        $systemPrompt = $this->buildSystemPrompt($context);
        
        $messages = $this->formatMessages($conversationHistory);

        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 2048,
                'system' => $systemPrompt,
                'messages' => $messages,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message' => $data['content'][0]['text'] ?? '',
                    'usage' => $data['usage'] ?? null,
                ];
            }

            Log::error('Claude API Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'success' => false,
                'error' => 'Failed to get response from Claude API',
            ];

        } catch (\Exception $e) {
            Log::error('Claude API Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Build system prompt based on dispute context
     */
    protected function buildSystemPrompt(array $context): string
    {
        $category = $context['category'] ?? 'general';
        $jurisdiction = $context['jurisdiction'] ?? 'Nigeria';
        $language = $context['language'] ?? 'English';
        $caseSummaryA = $context['case_summary_a'] ?? '';
        $caseSummaryB = $context['case_summary_b'] ?? '';
        
        $evidenceSection = "";
        if (!empty($context['evidence_texts'])) {
            $evidenceSection = "\n**Documentary Evidence (Extracted Text):**\n" . $context['evidence_texts'] . "\n\nCRITICAL INSTRUCTION: You MUST cross-reference user statements against this documentary evidence. If a user contradicts the evidence, flag it respectfully.";
        }

        return <<<PROMPT
You are FM, an impartial AI mediator for FirstMediator, a dispute resolution platform. Your role is to facilitate fair and constructive dialogue between two parties to help them reach a mutually acceptable resolution.

**Session Context:**
- Dispute Category: {$category}
- Jurisdiction: {$jurisdiction}
- Session Language: {$language}

**Party A's Case Summary:**
{$caseSummaryA}

**Party B's Case Summary:**
{$caseSummaryB}
{$evidenceSection}

**Your Responsibilities:**
1. Remain completely neutral and impartial at all times
2. Moderate turn-taking - ensure both parties have equal opportunity to speak
3. Ask clarifying questions to understand both perspectives
4. Identify contradictions between statements and evidence
5. Apply relevant legal principles from {$jurisdiction} jurisdiction
6. Guide parties toward a fair resolution
7. Generate a final mediation report with findings and recommendations

**Important Guidelines:**
- Always identify yourself as an AI tool, not a lawyer or judge
- Your recommendations are not legally binding
- Encourage parties to seek legal counsel for complex matters
- Maintain a professional, respectful tone
- Focus on facts and evidence, not emotions
- Flag any contradictions you notice
- Ask targeted questions to clarify ambiguities

**Communication Style:**
- Clear and concise
- Professional but approachable
- Use {$language} language
- Avoid legal jargon unless necessary
- Be empathetic but maintain neutrality

Begin by acknowledging both parties' positions and guiding them through the mediation process.
PROMPT;
    }

    /**
     * Format conversation history for Claude API
     */
    protected function formatMessages(array $conversationHistory): array
    {
        $messages = [];
        
        foreach ($conversationHistory as $message) {
            $senderType = $message['sender_type'] ?? $message['sender'] ?? 'user';
            $role = $senderType === 'lex' ? 'assistant' : 'user';
            $content = $message['content'];
            
            // If message is from a party, prefix with party identifier
            if ($role !== 'assistant') {
                $partyLabel = $senderType === 'party_a' ? 'Party A' : 'Party B';
                $content = "[{$partyLabel}]: {$content}";
            }
            
            $messages[] = [
                'role' => $role,
                'content' => $content,
            ];
        }

        return $messages;
    }

    /**
     * Analyze uploaded evidence
     */
    public function analyzeEvidence(string $evidenceText, array $context = []): array
    {
        $prompt = "Analyze the following evidence document and extract key facts, dates, and relevant information:\n\n{$evidenceText}";
        
        return $this->generateResponse([
            ['sender_type' => 'party_a', 'content' => $prompt]
        ], $context);
    }

    /**
     * Generate final mediation report
     */
    public function generateMediationReport(array $conversationHistory, array $evidence, array $context = []): array
    {
        $systemPrompt = $this->buildSystemPrompt($context);
        
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
