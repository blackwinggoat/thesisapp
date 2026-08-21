<?php

return [

    /*
    |--------------------------------------------------------------------------
    | External API token settings
    |--------------------------------------------------------------------------
    | Tokens are stored as SHA-256 hashes. The raw token is returned only once
    | during login and is never written to the database.
    */

    'token_ttl_days' => (int) env('API_TOKEN_TTL_DAYS', 30),

    'max_active_tokens_per_user' => (int) env('API_MAX_ACTIVE_TOKENS_PER_USER', 10),

];
