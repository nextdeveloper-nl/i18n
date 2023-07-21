<?php

namespace NextDeveloper\I18n\Tests\Database\Models;

use Tests\TestCase;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use NextDeveloper\I18n\Database\Filters\I18nTranslationQueryFilter;
use NextDeveloper\I18n\Services\AbstractServices\AbstractI18nTranslationService;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;

trait I18nTranslationTestTraits
{
    public $http;

    /**
    *   Creating the Guzzle object
    */
    public function setupGuzzle()
    {
        $this->http = new Client([
            'base_uri'  =>  '127.0.0.1:8000'
        ]);
    }

    /**
    *   Destroying the Guzzle object
    */
    public function destroyGuzzle()
    {
        $this->http = null;
    }

    public function test_http_i18ntranslation_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/i18n/i18ntranslation',
            ['http_errors' => false]
        );

        $this->assertContains($response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
        ]);
    }

    public function test_http_i18ntranslation_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request('POST', '/i18n/i18ntranslation', [
            'form_params'   =>  [
                'hash'  =>  'a',
                'text'  =>  'a',
                'translation'  =>  'a',
                ],
                ['http_errors' => false]
            ]
        );

        $this->assertEquals($response->getStatusCode(), Response::HTTP_OK);
    }

    /**
    * Get test
    *
    * @return bool
    */
    public function test_i18ntranslation_model_get()
    {
        $result = AbstractI18nTranslationService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_i18ntranslation_get_all()
    {
        $result = AbstractI18nTranslationService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_i18ntranslation_get_paginated()
    {
        $result = AbstractI18nTranslationService::get(null, [
            'paginated' =>  'true'
        ]);

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_i18ntranslation_event_retrieved_without_object()
    {
        try {
            event( new \NextDeveloper\I18n\Events\I18nTranslation\I18nTranslationRetrievedEvent() );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_i18ntranslation_event_created_without_object()
    {
        try {
            event( new \NextDeveloper\I18n\Events\I18nTranslation\I18nTranslationCreatedEvent() );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_i18ntranslation_event_creating_without_object()
    {
        try {
            event( new \NextDeveloper\I18n\Events\I18nTranslation\I18nTranslationCreatingEvent() );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_i18ntranslation_event_saving_without_object()
    {
        try {
            event( new \NextDeveloper\I18n\Events\I18nTranslation\I18nTranslationSavingEvent() );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_i18ntranslation_event_saved_without_object()
    {
        try {
            event( new \NextDeveloper\I18n\Events\I18nTranslation\I18nTranslationSavedEvent() );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_i18ntranslation_event_updating_without_object()
    {
        try {
            event( new \NextDeveloper\I18n\Events\I18nTranslation\I18nTranslationUpdatingEvent() );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_i18ntranslation_event_updated_without_object()
    {
        try {
            event( new \NextDeveloper\I18n\Events\I18nTranslation\I18nTranslationUpdatedEvent() );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_i18ntranslation_event_deleting_without_object()
    {
        try {
            event( new \NextDeveloper\I18n\Events\I18nTranslation\I18nTranslationDeletingEvent() );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_i18ntranslation_event_deleted_without_object()
    {
        try {
            event( new \NextDeveloper\I18n\Events\I18nTranslation\I18nTranslationDeletedEvent() );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_i18ntranslation_event_restoring_without_object()
    {
        try {
            event( new \NextDeveloper\I18n\Events\I18nTranslation\I18nTranslationRestoringEvent() );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_i18ntranslation_event_restored_without_object()
    {
        try {
            event( new \NextDeveloper\I18n\Events\I18nTranslation\I18nTranslationRestoredEvent() );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_i18ntranslation_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\I18n\Database\Models\I18nTranslation::first();

            event( new \NextDeveloper\I18n\Events\I18nTranslation\I18nTranslationRetrievedEvent($model) );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_i18ntranslation_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\I18n\Database\Models\I18nTranslation::first();

            event( new \NextDeveloper\I18n\Events\I18nTranslation\I18nTranslationCreatedEvent($model) );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_i18ntranslation_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\I18n\Database\Models\I18nTranslation::first();

            event( new \NextDeveloper\I18n\Events\I18nTranslation\I18nTranslationCreatingEvent($model) );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_i18ntranslation_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\I18n\Database\Models\I18nTranslation::first();

            event( new \NextDeveloper\I18n\Events\I18nTranslation\I18nTranslationSavingEvent($model) );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_i18ntranslation_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\I18n\Database\Models\I18nTranslation::first();

            event( new \NextDeveloper\I18n\Events\I18nTranslation\I18nTranslationSavedEvent($model) );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_i18ntranslation_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\I18n\Database\Models\I18nTranslation::first();

            event( new \NextDeveloper\I18n\Events\I18nTranslation\I18nTranslationUpdatingEvent($model) );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_i18ntranslation_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\I18n\Database\Models\I18nTranslation::first();

            event( new \NextDeveloper\I18n\Events\I18nTranslation\I18nTranslationUpdatedEvent($model) );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_i18ntranslation_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\I18n\Database\Models\I18nTranslation::first();

            event( new \NextDeveloper\I18n\Events\I18nTranslation\I18nTranslationDeletingEvent($model) );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_i18ntranslation_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\I18n\Database\Models\I18nTranslation::first();

            event( new \NextDeveloper\I18n\Events\I18nTranslation\I18nTranslationDeletedEvent($model) );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_i18ntranslation_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\I18n\Database\Models\I18nTranslation::first();

            event( new \NextDeveloper\I18n\Events\I18nTranslation\I18nTranslationRestoringEvent($model) );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_i18ntranslation_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\I18n\Database\Models\I18nTranslation::first();

            event( new \NextDeveloper\I18n\Events\I18nTranslation\I18nTranslationRestoredEvent($model) );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_i18ntranslation_event_hash_filter()
    {
        try {
            $request = new Request([
                'hash'  =>  'a'
            ]);

            $filter = new I18nTranslationQueryFilter($request);

            $model = \NextDeveloper\I18n\Database\Models\I18nTranslation::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_i18ntranslation_event_text_filter()
    {
        try {
            $request = new Request([
                'text'  =>  'a'
            ]);

            $filter = new I18nTranslationQueryFilter($request);

            $model = \NextDeveloper\I18n\Database\Models\I18nTranslation::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_i18ntranslation_event_translation_filter()
    {
        try {
            $request = new Request([
                'translation'  =>  'a'
            ]);

            $filter = new I18nTranslationQueryFilter($request);

            $model = \NextDeveloper\I18n\Database\Models\I18nTranslation::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}