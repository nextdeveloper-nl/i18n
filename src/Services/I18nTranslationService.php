<?php

namespace NextDeveloper\I18n\Services;

use NextDeveloper\I18n\Services\AbstractServices\AbstractI18nTranslationService;

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
     * This function translates the given text into the locale we asked for.
     *
     * @param $text
     * @param $toLocale
     * @return void
     */
    public static function translate($text, $toLocale = 'en') {
        //  Burada default locale'ı alacaksın ilk
        //  Eğer burada şöyle bir translation talebi gelirse;
        //  translate("Hello world", "tr");
        //  O zaman
        //  1) $hash = hash("Hello world")
        //  2) $checkTranslate = I18nService::getByHash(....);
        //  3) if($checkTranslate == null) save to database
        //  4) translate with AI
        //  5) return translated version
    }
}