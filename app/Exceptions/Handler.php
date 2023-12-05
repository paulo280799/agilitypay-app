<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof HttpException) {
            \Log::info($exception);

            return response()->json(['success' => false, 'message' => 'Ocorreu um erro interno. Contate o adminnistrador do sistema'], 500);
        } elseif ($exception instanceof ValidationException) {
            \Log::info($exception);

            return response()->json(['success' => false, 'message' => $exception->validator->errors()->first()], 422);
        } elseif ($exception instanceof AuthenticationException) {
            \Log::info($exception);

            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 422);
        } else {
            \Log::info($exception);

            return response()->json(['success' => false, 'message' => 'Ocorreu um erro interno. Contate o adminnistrador do sistema'], 500);
        }

        return parent::render($request, $exception);
    }
}
