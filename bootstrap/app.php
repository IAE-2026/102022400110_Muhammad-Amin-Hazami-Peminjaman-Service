<?php

error_reporting(E_ALL & ~E_DEPRECATED);


use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',
    )
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'apikey' => \App\Http\Middleware\ApiKeyMiddleware::class,
        'jwt' => \App\Http\Middleware\JwtMiddleware::class,
    ]);
})
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'status'  => 'error',
                    'message' => 'Resource not found',
                    'errors'  => null,
                    'data'    => null,
                ], 404);
            }
        });

        $exceptions->renderable(function (ValidationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'status'  => 'error',
                    'message' => 'Validation failed',
                    'errors'  => $e->errors(),
                    'data'    => null,
                ], 422);
            }
        });

        $exceptions->renderable(function (\Throwable $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'status'  => 'error',
                    'message' => $e->getMessage() ?: 'Internal Server Error',
                    'errors'  => [
                        'exception' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ],
                    'data'    => null,
                ], 500);
            }
        });
    })->create();

