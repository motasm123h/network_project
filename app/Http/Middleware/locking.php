<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Files;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class locking
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $file = Files::find($request->route('file_id'));

        if ($file && $file->locked_by && $file->locked_by !== auth()->id()) {
            return response()->json(['message' => 'File is currently locked by another user'], 423); // 423 Locked
        }

        return $next($request);
    }
}
