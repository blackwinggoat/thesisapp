<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class ApiAccessToken extends Model
{
    protected $table = 'api_access_tokens';

    protected $fillable = [
        'user_id',
        'token_hash',
        'client_name',
        'scopes',
        'expires_at',
        'last_used_at',
        'revoked_at',
    ];

    protected $hidden = [
        'token_hash',
    ];

    protected $dates = [
        'expires_at',
        'last_used_at',
        'revoked_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
