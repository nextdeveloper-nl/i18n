<?php

namespace NextDeveloper\I18n\Services\TranslationServices;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

/**
 * Service class for translating text using a LiteLLM proxy (OpenAI-compatible chat completions API).
 *
 * @see https://docs.litellm.ai/docs/proxy/user_keys
 */
class LiteLLMTranslationService
{
    /**
     * @var Client The LiteLLM Translation client.
     */
    protected Client $client;

    /**
     * LiteLLMTranslationService constructor.
     *
     * @throws \Exception If the LiteLLM Translation API is not properly configured.
     */
    public function __construct()
    {
        // Check if the LiteLLM Translation API is properly configured.
        if (
            !config('i18n.services.litellm.url')
            || !config('i18n.services.litellm.key')
            || !config('i18n.services.litellm.model')
        ) {
            throw new \Exception('LiteLLM Translation API is not configured properly.');
        }

        // Instantiate the LiteLLM Translation client with configuration options.
        $this->client = new Client([
            'base_uri' => config('i18n.services.litellm.url'),
            'timeout' => 120, // Response timeout in seconds
            'connect_timeout' => 120, // Connection timeout in seconds
            'headers' => [
                'Authorization' => 'Bearer ' . config('i18n.services.litellm.key'),
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Translates the given text to the specified target locale.
     *
     * @param string $text The text to be translated.
     * @param string $targetLocale The target locale based on ISO-639-1 code.
     * @param string|null $sourceLocale The source locale based on ISO-639-1 code (optional).
     * @return string The translated text, or the original text if translation fails.
     * @throws GuzzleException If an error occurs during the translation process.
     */
    public function translate(string $text, string $targetLocale, ?string $sourceLocale = null): string
    {
        // If source and target locales are the same, return the original text without translation
        if ($sourceLocale !== null && $this->normalizeLocale($sourceLocale) === $this->normalizeLocale($targetLocale)) {
            return $text;
        }

        try {
            $systemPrompt = $this->buildTranslationPrompt($targetLocale, $sourceLocale);

            $response = $this->client->post('chat/completions', [
                'json' => [
                    'model' => config('i18n.services.litellm.model'),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $systemPrompt,
                        ],
                        [
                            'role' => 'user',
                            'content' => $text,
                        ],
                    ],
                    'temperature' => 0.3, // Low temperature for consistent, accurate translations
                    'max_tokens' => 8192,
                    'top_p' => 1.0,
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if ($result && isset($result["choices"][0]["message"]["content"])) {
                return trim($result["choices"][0]["message"]["content"]);
            }

            Log::error("[i18n.LiteLLMTranslationService] An error occurred while translating the text: " . $response->getBody()->getContents());
            return $text;
        } catch (GuzzleException $e) {
            Log::error("[i18n.LiteLLMTranslationService] An error occurred while translating the text: " . $e->getMessage());
            return $text;
        }
    }

    /**
     * Builds an optimized translation prompt for the AI model.
     *
     * @param string $targetLocale The target language code.
     * @param string|null $sourceLocale The source language code (optional).
     * @return string The system prompt for translation.
     */
    protected function buildTranslationPrompt(string $targetLocale, ?string $sourceLocale = null): string
    {
        $sourceInfo = $sourceLocale ? "from language code '$sourceLocale' " : "";

        return <<<PROMPT
You are a professional translator specializing in Cloud Service Provider (CSP) platforms and infrastructure software.

Context:
- All text originates from a cloud computing platform (similar to AWS, Azure, or Google Cloud)
- Users are IT professionals, DevOps engineers, system administrators, and cloud architects
- Terminology must match the standard cloud computing vocabulary used by major CSPs in the target language

Your task:
- Translate the provided text {$sourceInfo}to the language specified by ISO-639-1 code: '{$targetLocale}'
- Preserve the original meaning, tone, and intent of the text
- Maintain any formatting, punctuation, and special characters
- If the text contains placeholders (like :name, {variable}, %s), keep them exactly as they are

Terminology rules:
- Use established cloud computing industry terms in the target language wherever they exist
- If a cloud term has no widely adopted translation (e.g. "instance", "subnet", "snapshot", "load balancer", "namespace", "cluster", "node", "volume", "bucket", "pipeline", "deployment", "tenant", "firewall rule"), keep the English term as-is
- Never translate product names, brand names, or proper nouns (e.g. PlusClouds, Kubernetes, Terraform)
- Never translate code snippets, CLI commands, API keys, or technical identifiers
- Prefer formal/technical register over colloquial language — this is a B2B platform

Output rules:
- Output ONLY the translated text, nothing else
- Do NOT add explanations, comments, or notes
- Do NOT add quotation marks around the translation unless they were in the original
- If the text is already in the target language, return it unchanged
PROMPT;
    }

    /**
     * Normalizes locale codes for comparison.
     * Handles variations like 'en', 'en_US', 'en-US' etc.
     *
     * @param string $locale The locale code to normalize.
     * @return string The normalized locale code (lowercase, primary language only).
     */
    protected function normalizeLocale(string $locale): string
    {
        // Convert to lowercase and get the primary language code
        $locale = strtolower(trim($locale));

        // Handle formats like 'en_US', 'en-US' - extract primary language
        if (preg_match('/^([a-z]{2,3})[-_]/', $locale, $matches)) {
            return $matches[1];
        }

        return $locale;
    }
}
