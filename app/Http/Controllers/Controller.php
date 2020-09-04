<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Attendance App",
     *      description="Laravel Attendance App",
     *      @OA\Contact(
     *          email="admin@admin.com"
     *      ),
     *      @OA\License(
     *          name="license_name",
     *          url="http://localhost:8000/license"
     *      )
     * )
     *
     * @OA\Server(
     *      url="http://localhost:8000/",
     *      description="Demo API Server"
     * )

     *
     *
     */
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
