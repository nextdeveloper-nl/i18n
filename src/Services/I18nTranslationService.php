<?php

namespace NextDeveloper\I18n\Services;

use Google\Cloud\Core\Exception\ServiceException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use NextDeveloper\Commons\Database\Models\Domains;
use NextDeveloper\Commons\Database\Models\Languages;
use NextDeveloper\I18n\Database\Filters\I18nTranslationQueryFilter;
use NextDeveloper\I18n\Database\Models\I18nTranslation;
use NextDeveloper\I18n\Services\AbstractServices\AbstractI18nTranslationService;
use NextDeveloper\I18n\Services\TranslationServices\GoogleTranslationService;
use NextDeveloper\I18n\Services\TranslationServices\LeoTransService;
use NextDeveloper\I18n\Services\TranslationServices\OpenAITranslationService;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;

/**
* This class is responsible for managing the data for I18nTranslation
*
* Class I18nTranslationService.
*
* @package NextDeveloper\I18n\Database\Models
*/
class I18nTranslationService extends AbstractI18nTranslationService {

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

    public static function get(I18nTranslationQueryFilter $filter = null, array $params = []): \Illuminate\Database\Eloquent\Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $filters = $filter->filters();

        if(array_key_exists('exactText', $filters)) {
            //  Check if the word exists in the database
            self::translate(
                data: $filters['exactText'],
                toLocale: $filters['locale'] ?? App::getLocale(),
                domainId: $filters['commonDomainId'] ?? null
            );
        }

        return parent::get($filter, $params);
    }

    /**
     * Translate a given text to the specified locale using the configured translator model.
     *
     * @param array $data
     * @param string $toLocale The target locale for translation. Default is 'en'.
     *
     * @throws ServiceException|\Exception
     */
    public static function translate($data, $toLocale = 'en', $domainId = null)
    {
        //  Added here as a bug fix, and to be able to use with multiple services
        if(!is_array($data)){
            $temp['text']   =   $data;
            $data   =   $temp;
        }

        if(!$toLocale)
            $toLocale = App::getLocale();

        $toLocale = trim($toLocale);

        // Generate a hash for the input text to check if translation is already stored.
        $hashText = hash('xxh3', $data['text']);

        // Append the locale to the hash to make it unique.
        $hashTextWithLocale = $toLocale . $hashText;

        // Check if the translation for the text already exists in the database.
        $checkTranslation =  self::getByHash($hashTextWithLocale);

        // If translation exists, return it.
        if ($checkTranslation) {
            $domain = Domains::withoutGlobalScope(AuthorizationScope::class)->where('uuid', $domainId)->first();

            $translationWithDomain = null;

            if($domain) {
                $translationWithDomain = I18nTranslation::where('hash', $hashTextWithLocale)
                    ->where('common_domain_id', $domain->id)
                    ->first();
            }

            if(!$translationWithDomain) {
                I18nTranslation::create([
                    'hash'          => $checkTranslation->hash,
                    'common_language_id'   => $checkTranslation->common_language_id,
                    'common_domain_id'     => $domain ? $domain->id : null,
                    'text'          => $checkTranslation->text,
                    'translation'   => $checkTranslation->translation,
                ]);
            }

            return $checkTranslation;
        }

        // Get the configured translator model from the application settings.
        $translatorModel = config('i18n.translator.default_model');

        // Instantiate the translator based on the configured model.
        $translator = match ($translatorModel) {
            'openai'        => new OpenAITranslationService(),
            'leotranslator' => new LeoTransService(),
            default         => new GoogleTranslationService(),
        };

        try {
            Log::debug('[i18n\TranslationService\translate] Using translator model: ' . $translatorModel . ' for text: ' . $data['text'] . ' to locale: ' . $toLocale);
            // Translate the text using the selected translator.
            $translation = $translator->translate($data['text'], trim($toLocale));
        } catch (ServiceException | GuzzleException $e) {
            Log::error('[i18n\TranslationService\translate] Cannot translate because: ' . $e->getMessage());
            return $data;
        }

        // If the original text is the same as the translated text, return the original text.
        if ($data['text'] === $translation) {
            //  We are removing this because the translator keeps translating the very same sentence to the very same
            //  language. To avoid this we need to cache the result.
            //return $data;
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
        return self::create($data);
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

    public static function getTranslations($domainId, $languageId): array
    {
        $translations = I18nTranslation::withoutGlobalScopes()
            ->where('common_domain_id', $domainId)
            ->where('common_language_id', $languageId)
            ->get();

        $keyedTranslations = [];

        foreach ($translations as $translation) {
            $keyedTranslations[$translation->text] = $translation->translation;
        }

        return $keyedTranslations;
    }
}
