<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Users extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'name',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function userRole()
    {
        return $this->belongsTo(UserRoles::class, 'user_role_id', 'id');
    }

    public function scores()
    {
        return $this->hasMany(Score::class, 'user_id', 'id');
    }
}