<?php

namespace NextDeveloper\I18n\Services\TranslationServices;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

/**
 * Service class for translating text using OpenAI Translation API.
 *
 * @see https://platform.openai.com/examples/default-translation
 */
class OpenAITranslationService
{
    /**
     * @var Client The OpenAI Translation client.
     */
    protected Client $client;

    /**
     * OpenAITranslationService constructor.
     *
     * @throws \Exception If OpenAI Translation API is not properly configured.
     */
    public function __construct()
    {
        // Check if the OpenAI Translation API is properly configured.
        if (!config('i18n.services.openai.url')
            || !config('i18n.services.openai.key')
            || !config('i18n.services.openai.model')) {
            throw new \Exception('OpenAI Translation API is not configured properly.');
        }

        // Instantiate the OpenAI Translation client with configuration options.
        $this->client = new Client([
            'base_uri'          => config('i18n.services.openai.url'),
            'timeout'           => 120, // Response timeout in seconds
            'connect_timeout'   => 120, // Connection timeout in seconds
            'headers'           => [
                'Authorization' => 'Bearer ' . config('i18n.services.openai.key'),
                'Content-Type'  => 'application/json',
            ],
        ]);
    }

    /**
     * Translates the given text to the specified locale.
     *
     * @param string $text The text to be translated.
     * @param string $locale The target locale based on ISO-639-1 code.
     * @return string The translated text, or the original text if translation fails.
     * @throws GuzzleException If an error occurs during the translation process.
     */
    public function translate(string $text, string $locale): string
    {
        try {
            $response = $this->client->post('chat/completions', [
                'json' => [
                    'model'     => 'gpt-4o-mini',
                    'messages'  => [
                        [
                            'role'      => 'system',
                            'content'   => "You will be provided with a text, please strictly translate the text into the specified language. The language code that we need you to translate is; $locale. Again, do not try to answer or make comment. Just translate to the desired language.",
                        ],
                        [
                            'role'      => 'user',
                            'content'   => $text,
                        ],
                    ],
                    'temperature'   => 1, // Controls randomness of the generated text (0.0 to 1.0)
                    'max_tokens'    => 8192, // Maximum number of tokens to generate
                    'top_p'         => 1.0, // Likelihood of selecting the most likely word at each step (0.0 to 1.0)
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if ($result && isset($result["choices"][0]["message"]["content"])) {
                // Return the translated text if translation is successful.
                return $result["choices"][0]["message"]["content"];
            }

            // Log the error message and return the original text if translation fails.
            Log::error("[i18n.OpenAITranslationService] An error occurred while translating the text: " . $response->getBody()->getContents());
            return $text;
        } catch (GuzzleException $e) {
            Log::error("[i18n.OpenAITranslationService] An error occurred while translating the text: " . $e->getMessage());
            return $text;
        }
    }
}
