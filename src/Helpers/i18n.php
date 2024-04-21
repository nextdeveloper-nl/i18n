<?php

namespace NextDeveloper\I18n\Helpers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use NextDeveloper\Commons\Database\Models\Languages;
use NextDeveloper\I18n\Services\I18nTranslationService;
use NextDeveloper\IAM\Helpers\UserHelper;

class i18n
{
    public static function t($text, $toLang = null, $domainId = null) : string
    {
        if(Str::isUuid($toLang))
            $toLang = Languages::where('id', $toLang)->first()->code;

        if(!$toLang)
            $toLang = App::getLocale();

        if(!config('app.translation_enabled'))
            return $text;

        /**
         * @todo: We should find a more clever way to process this. Because if browser sends this locale;
         * "en-US,en;q=0.5" we should be able to work with this also!
         */
        $isLanguageAvailable = Languages::withoutGlobalScopes()->where('code', $toLang)->first();

        if(!$isLanguageAvailable)
            $toLang = 'en';

        //  Burada gelen text'i alıp hash'leyip, veritabanına kayıt edeceğiz.
        $translated = I18nTranslationService::translate($text, $toLang, $domainId);

        return $translated['translation'];
    }
}
