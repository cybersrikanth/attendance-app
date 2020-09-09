<?php

namespace Tests;

use App\Helpers\DocHelper;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    // use DatabaseTransactions;
    use RefreshDatabase;

    protected $request = [
        "method" => "GET",
        "uri" => "",
        "headers" => ["Accept" => "application/json", "content-type" => "application/json"],
        "body" => []
    ];

    protected function setRequestHeader($key, $value)
    {
        $headers = $this->request["headers"];
        $headers[$key] = $value;
        $this->request['headers'] = $headers;
    }

    protected function fire(){
        return $this->json($this->request["method"], $this->request["uri"], $this->request["body"], $this->request["headers"]);
    }

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->setUp();
        DocHelper::init();
    }

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
