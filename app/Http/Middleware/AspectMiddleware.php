<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AspectMiddleware
{
    public function __construct(public string $message = 'Logging...')
    {
        // Constructor for optional message customization
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $controller = $request->route()->getController();
        $method = $request->route()->getActionMethod();

        // Execute logic before handling the request
        $this->logBefore($request, $controller, $method);

        try {
            // Process the request and capture the response
            $response = $next($request);

            // Execute logic after handling the request
            $this->logAfter($response);

            return $response;
        } catch (\Throwable $exception) {
            // Log the exception
            $this->logException($request, $controller, $method, $exception);

            // Re-throw the exception to be handled by Laravel
            throw $exception;
        }
    }

    /**
     * Log information before handling the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $controller
     * @param  string  $method
     * @return void
     */
    protected function logBefore(Request $request, $controller, string $method): void
    {
        Log::info($this->message);
        Log::info('Request: ' . $request->fullUrl());
        Log::info('Controller: ' . get_class($controller));
        Log::info('Method: ' . $method);
    }

    /**
     * Log information after handling the request.
     *
     * @param  mixed  $response
     * @return void
     */
    protected function logAfter($response): void
    {
        Log::info('Response: ' . $response->getContent());
    }

    /**
     * Log exceptions thrown during the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $controller
     * @param  string  $method
     * @param  \Throwable  $exception
     * @return void
     */
    protected function logException(Request $request, $controller, string $method, \Throwable $exception): void
    {
        Log::error('Exception occurred in ' . get_class($controller) . '::' . $method);
        Log::error('Exception Message: ' . $exception->getMessage());
        Log::error('Stack Trace: ' . $exception->getTraceAsString());
    }
}
