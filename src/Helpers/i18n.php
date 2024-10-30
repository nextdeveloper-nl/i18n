<?php

namespace NextDeveloper\I18n\Helpers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use NextDeveloper\Commons\Database\Models\Languages;
use NextDeveloper\I18n\Services\I18nTranslationService;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;

class i18n
{
    public static function translateWithoutSaving($text, $toLang = null) : string
    {
        if(!config('app.translation_enabled'))
            return $text;

        if(Str::isUuid($toLang))
            $toLang = Languages::where('uuid', $toLang)->first()->code;

        if(is_int($toLang))
            $toLang = Languages::where('id', $toLang)->first()->code;

        if(!$toLang)
            $toLang = App::getLocale();

        /**
         * @todo: We should find a more clever way to process this. Because if browser sends this locale;
         * "en-US,en;q=0.5" we should be able to work with this also!
         */
        $isLanguageAvailable = Languages::withoutGlobalScopes()->where('code', $toLang)->first();

        if(!$isLanguageAvailable)
            $toLang = 'en';

        return I18nTranslationService::translateWithoutSaving($text, $toLang);
    }

    public static function t($text, $toLang = null, $domainId = null) : string
    {
        if(!config('app.translation_enabled'))
            return $text;

        if(Str::isUuid($toLang))
            $toLang = Languages::where('uuid', $toLang)->first()->code;

        if(is_int($toLang))
            $toLang = Languages::where('id', $toLang)->first()->code;

        if(!$toLang)
            $toLang = App::getLocale();

        /**
         * @todo: We should find a more clever way to process this. Because if browser sends this locale;
         * "en-US,en;q=0.5" we should be able to work with this also!
         */
        $isLanguageAvailable = Languages::withoutGlobalScopes()->where('code', $toLang)->first();

        if(!$isLanguageAvailable)
            $toLang = 'en';

        //  Burada gelen text'i alıp hash'leyip, veritabanına kayıt edeceğiz.
        $translated = I18nTranslationService::translate($text, $toLang, $domainId);

        if(!$translated)
            return $text;

        return $translated['translation'];
    }

    public static function getLangByUuid($langId)
    {
        if(Str::isUuid($langId)) {
            return Languages::withoutGlobalScope(AuthorizationScope::class)
                ->where('uuid', $langId)
                ->first();
        }

        return Languages::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $langId)
            ->first();
    }
}
