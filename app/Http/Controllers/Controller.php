<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

abstract class Controller
{
    /**
     * Return a consistent JSON success response.
     *
     * @param  mixed  $data
     * @param  string  $message
     * @param  int  $status
     * @return JsonResponse
     */
    protected function successResponse(mixed $data, string $message = 'Success', int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }
}
