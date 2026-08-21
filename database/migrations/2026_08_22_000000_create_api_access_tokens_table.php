<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiAccessTokensTable extends Migration
{
    public function up()
    {
        Schema::create('api_access_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('token_hash', 64)->unique();
            $table->string('client_name', 100);
            $table->text('scopes')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('revoked_at')->nullable()->index();
            $table->timestamps();

            $table->index(['user_id', 'revoked_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('api_access_tokens');
    }
}
