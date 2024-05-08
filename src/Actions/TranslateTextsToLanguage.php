<?php

namespace NextDeveloper\I18n\Actions;

use NextDeveloper\Commons\Actions\AbstractAction;
use NextDeveloper\Commons\Database\Models\Languages;
use NextDeveloper\I18n\Database\Models\I18nTranslation;

/**
 * This action will translate the given text to other languages if the translation is not already present.
 * If text is not provided, it will translate all texts to other languages.
 *
 * @package NextDeveloper\I18n
 */
class TranslateTextsToLanguage extends AbstractAction
{
    public $model;

    public function __construct(I18nTranslation $i18nTranslation = null)
    {
        $this->model = $i18nTranslation;
    }

    /**
     * Translates english to other languages
     *
     * @throws \Exception
     */
    public function handle()
    {
        // Set initial progress
        $this->setProgress(0, 'Registering the user');

        /**
         * This action will translate all the english text to other languages if the translation is not already present.
         */

        /**
         * 1) Get all english text from the database
         * 2) Get all the active languages from the database
         * 3) Translate the english text to all the active languages
         */

        $this->setFinished();
    }
}
