<?php

namespace NextDeveloper\I18n\Http\Controllers\I18nTranslation;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use NextDeveloper\Commons\Http\Controllers\AbstractController;
use NextDeveloper\Commons\Http\Response\ResponsableFactory;
use NextDeveloper\I18n\Http\Requests\I18nTranslation\I18nTranslationUpdateRequest;
use NextDeveloper\I18n\Database\Filters\I18nTranslationQueryFilter;
use NextDeveloper\I18n\Http\Requests\Translation\TranslationCreateRequest;
use NextDeveloper\I18n\Services\I18nTranslationService;
use NextDeveloper\I18n\Http\Requests\I18nTranslation\I18nTranslationCreateRequest;
use NextDeveloper\I18n\Services\LanguageGeneratorService;

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

    public function jsVue(I18nTranslationQueryFilter $filter, Request $request) {
        $data = I18nTranslationService::get($filter, $request->all());

        $js = 'export const lang = Vue.ref({' . PHP_EOL;
        foreach ($data as $datum) {
            $js .= '"' . $datum['text'] . '": "' . $datum['translation'] . '",' . PHP_EOL;
        }
        $js .= '"built_with" : "NextDeveloper i18n module"'. PHP_EOL;
        $js .= '})';

        return $js;
    }

    public function jsJson(I18nTranslationQueryFilter $filter, Request $request) {
        $data = I18nTranslationService::get($filter, $request->all());

        $json = [];

        foreach ($data as $datum) {
            $json[$datum['text']] = $datum['translation'];
        }

        return response()->json($json);
    }

    /**
     * This method returns the specified i18ntranslation.
     *
     * @param TranslationCreateRequest $request
     * @return mixed|null
     * @throws \Google\Cloud\Core\Exception\ServiceException
     */
    public function store(TranslationCreateRequest $request) {
        dd('asd');
        $data   = $request->validated();
        $model  = I18nTranslationService::translate($data, $data['locale'], $data['domain_id']);

        return ResponsableFactory::makeResponse($this, $model);
    }

    public function generate() {
        LanguageGeneratorService::generate();
    }
}
