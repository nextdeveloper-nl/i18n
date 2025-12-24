<?php

namespace NextDeveloper\I18n\Services\TranslationServices;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/**
 * Service class for translating text using LeoTranslator API.
 *
 */
class LeoTransService
{
    private Client $client;
    private int $timeout;
    private int $connectTimeout;
    private int $retries;
    private int $retryDelayMs;

    public function __construct(?Client $client = null)
    {
        $apiKey  = (string) config('i18n.services.leotranslator.key');
        $baseUrl = rtrim((string) config('i18n.services.leotranslator.url'), '/');

        if ($apiKey === '' || $baseUrl === '') {
            throw new \InvalidArgumentException(__METHOD__ . ': leotranslator API key or URL is not set in the configuration.');
        }

        // Configurable timeouts/retries with safe defaults
        $this->timeout        = (int) config('i18n.services.leotranslator.timeout', 10);
        $this->connectTimeout = (int) config('i18n.services.leotranslator.connect_timeout', 5);
        $this->retries        = max(0, (int) config('i18n.services.leotranslator.retries', 2));
        $this->retryDelayMs   = max(0, (int) config('i18n.services.leotranslator.retry_delay_ms', 200));

        $this->client = $client ?? new Client([
            'base_uri'    => $baseUrl,
            'headers'     => [
                'X-API-KEY'    => $apiKey,
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
            // Keep http_errors=false to inspect responses explicitly and control retries
            'http_errors' => false,
        ]);
    }

    /**
     * Translate text to the given target language.
     * - Returns empty string for empty input.
     * - On any failure, logs error and returns the original text (consistent with other services).
     */
    public function translate(string $text, string $targetLanguage, ?string $sourceLanguage = 'auto'): string
    {

        if ($text === '') {
            return '';
        }

        $targetLanguage = trim($targetLanguage);
        if ($targetLanguage === '') {
            Log::warning('[i18n.LeoTransService] Empty target language provided; returning original text.');
            return $text;
        }

        $payload = [
            'q'      => $text,
            'source' => $sourceLanguage ?: 'auto',
            'target' => $targetLanguage,
        ];

        $attempt = 0;
        while (true) {
            try {
                $response = $this->client->post('/translate', [
                    'json'            => $payload,
                    'timeout'         => $this->timeout,
                    'connect_timeout' => $this->connectTimeout,
                ]);

                $status = $response->getStatusCode();
                $body   = (string) $response->getBody();

                // Retry only on transient conditions: 429 or 5xx
                if ($status === 429 || $status >= 500) {
                    if ($attempt < $this->retries) {
                        usleep($this->computeBackoffDelayUs($attempt));
                        $attempt++;
                        continue;
                    }
                    Log::error("[i18n.LeoTransService] Translation failed after retries. HTTP {$status}. Body: {$body}");
                    return $text;
                }

                // Non-success and non-retryable codes -> log and return original
                if ($status < 200 || $status >= 300) {
                    Log::error("[i18n.LeoTransService] Translation request failed. HTTP {$status}. Body: {$body}");
                    return $text;
                }

                $data = json_decode($body, true);
                if (!is_array($data) || !array_key_exists('translatedText', $data)) {
                    Log::error('[i18n.LeoTransService] Missing translatedText in response.', ['body' => $body]);
                    return $text;
                }

                $translated = (string) $data['translatedText'];
                return $translated !== '' ? $translated : $text;
            } catch (\Throwable $e) {
                // Network/timeout/DNS etc: retry if allowed, otherwise log and return original
                if ($attempt < $this->retries) {
                    usleep($this->computeBackoffDelayUs($attempt));
                    $attempt++;
                    continue;
                }
                Log::error('[i18n.LeoTransService] Translation request threw exception after retries: ' . $e->getMessage(), [
                    'exception' => $e,
                ]);
                return $text;
            }
        }
    }

    private function computeBackoffDelayUs(int $attempt): int
    {
        $delayMs = $this->retryDelayMs * (2 ** $attempt);
        return $delayMs * 1000;
    }
}
