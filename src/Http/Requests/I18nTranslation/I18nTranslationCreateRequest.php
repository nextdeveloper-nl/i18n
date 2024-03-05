<?php

namespace NextDeveloper\I18n\Http\Requests\I18nTranslation;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class I18nTranslationCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules() {
        return [
            'hash'        => 'required|string|max:32',
			'language_id' => 'nullable|exists:languages,uuid|uuid',
			'text'        => 'required|string',
			'translation' => 'required|string',
            'domain_id'   => 'required|exists:domains,uuid|uuid',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
