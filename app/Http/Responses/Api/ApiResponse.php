<?php

namespace App\Http\Responses\Api;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiResponse
{
    public static function ok(string $message, ?array $data = null): JsonResponse
    {
        return self::success($message, $data, Response::HTTP_OK);
    }

    public static function created(string $message, ?array $data = null): JsonResponse
    {
        return self::success($message, $data, Response::HTTP_CREATED);
    }

    public static function accepted(string $message, ?array $data = null): JsonResponse
    {
        return self::success($message, $data, Response::HTTP_ACCEPTED);
    }

    public static function noContent(): JsonResponse
    {
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public static function unauthorized(string $message = 'Unauthenticated.'): JsonResponse
    {
        return self::error($message, Response::HTTP_UNAUTHORIZED);
    }

    public static function forbidden(string $message = 'Forbidden.'): JsonResponse
    {
        return self::error($message, Response::HTTP_FORBIDDEN);
    }

    public static function notFound(string $message = 'Resource not found.'): JsonResponse
    {
        return self::error($message, Response::HTTP_NOT_FOUND);
    }

    public static function unprocessable(string $message): JsonResponse
    {
        return self::error($message, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public static function serverError(string $message = 'An unexpected error occurred.'): JsonResponse
    {
        return self::error($message, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    private static function success(string $message, ?array $data, int $status): JsonResponse
    {
        $payload = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $payload['data'] = $data;
        }

        return response()->json($payload, $status);
    }

    private static function error(string $message, int $status): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}
