<?php

namespace NextDeveloper\I18n\Services\TranslationServices;

use Google\Cloud\Translate\V3\Client\TranslationServiceClient;
use Google\ApiCore\ApiException;
use Google\Cloud\Translate\V3\TranslateTextRequest;

/**
 * GoogleTranslationService
 * Service class for translating text using the Google Cloud Translation V3 API.
 */
class GoogleTranslationService
{
    /**
     * The Google Cloud Translate V3 client.
     * @var TranslationServiceClient
     */
    protected $client;

    /**
     * The formatted parent resource name for the API.
     * @var string
     */
    protected string $parent;

    /**
     * Initializes the client and sets up the parent resource name.
     */
    public function __construct()
    {
        // Get the key file path from the configuration.
        $keyFilePath = config('i18n.services.google.translate.file_path');
        $keyFilePath = base_path($keyFilePath);

        if (empty($keyFilePath) || !file_exists($keyFilePath)) {
            throw new \InvalidArgumentException(__METHOD__ . 'Google Cloud credentials file path is not set or file does not exist.');
        }

        // Load the credentials JSON file content
        $credentialsJson = file_get_contents($keyFilePath);
        if ($credentialsJson === false) {
            throw new \InvalidArgumentException(__METHOD__ . 'Unable to read Google Cloud credentials file.');
        }

        $credentials = json_decode($credentialsJson, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException(__METHOD__ . 'Invalid JSON in Google Cloud credentials file.');
        }

        // Initialize the TranslationServiceClient with the credentials array.
        $this->client = new TranslationServiceClient([
            'credentials' => $credentials
        ]);

        $projectId = config('i18n.services.google.translate.project_id');
        if (empty($projectId)) {
            throw new \InvalidArgumentException(__METHOD__ . 'Google Cloud Project ID is not set in the configuration.');
        }
        // Set the location for the translation service.
        $location = config('i18n.services.google.translate.location');

        // Format the parent resource name required by the V3 API.
        $this->parent = TranslationServiceClient::locationName($projectId, $location);
    }

    /**
     * Translates the given text to the specified locale using API V3.
     *
     * @param string $text The text to be translated.
     * @param string $locale The target language code (e.g., 'fr', 'de').
     * @return string The translated text.
     */
    public function translate(string $text, string $locale): string
    {
        try {
            // Create a request object for the translation
            $request = (new TranslateTextRequest())
                ->setContents([$text])
                ->setTargetLanguageCode($locale)
                ->setParent($this->parent);

            // Call the method with the request object
            $response = $this->client->translateText($request);

            foreach ($response->getTranslations() as $translation) {
                return $translation->getTranslatedText();
            }

            return $text;

        } catch (ApiException $e) {
            error_log(__METHOD__ . 'Google Translation Error: ' . $e->getMessage());
            return $text; // Return the original text in case of an error
        }
    }

    /**
     * It's a good practice to close the client connection when you're done.
     */
    public function __destruct()
    {
        if ($this->client) {
            $this->client->close();
        }
    }
}
