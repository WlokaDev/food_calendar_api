<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

trait ApiResponse
{
    /**
     * @param mixed $data
     * @param int $customStatusCode
     * @return JsonResponse
     */

    public function successResponse(
        mixed $data,
        int $customStatusCode = ResponseAlias::HTTP_OK
    ) : JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'data' => $data,
            'code' => $customStatusCode
        ], 200);
    }

    /**
     * @param string|null $message
     * @param array|null $data
     * @param int $status
     * @return JsonResponse
     */

    public function errorResponse(
        string|null $message = null,
        array|null $data = null,
        int $status = ResponseAlias::HTTP_BAD_REQUEST
    ) : JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => $data,
            'code' => $status
        ], $status);
    }
}
