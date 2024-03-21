<?php

namespace NextDeveloper\I18n\Services;

use Google\Cloud\Core\Exception\ServiceException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use NextDeveloper\Commons\Database\Models\Domains;
use NextDeveloper\Commons\Database\Models\Languages;
use NextDeveloper\I18n\Database\Models\I18nTranslation;
use NextDeveloper\I18n\Services\AbstractServices\AbstractI18nTranslationService;
use NextDeveloper\I18n\Services\TranslationServices\GoogleTranslationService;

/**
* This class is responsible from managing the data for I18nTranslation
*
* Class I18nTranslationService.
*
* @package NextDeveloper\I18n\Database\Models
*/
class I18nTranslationService extends AbstractI18nTranslationService {

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE


    /**
     * Translate a given text to the specified locale using the configured translator model.
     *
     * @param array $data
     * @param string $toLocale The target locale for translation. Default is 'en'.
     *
     * @return I18nTranslation The translated text.
     * @throws ServiceException
     */
    public static function translate($data, $toLocale = 'en', $domainId = null): I18nTranslation
    {
        //  Added here as a bug fix, and to be able to use with multiple services
        if(!is_array($data)){
            $temp['text']   =   $data;
            $data   =   $temp;
        }

        if(!$toLocale)
            $toLocale = App::getLocale();

        // Generate a hash for the input text to check if translation is already stored.
        $hashText = hash('xxh3', $data['text']);

        // Append the locale to the hash to make it unique.
        $hashTextWithLocale = $toLocale . $hashText;

        // Check if the translation for the text already exists in the database.
        $checkTranslation =  self::getByHash($hashTextWithLocale);

        // If translation exists, return it.
        if ($checkTranslation) {
            return $checkTranslation;
        }

        // Get the configured translator model from the application settings.
        $translatorModel = config('i18n.translator.default_model');

        // Instantiate the translator based on the configured model.
        switch ($translatorModel) {
            default:
                $translator = new GoogleTranslationService();
                break;
        }

        try {
            // Translate the text using the selected translator.
            $translation = $translator->translate($data['text'], $toLocale);
        } catch (ServiceException $e) {
            // If translation fails, return the original text.
            if($e->getCode() == 403) {
                Log::error('[i18n\TranslationService\translate] Cannot translate because: ' . $e->getMessage());
            }

            return $data['text'];
        }

        // Get Language ID
        // TODO: Should be refactored to use the LanguageService
        $language = Languages::withoutGlobalScopes()->where('iso_639_1_code', $toLocale)->first();

        $domain = null;

        if($domainId != null) {
            $domain = Domains::withoutGlobalScopes()->where('uuid', $domainId)->first();
        }

        $data = [
            'hash'          => $hashTextWithLocale,
            'translation'   => $translation,
            'text'          => $data['text'],
            'common_language_id'   => $language->id,
        ];

        if($domain) {
            $data['common_domain_id']   =   $domain->id;
        }

        // Store the translated text in the database for future use.
        $storeTranslatedText = self::create($data);

        // Return the translated Model.
        return $storeTranslatedText;
    }

    /**
     * Get the translation from the database based on the provided hash.
     *
     * @param string $hash The hash of the text for which translation is requested.
     *
     * @return I18nTranslation|null The translation object if found, null otherwise.
     */
    public static function getByHash(string $hash): ?I18nTranslation
    {
        return I18nTranslation::withoutGlobalScopes()->where('hash', $hash)->first();
    }
}
