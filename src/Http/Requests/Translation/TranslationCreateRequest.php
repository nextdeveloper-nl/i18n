<?php

namespace NextDeveloper\I18n\Http\Requests\Translation;

use Illuminate\Foundation\Http\FormRequest;

class TranslationCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'text'      => 'required|string',
            'locale'    => 'required|string|exists:common_languages,iso_639_1_code',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
