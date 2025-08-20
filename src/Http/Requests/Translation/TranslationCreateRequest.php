<?php

namespace NextDeveloper\I18n\Http\Requests\Translation;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class TranslationCreateRequest extends AbstractFormRequest
{
    public function rules(): array
    {
        return [
            'text'      => 'required|string',
            'locale'    => 'required|string|exists:common_languages,iso_639_1_code',
            'common_domain_id' =>   'required|exists:common_domains,uuid',
        ];
    }
}
