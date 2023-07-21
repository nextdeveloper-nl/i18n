<?php

namespace NextDeveloper\I18n\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;
    

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class I18nTranslationQueryFilter extends AbstractQueryFilter
{
    /**
    * @var Builder
    */
    protected $builder;
    
    public function hash($value)
    {
        return $this->builder->where('hash', 'like', '%' . $value . '%');
    }
    
    public function text($value)
    {
        return $this->builder->where('text', 'like', '%' . $value . '%');
    }
    
    public function translation($value)
    {
        return $this->builder->where('translation', 'like', '%' . $value . '%');
    }

    public function languageId($value)
    {
        $language = Language::where('uuid', $value)->first();

        if($language) {
            return $this->builder->where('language_id', '=', $language->id);
        }
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}