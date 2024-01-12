<?php

namespace NextDeveloper\I18n\Http\Transformers;

use NextDeveloper\I18n\Database\Models\I18nTranslation;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;

/**
 * Class I18nTranslationTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\I18n\Http\Transformers
 */
class I18nTranslationTransformer extends AbstractTransformer {

    /**
     * @param I18nTranslation $model
     *
     * @return array
     */
    public function transform(I18nTranslation $model) {
        return $this->buildPayload([
            'id'  =>  $model->uuid,
            'hash'  =>  $model->hash,
            'language_id'  =>  $model->common_language_id,
            'text'  =>  $model->text,
            'translation'  =>  $model->translation,
        ]);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}