<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\ApiAccessToken;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string|max:190',
            'password' => 'required|string|max:255',
            'client_name' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data login belum lengkap atau tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $identifier = trim((string) $request->input('identifier'));
        $field = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        $user = User::where($field, $identifier)->first();

        if (!$user || !Hash::check((string) $request->input('password'), $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Kredensial tidak valid.',
                'error' => 'invalid_credentials',
            ], 401);
        }

        $ttlDays = max(1, (int) config('api.token_ttl_days', 30));
        $maxActiveTokens = max(1, (int) config('api.max_active_tokens_per_user', 10));
        $now = now();

        $activeTokens = ApiAccessToken::where('user_id', $user->id)
            ->whereNull('revoked_at')
            ->where(function ($query) use ($now) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', $now);
            })
            ->orderBy('created_at')
            ->get();

        while ($activeTokens->count() >= $maxActiveTokens) {
            $oldest = $activeTokens->shift();
            $oldest->revoked_at = $now;
            $oldest->save();
        }

        try {
            $plainToken = bin2hex(random_bytes(32));
        } catch (\Exception $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Token login belum dapat dibuat. Silakan coba kembali.',
                'error' => 'token_generation_failed',
            ], 500);
        }

        $accessToken = ApiAccessToken::create([
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $plainToken),
            'client_name' => trim((string) $request->input('client_name')),
            'scopes' => json_encode(['profile:read']),
            'expires_at' => $now->copy()->addDays($ttlDays),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login API berhasil.',
            'data' => [
                'access_token' => $plainToken,
                'token_type' => 'Bearer',
                'expires_at' => $accessToken->expires_at->toIso8601String(),
                'user' => $this->userPayload($user),
            ],
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'user' => $this->userPayload($request->user()),
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $accessToken = $request->attributes->get('api_access_token');
        if ($accessToken) {
            $accessToken->revoked_at = now();
            $accessToken->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Token API berhasil dicabut.',
        ]);
    }

    protected function userPayload(User $user)
    {
        return [
            'id' => (int) $user->id,
            'name' => (string) $user->name,
            'email' => (string) $user->email,
            'level' => (int) $user->level,
        ];
    }
}
