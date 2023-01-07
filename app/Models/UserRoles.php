<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRoles extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_name',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'user_role_id', 'id');
    }
}