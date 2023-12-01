<?php

namespace NextDeveloper\I18n\Services\TranslationServices;

use Google\Cloud\Core\Exception\GoogleException;
use Google\Cloud\Core\Exception\ServiceException;
use Google\Cloud\Translate\V2\TranslateClient;

/**
 * Class GoogleTranslationService
 *
 * Service class for translating text using Google Cloud Translation API.
 */
class GoogleTranslationService
{
    /**
     * @var TranslateClient The Google Cloud Translate client.
     */
    protected $client;

    /**
     * GoogleTranslatorModelService constructor.
     *
     * @throws GoogleException
     */
    public function __construct()
    {
        $this->client = new TranslateClient([
            'key' => config('i18n.services.google.translate.key')
        ]);
    }

    /**
     * Translate the given text to the specified locale.
     *
     * @param string $text The text to be translated.
     * @param string $locale The target locale for translation.
     *
     * @return string The translated text.
     * @throws ServiceException
     */
    public function translate(string $text, string $locale): string
    {
        $result = $this->client->translate($text, [
            'target' => $locale,
        ]);

        return $result['text'];
    }
}
