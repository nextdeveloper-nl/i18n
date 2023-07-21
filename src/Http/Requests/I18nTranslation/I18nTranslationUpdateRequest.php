<?php

namespace NextDeveloper\I18n\Http\Requests\I18nTranslation;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class I18nTranslationUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules() {
        return [
            'hash'        => 'nullable|string|max:32',
			'language_id' => 'nullable|exists:languages,uuid|uuid',
			'text'        => 'nullable|string',
			'translation' => 'nullable|string',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}