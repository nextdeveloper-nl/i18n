<?php

namespace NextDeveloper\I18n\Http\Controllers\I18nTranslation;

use Illuminate\Http\Request;
use NextDeveloper\Generator\Common\AbstractController;
use NextDeveloper\Generator\Http\Traits\ResponsableFactory;
use NextDeveloper\I18n\Http\Requests\I18nTranslation\I18nTranslationUpdateRequest;
use NextDeveloper\I18n\Database\Filters\I18nTranslationQueryFilter;
use NextDeveloper\I18n\Services\I18nTranslationService;
use NextDeveloper\I18n\Http\Requests\I18nTranslation\I18nTranslationCreateRequest;

class TranslationController extends AbstractController
{
    /**
    * This method returns the list of i18ntranslations.
    *
    * optional http params:
    * - paginate: If you set paginate parameter, the result will be returned paginated.
    *
    * @param I18nTranslationQueryFilter $filter An object that builds search query
    * @param Request $request Laravel request object, this holds all data about request. Automatically populated.
    * @return \Illuminate\Http\JsonResponse
    */
    public function index(I18nTranslationQueryFilter $filter, Request $request) {
        $data = I18nTranslationService::get($filter, $request->all());

        return ResponsableFactory::makeResponse($this, $data);
    }
}