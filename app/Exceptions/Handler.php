<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    private function response()
    {
        return ResponseHelper::response();
    }
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof HttpException) {
            return $this->response()
                ->message($exception->getMessage())
                ->data(null)
                ->send($exception->getStatusCode());
        }
        if ($exception instanceof ModelNotFoundException) {
            return $this->response()
                ->message("Resource Not Found")
                ->data(null)
                ->send(404);
        }
        if ($exception instanceof ValidationException) {
            return $this->response()
                ->message("Validation Error")
                ->data($exception->errors())
                ->send(422);
        }
        if ($exception instanceof AuthenticationException) {
            return $this->response()
                ->message($exception->getMessage())
                ->data(null)
                ->send(403);
        }
        if ($exception instanceof AuthorizationException) {
            return $this->response()
                ->message($exception->getMessage())
                ->data(null)
                ->send(401);
        }
        // return $this->response()
        //     ->message("Bad Request")
        //     ->data("Something went wrong")
        //     ->send(400);
        return parent::render($request, $exception);
    }
}
