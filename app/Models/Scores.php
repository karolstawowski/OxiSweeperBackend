<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scores extends Model
{
    use HasFactory;

    protected $fillable = [
        'score',
        'user_id',
        'difficulty_level'
    ];

    public function userId()
    {
        return $this->belongsTo(Users::class, 'user_id', 'id');
    }
}