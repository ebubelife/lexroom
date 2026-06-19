<?php

namespace App\Contracts;

interface AiProviderInterface
{
    public function generateResponse(array $conversationHistory, array $context = []): array;

    public function analyzeEvidence(string $evidenceText, array $context = []): array;

    public function generateMediationReport(array $conversationHistory, array $evidence, array $context = []): array;
}
