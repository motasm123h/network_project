<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Closure;
use Exception;
use Illuminate\Http\Request;

class fileException extends Exception
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // Pass the request to the next middleware/controller
            return $next($request);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred during the request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
