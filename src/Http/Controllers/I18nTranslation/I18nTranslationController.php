<?php

namespace NextDeveloper\I18n\Http\Controllers\I18nTranslation;

use Illuminate\Http\Request;
use NextDeveloper\Commons\Http\Controllers\AbstractController;
use NextDeveloper\Commons\Http\Response\ResponsableFactory;
use NextDeveloper\I18n\Http\Requests\I18nTranslation\I18nTranslationUpdateRequest;
use NextDeveloper\I18n\Database\Filters\I18nTranslationQueryFilter;
use NextDeveloper\I18n\Services\I18nTranslationService;
use NextDeveloper\I18n\Http\Requests\I18nTranslation\I18nTranslationCreateRequest;

class I18nTranslationController extends AbstractController
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

    /**
    * This method receives ID for the related model and returns the item to the client.
    *
    * @param $i18nTranslationId
    * @return mixed|null
    * @throws \Laravel\Octane\Exceptions\DdException
    */
    public function show($ref) {
        //  Here we are not using Laravel Route Model Binding. Please check routeBinding.md file
        //  in NextDeveloper Platform Project
        $model = I18nTranslationService::getByRef($ref);

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
    * This method created I18nTranslation object on database.
    *
    * @param I18nTranslationCreateRequest $request
    * @return mixed|null
    * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
    */
    public function store(I18nTranslationCreateRequest $request) {
        $model = I18nTranslationService::create($request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
    * This method updates I18nTranslation object on database.
    *
    * @param $i18nTranslationId
    * @param CountryCreateRequest $request
    * @return mixed|null
    * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
    */
    public function update($i18nTranslationId, I18nTranslationUpdateRequest $request) {
        $model = I18nTranslationService::update($i18nTranslationId, $request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
    * This method updates I18nTranslation object on database.
    *
    * @param $i18nTranslationId
    * @param CountryCreateRequest $request
    * @return mixed|null
    * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
    */
    public function destroy($i18nTranslationId) {
        $model = I18nTranslationService::delete($i18nTranslationId);

        return ResponsableFactory::makeResponse($this, $model);
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}
