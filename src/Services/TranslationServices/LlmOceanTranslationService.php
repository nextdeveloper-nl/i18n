<?php

namespace NextDeveloper\I18n\Services\TranslationServices;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/**
 * Service class for translating text using the LLM Ocean API.
 *
 * LLM Ocean exposes an OpenAI compatible chat completions endpoint, so the request
 * and response shapes match OpenAITranslationService. The backend is a pool of
 * on demand agents, which means it may answer with 502/503 while a model is waking
 * up, so transient failures are retried with an exponential backoff.
 */
class LlmOceanTranslationService
{
    /**
     * @var Client The HTTP client.
     */
    protected Client $client;

    /**
     * @var string The LLM Ocean model to use.
     */
    protected string $model;

    /**
     * @var int Maximum tokens in the response.
     */
    protected int $maxTokens;

    /**
     * @var float Sampling temperature.
     */
    protected float $temperature;

    /**
     * @var int Number of retries for transient failures.
     */
    protected int $retries;

    /**
     * @var int Base delay in milliseconds between retries.
     */
    protected int $retryDelayMs;

    /**
     * LlmOceanTranslationService constructor.
     *
     * @throws \Exception If the LLM Ocean API is not properly configured.
     */
    public function __construct(?Client $client = null)
    {
        if (
            !config('i18n.services.llmocean.url')
            || !config('i18n.services.llmocean.key')
            || !config('i18n.services.llmocean.model')
        ) {
            throw new \Exception('LLM Ocean Translation API is not configured properly.');
        }

        $this->model        = (string) config('i18n.services.llmocean.model');
        $this->maxTokens    = (int) config('i18n.services.llmocean.max_tokens', 8192);
        $this->temperature  = (float) config('i18n.services.llmocean.temperature', 0.3);
        $this->retries      = max(0, (int) config('i18n.services.llmocean.retries', 2));
        $this->retryDelayMs = max(0, (int) config('i18n.services.llmocean.retry_delay_ms', 500));

        $this->client = $client ?? new Client([
            'base_uri'        => rtrim((string) config('i18n.services.llmocean.url'), '/') . '/',
            'timeout'         => (int) config('i18n.services.llmocean.timeout', 120),
            'connect_timeout' => (int) config('i18n.services.llmocean.connect_timeout', 30),
            'headers'         => [
                'Authorization' => 'Bearer ' . config('i18n.services.llmocean.key'),
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ],
            // Keep http_errors=false to inspect responses explicitly and control retries
            'http_errors'     => false,
        ]);
    }

    /**
     * Translates the given text to the specified target locale.
     *
     * @param string      $text         The text to be translated.
     * @param string      $targetLocale The target locale based on ISO-639-1 code.
     * @param string|null $sourceLocale The source locale based on ISO-639-1 code (optional).
     * @return string The translated text, or the original text if translation fails.
     */
    public function translate(string $text, string $targetLocale, ?string $sourceLocale = null): string
    {
        if ($text === '') {
            return '';
        }

        $targetLocale = trim($targetLocale);

        if ($targetLocale === '') {
            Log::warning('[i18n.LlmOceanTranslationService] Empty target locale provided; returning original text.');
            return $text;
        }

        // If source and target locales are the same, return the original text without translation
        if ($sourceLocale !== null && $this->normalizeLocale($sourceLocale) === $this->normalizeLocale($targetLocale)) {
            return $text;
        }

        $payload = [
            'model'       => $this->model,
            'messages'    => [
                [
                    'role'    => 'system',
                    'content' => $this->buildTranslationPrompt($targetLocale, $sourceLocale),
                ],
                [
                    'role'    => 'user',
                    'content' => $text,
                ],
            ],
            'temperature' => $this->temperature,
            'max_tokens'  => $this->maxTokens,
            'top_p'       => 1.0,
        ];

        $attempt = 0;

        while (true) {
            try {
                $response = $this->client->post('chat/completions', [
                    'json' => $payload,
                ]);

                $status = $response->getStatusCode();
                $body   = (string) $response->getBody();

                // Retry only on transient conditions: 429 or 5xx (agent waking up, rate limits)
                if ($status === 429 || $status >= 500) {
                    if ($attempt < $this->retries) {
                        usleep($this->computeBackoffDelayUs($attempt));
                        $attempt++;
                        continue;
                    }

                    Log::error("[i18n.LlmOceanTranslationService] Translation failed after retries. HTTP {$status}. Body: {$body}");
                    return $text;
                }

                if ($status < 200 || $status >= 300) {
                    Log::error("[i18n.LlmOceanTranslationService] Translation request failed. HTTP {$status}. Body: {$body}");
                    return $text;
                }

                $result = json_decode($body, true);

                if (!is_array($result) || !isset($result['choices'][0]['message']['content'])) {
                    Log::error('[i18n.LlmOceanTranslationService] Unexpected response structure: ' . $body);
                    return $text;
                }

                $translation = $this->cleanTranslation((string) $result['choices'][0]['message']['content']);

                return $translation !== '' ? $translation : $text;
            } catch (\Throwable $e) {
                // Network/timeout/DNS etc: retry if allowed, otherwise log and return original
                if ($attempt < $this->retries) {
                    usleep($this->computeBackoffDelayUs($attempt));
                    $attempt++;
                    continue;
                }

                Log::error('[i18n.LlmOceanTranslationService] Translation request threw exception after retries: ' . $e->getMessage(), [
                    'exception' => $e,
                ]);

                return $text;
            }
        }
    }

    /**
     * Removes reasoning blocks that some open weight models emit before the answer.
     *
     * @param string $translation The raw model output.
     * @return string The cleaned translation.
     */
    protected function cleanTranslation(string $translation): string
    {
        $translation = preg_replace('/<think>.*?<\/think>/is', '', $translation) ?? $translation;

        return trim($translation);
    }

    /**
     * Computes the exponential backoff delay in microseconds for the given attempt.
     *
     * @param int $attempt The zero based attempt number.
     * @return int The delay in microseconds.
     */
    protected function computeBackoffDelayUs(int $attempt): int
    {
        return $this->retryDelayMs * (2 ** $attempt) * 1000;
    }

    /**
     * Builds an optimized translation prompt for the AI model.
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
     * Handles variations like 'en', 'en_US', 'en-US' etc.
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
