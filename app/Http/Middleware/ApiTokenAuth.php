<?php

namespace App\Http\Middleware;

use App\Model\ApiAccessToken;
use Closure;
use Illuminate\Support\Facades\DB;

class ApiTokenAuth
{
    public function handle($request, Closure $next)
    {
        $authorization = trim((string) $request->header('Authorization', ''));
        if (!preg_match('/^Bearer\s+([a-f0-9]{64})$/i', $authorization, $matches)) {
            return $this->unauthorized('missing_token');
        }

        $accessToken = ApiAccessToken::with('user')
            ->where('token_hash', hash('sha256', $matches[1]))
            ->first();

        if (!$accessToken || $accessToken->revoked_at !== null) {
            return $this->unauthorized('invalid_token');
        }

        if ($accessToken->expires_at !== null && now()->greaterThanOrEqualTo($accessToken->expires_at)) {
            return $this->unauthorized('expired_token');
        }

        if (!$accessToken->user) {
            return $this->unauthorized('invalid_token');
        }

        $request->setUserResolver(function () use ($accessToken) {
            return $accessToken->user;
        });
        $request->attributes->set('api_access_token', $accessToken);

        DB::table('api_access_tokens')
            ->where('id', $accessToken->id)
            ->update([
                'last_used_at' => now(),
                'updated_at' => now(),
            ]);

        return $next($request);
    }

    protected function unauthorized($code)
    {
        return response()->json([
            'success' => false,
            'message' => 'Token API tidak valid atau sudah tidak berlaku.',
            'error' => $code,
        ], 401);
    }
}
