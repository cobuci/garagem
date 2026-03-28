<?php

namespace App\Http\Responses\Api\Concerns;

use App\Http\Responses\Api\ApiResponse;
use Illuminate\Http\JsonResponse;

trait HasApiResponses
{
    protected function ok(string $message, ?array $data = null): JsonResponse
    {
        return ApiResponse::ok($message, $data);
    }

    protected function created(string $message, ?array $data = null): JsonResponse
    {
        return ApiResponse::created($message, $data);
    }

    protected function accepted(string $message, ?array $data = null): JsonResponse
    {
        return ApiResponse::accepted($message, $data);
    }

    protected function noContent(): JsonResponse
    {
        return ApiResponse::noContent();
    }

    protected function unauthorized(string $message = 'Unauthenticated.'): JsonResponse
    {
        return ApiResponse::unauthorized($message);
    }

    protected function forbidden(string $message = 'Forbidden.'): JsonResponse
    {
        return ApiResponse::forbidden($message);
    }

    protected function notFound(string $message = 'Resource not found.'): JsonResponse
    {
        return ApiResponse::notFound($message);
    }

    protected function unprocessable(string $message): JsonResponse
    {
        return ApiResponse::unprocessable($message);
    }

    protected function serverError(string $message = 'An unexpected error occurred.'): JsonResponse
    {
        return ApiResponse::serverError($message);
    }
}
