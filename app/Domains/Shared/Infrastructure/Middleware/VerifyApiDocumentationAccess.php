<?php

declare(strict_types=1);

namespace App\Domains\Shared\Infrastructure\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * VerifyApiDocumentationAccess
 *
 * Protects API documentation endpoints using HMAC-signed requests.
 *
 * Required headers:
 *   X-API-Key   — public key stored in api_keys table
 *   X-Timestamp — Unix timestamp (request rejected if > 5 min old)
 *   X-Signature — HMAC-SHA256 of "{METHOD}\n{PATH}\n{TIMESTAMP}\n{BODY}"
 *                 signed with the api_keys.secret value
 */
final class VerifyApiDocumentationAccess
{
    private const MAX_TIMESTAMP_DRIFT_SECONDS = 300;

    public function handle(Request $request, Closure $next): Response
    {
        $apiKey    = $request->header('X-API-Key');
        $timestamp = $request->header('X-Timestamp');
        $signature = $request->header('X-Signature');

        if (!$apiKey || !$timestamp || !$signature) {
            return response()->json(
                ['error' => 'Missing authentication headers'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        if (!ctype_digit((string) $timestamp)) {
            return response()->json(
                ['error' => 'Invalid timestamp format'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        if (abs(time() - (int) $timestamp) > self::MAX_TIMESTAMP_DRIFT_SECONDS) {
            return response()->json(
                ['error' => 'Request timestamp expired'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $record = DB::table('api_keys')
            ->where('key', $apiKey)
            ->where('is_active', true)
            ->first();

        if (!$record) {
            return response()->json(
                ['error' => 'Invalid API key'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $stringToSign = implode("\n", [
            $request->method(),
            $request->path(),
            $timestamp,
            $request->getContent(),
        ]);

        $expected = hash_hmac('sha256', $stringToSign, $record->secret);

        if (!hash_equals($expected, $signature)) {
            return response()->json(
                ['error' => 'Invalid signature'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        DB::table('api_keys')
            ->where('id', $record->id)
            ->update(['last_used_at' => now()]);

        return $next($request);
    }
}
