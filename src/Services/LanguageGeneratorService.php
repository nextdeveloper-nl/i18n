<?php

namespace NextDeveloper\I18n\Services;

use Google\Cloud\Core\Exception\ServiceException;
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
class LanguageGeneratorService {
    public static function generate() {
        $langs = Languages::withoutGlobalScopes()->get();

        $domain = Domains::withoutGlobalScopes()
            ->where('uuid', config('i18n.domain.id'))
            ->first();

        $availableLanguages = [];

        foreach ($langs as $lang) {
            $translations = I18nTranslation::withoutGlobalScopes()
                ->where('common_domain_id', $domain->id)
                ->where('common_language_id', $lang->id)
                ->get();

            if(count($translations) < 1)
                continue;

            $availableLanguages[] = $lang;

            $json = [];

            foreach ($translations as $translation) {
                $json[$translation['text']] = $translation['translation'];
            }

            $file = config('i18n.translations.folder') . trim($lang->code) . '.json';

            file_put_contents($file, json_encode($json, JSON_UNESCAPED_UNICODE));
        }

        $availableLanguagesFile = config('i18n.translations.folder') . 'languages.json';

        file_put_contents($availableLanguagesFile, json_encode($availableLanguages, JSON_UNESCAPED_UNICODE));
    }
}
