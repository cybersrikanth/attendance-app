<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    // use DatabaseTransactions;
    use RefreshDatabase;
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('passport:install');
    }

    protected $success_structure = [
        "success", "message", "data"
    ];

    protected $error_structure = [
        "success", "message", "errors"
    ];
}
