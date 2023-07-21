<?php

namespace NextDeveloper\I18n\Database\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use NextDeveloper\Commons\Database\Traits\Filterable;
use NextDeveloper\I18n\Database\Observers\I18nTranslationObserver;
use NextDeveloper\Commons\Database\Traits\UuidId;

/**
 * Class I18nTranslation.
 *
 * @package NextDeveloper\I18n\Database\Models
 */
class I18nTranslation extends Model
{
    use Filterable, UuidId;
    

    public $timestamps = false;

    protected $table = 'i18n_translations';


    /**
     * @var array
     */
    protected $guarded = [];

    /**
     *  Here we have the fulltext fields. We can use these for fulltext search if enabled.
     */
    protected $fullTextFields = [
        
    ];

    /**
     * @var array
     */
    protected $appends = [
        
    ];

    /**
     * We are casting fields to objects so that we can work on them better
     * @var array
     */
    protected $casts = [
        'id'          => 'integer',
		'uuid'        => 'string',
		'hash'        => 'string',
		'language_id' => 'integer',
		'text'        => 'string',
		'translation' => 'string',
    ];

    /**
     * We are casting data fields.
     * @var array
     */
    protected $dates = [
        
    ];

    /**
     * @var array
     */
    protected $with = [

    ];

    /**
     * @var int
     */
    protected $perPage = 20;

    /**
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        //  We create and add Observer even if we wont use it.
        parent::observe(I18nTranslationObserver::class);
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}