<?php

namespace NextDeveloper\I18n\Events\I18nTranslation;

use Illuminate\Queue\SerializesModels;
use NextDeveloper\I18n\Database\Models\I18nTranslation;

/**
 * Class I18nTranslationRetrievedEvent
 * @package NextDeveloper\I18n\Events
 */
class I18nTranslationRetrievedEvent
{
    use SerializesModels;

    /**
     * @var I18nTranslation
     */
    public $_model;

    /**
     * @var int|null
     */
    protected $timestamp = null;

    public function __construct(I18nTranslation $model = null) {
        $this->_model = $model;
    }

    /**
    * @param int $value
    *
    * @return AbstractEvent
    */
    public function setTimestamp($value) {
        $this->timestamp = $value;

        return $this;
    }

    /**
    * @return int|null
    */
    public function getTimestamp() {
        return $this->timestamp;
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}