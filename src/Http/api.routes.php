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
        Route::get('/', );
    });
});