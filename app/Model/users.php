<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class users extends Model
{
  protected $table = 'users';
  protected $primaryKey = 'id';
  protected $fillable = ['id', 'name', 'email', 'password', 'remember_token', 'level'];
}
