<?php

Route::prefix('i18n')->group(function() {
    Route::prefix('translations')->group(function () {
        Route::get('/', 'I18nTranslation\I18nTranslationController@index');
        Route::get('/{i18n-translations}', 'I18nTranslation\I18nTranslationController@show');
        Route::post('/', 'I18nTranslation\I18nTranslationController@store');
        Route::put('/{i18n-translations}', 'I18nTranslation\I18nTranslationController@update');
        Route::delete('/{i18n-translations}', 'I18nTranslation\I18nTranslationController@destroy');
    });

// EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

    Route::prefix('translate')->group(function() {
        // replace "/" with "lang" in the route, for avoiding option method error
        Route::post('/lang', 'I18nTranslation\TranslationController@store');
    });

    Route::prefix('translation')->group(function() {
        Route::get('/list.vue', 'I18nTranslation\TranslationController@jsVue');
        Route::get('/list.json', 'I18nTranslation\TranslationController@jsJson');
        Route::get('/generate', 'I18nTranslation\TranslationController@generate');
    });
});
