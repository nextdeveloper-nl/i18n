<?php

namespace NextDeveloper\I18n\Services\TranslationServices;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

/**
 * Service class for translating text using the Anthropic Claude API.
 *
 * @see https://docs.anthropic.com/en/api/messages
 */
class ClaudeTranslationService
{
    /**
     * @var Client The HTTP client.
     */
    protected Client $client;

    /**
     * @var string The Claude model to use.
     */
    protected string $model;

    /**
     * @var int Maximum tokens in the response.
     */
    protected int $maxTokens;

    /**
     * ClaudeTranslationService constructor.
     *
     * @throws \Exception If the Claude API is not properly configured.
     */
    public function __construct()
    {
        if (
            !config('i18n.services.claude.url')
            || !config('i18n.services.claude.key')
            || !config('i18n.services.claude.model')
        ) {
            throw new \Exception('Claude Translation API is not configured properly.');
        }

        $this->model     = config('i18n.services.claude.model');
        $this->maxTokens = (int) config('i18n.services.claude.max_tokens', 8192);

        $this->client = new Client([
            'base_uri' => config('i18n.services.claude.url'),
            'timeout'  => 120,
            'connect_timeout' => 120,
            'headers'  => [
                'x-api-key'         => config('i18n.services.claude.key'),
                'anthropic-version' => config('i18n.services.claude.version', '2023-06-01'),
                'Content-Type'      => 'application/json',
            ],
        ]);
    }

    /**
     * Translates the given text to the specified target locale.
     *
     * @param string      $text         The text to be translated.
     * @param string      $targetLocale The target locale based on ISO-639-1 code.
     * @param string|null $sourceLocale The source locale based on ISO-639-1 code (optional).
     * @return string The translated text, or the original text if translation fails.
     * @throws GuzzleException
     */
    public function translate(string $text, string $targetLocale, ?string $sourceLocale = null): string
    {
        if ($sourceLocale !== null && $this->normalizeLocale($sourceLocale) === $this->normalizeLocale($targetLocale)) {
            return $text;
        }

        try {
            $systemPrompt = $this->buildTranslationPrompt($targetLocale, $sourceLocale);

            $response = $this->client->post('messages', [
                'json' => [
                    'model'      => $this->model,
                    'max_tokens' => $this->maxTokens,
                    'system'     => $systemPrompt,
                    'messages'   => [
                        [
                            'role'    => 'user',
                            'content' => $text,
                        ],
                    ],
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if ($result && isset($result['content'][0]['text'])) {
                return trim($result['content'][0]['text']);
            }

            Log::error('[i18n.ClaudeTranslationService] Unexpected response structure: ' . json_encode($result));
            return $text;

        } catch (GuzzleException $e) {
            Log::error('[i18n.ClaudeTranslationService] An error occurred while translating the text: ' . $e->getMessage());
            return $text;
        }
    }

    /**
     * Builds an optimized translation prompt for Claude.
     *
     * @param string      $targetLocale The target language code.
     * @param string|null $sourceLocale The source language code (optional).
     * @return string The system prompt for translation.
     */
    protected function buildTranslationPrompt(string $targetLocale, ?string $sourceLocale = null): string
    {
        $sourceInfo = $sourceLocale ? "from language code '$sourceLocale' " : '';

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
     *
     * @param string $locale The locale code to normalize.
     * @return string The normalized locale code (lowercase, primary language only).
     */
    protected function normalizeLocale(string $locale): string
    {
        $locale = strtolower(trim($locale));

        if (preg_match('/^([a-z]{2,3})[-_]/', $locale, $matches)) {
            return $matches[1];
        }

        return $locale;
    }
}
