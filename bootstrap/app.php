<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'admin'=>App\Http\Middleware\AdminMiddleware::class,
            'google-auth'=>App\Http\Middleware\GoogleAuthenticateMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (\Throwable $exception, Request $request) {
            $isDatabaseFailure = $exception instanceof QueryException
            || $exception instanceof \PDOException;

            if (!$isDatabaseFailure && $exception instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                return null;
            }

            $status = $isDatabaseFailure ? 503 : 500;
            $title = $isDatabaseFailure ? 'Service temporarily unavailable' : 'Something went wrong';
            $message = $isDatabaseFailure
                ? 'We cannot reach the service right now. Please try again in a few minutes.'
                : 'We could not complete that request. Please try again.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                ], $status);
            }

            return response()->view('errors.unavailable', [
                'title' => $title,
                'message' => $message,
                'status' => $status,
            ], $status);
        });
    })->create();
