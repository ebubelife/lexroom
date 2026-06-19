<?php

namespace App\Traits;

trait BuildsMediatorPrompt
{
    protected function buildSystemPrompt(array $context): string
    {
        $category = $context['category'] ?? 'general';
        $jurisdiction = $context['jurisdiction'] ?? 'Nigeria';
        $language = $context['language'] ?? 'English';
        $caseSummaryA = $context['case_summary_a'] ?? '';
        $caseSummaryB = $context['case_summary_b'] ?? '';

        $evidenceSection = '';
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

    protected function formatMessages(array $conversationHistory): array
    {
        $messages = [];

        foreach ($conversationHistory as $message) {
            $senderType = $message['sender_type'] ?? $message['sender'] ?? 'user';
            $role = $senderType === 'lex' ? 'assistant' : 'user';
            $content = $message['content'];

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
}
