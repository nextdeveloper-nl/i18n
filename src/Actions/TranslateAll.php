<?php

namespace NextDeveloper\I18n\Actions;

use NextDeveloper\Commons\Actions\AbstractAction;

/**
 * This action will translate all the english text to other languages if the translation is not already present.
 *
 * @package NextDeveloper\I18n
 */
class TranslateAll extends AbstractAction
{
    public $model;

    public function __construct()
    {

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
