<?php

namespace App\Traits;

trait ResponseTrait
{
    public function apiResponse($message = null, $data = null, $statuscode = 200)
    {
        // dd('here', $data, $message);
        return response()->json([
            'message' => $message,
            'data' => $data
        ], $statuscode);
    }
}
