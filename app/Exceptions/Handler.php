<?php

namespace App\Exceptions;

use App\Http\Traits\ApiResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponse;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if($e instanceof UnauthorizedException) {
            return $this->errorResponse(
                __('messages.Unauthorized'),
                status: 401
            );
        }

        if($e instanceof ValidationException) {
            return $this->errorResponse(
                __('messages.Validation error'),
                $e->errors(),
                422
            );
        }

        if($e instanceof NotFoundHttpException) {
            return $this->errorResponse(
                __('messages.Page not found'),
                status: 404
            );
        }

        return parent::render($request, $e);
    }
}
