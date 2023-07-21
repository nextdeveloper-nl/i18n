<?php

namespace NextDeveloper\I18n\Services\AbstractServices;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use NextDeveloper\I18n\Database\Models\I18nTranslation;
use NextDeveloper\I18n\Database\Filters\I18nTranslationQueryFilter;

use NextDeveloper\I18n\Events\I18nTranslation\I18nTranslationCreatedEvent;
use NextDeveloper\I18n\Events\I18nTranslation\I18nTranslationCreatingEvent;
use NextDeveloper\I18n\Events\I18nTranslation\I18nTranslationUpdatedEvent;
use NextDeveloper\I18n\Events\I18nTranslation\I18nTranslationUpdatingEvent;
use NextDeveloper\I18n\Events\I18nTranslation\I18nTranslationDeletedEvent;
use NextDeveloper\I18n\Events\I18nTranslation\I18nTranslationDeletingEvent;

/**
* This class is responsible from managing the data for I18nTranslation
*
* Class I18nTranslationService.
*
* @package NextDeveloper\I18n\Database\Models
*/
class AbstractI18nTranslationService {
    public static function get(I18nTranslationQueryFilter $filter = null, array $params = []) : Collection|LengthAwarePaginator {
        $enablePaginate = array_key_exists('paginate', $params);

        /**
        * Here we are adding null request since if filter is null, this means that this function is called from
        * non http application. This is actually not I think its a correct way to handle this problem but it's a workaround.
        *
        * Please let me know if you have any other idea about this; baris.bulut@nextdeveloper.com
        */
        if($filter == null)
            $filter = new I18nTranslationQueryFilter(new Request());

        $perPage = config('commons.pagination.per_page');

        if($perPage == null)
            $perPage = 20;

        if(array_key_exists('per_page', $params)) {
            $perPage = intval($params['per_page']);

            if($perPage == 0)
                $perPage = 20;
        }

        if(array_key_exists('orderBy', $params)) {
            $filter->orderBy($params['orderBy']);
        }

        $model = I18nTranslation::filter($filter);

        if($model && $enablePaginate)
            return $model->paginate($perPage);
        else
            return $model->get();

        if(!$model && $enablePaginate)
            return I18nTranslation::paginate($perPage);
        else
            return I18nTranslation::get();
    }

    public static function getAll() {
        return I18nTranslation::all();
    }

    /**
    * This method returns the model by looking at reference id
    *
    * @param $ref
    * @return mixed
    */
    public static function getByRef($ref) : ?I18nTranslation {
        return I18nTranslation::findByRef($ref);
    }

    /**
    * This method returns the model by lookint at its id
    *
    * @param $id
    * @return I18nTranslation|null
    */
    public static function getById($id) : ?I18nTranslation {
        return I18nTranslation::where('id', $id)->first();
    }

    /**
    * This method created the model from an array.
    *
    * Throws an exception if stuck with any problem.
    *
    * @param array $data
    * @return mixed
    * @throw Exception
    */
    public static function create(array $data) {
        event( new I18nTranslationCreatingEvent() );

        try {
            $model = I18nTranslation::create($data);
        } catch(\Exception $e) {
            throw $e;
        }

        event( new I18nTranslationCreatedEvent($model) );

        return $model;
    }

    /**
    * This method updated the model from an array.
    *
    * Throws an exception if stuck with any problem.
    *
    * @param
    * @param array $data
    * @return mixed
    * @throw Exception
    */
    public static function update($id, array $data) {
        $model = I18nTranslation::where('uuid', $id)->first();

        event( new I18nTranslationsUpdateingEvent($model) );

        try {
           $model = $model->update($data);
        } catch(\Exception $e) {
           throw $e;
        }

        event( new I18nTranslationsUpdatedEvent($model) );

        return $model;
    }

    /**
    * This method updated the model from an array.
    *
    * Throws an exception if stuck with any problem.
    *
    * @param
    * @param array $data
    * @return mixed
    * @throw Exception
    */
    public static function delete($id, array $data) {
        $model = I18nTranslation::where('uuid', $id)->first();

        event( new I18nTranslationsDeletingEvent() );

        try {
            $model = $model->delete();
        } catch(\Exception $e) {
            throw $e;
        }

        event( new I18nTranslationsDeletedEvent($model) );

        return $model;
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}