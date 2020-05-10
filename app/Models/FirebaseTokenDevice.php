<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FirebaseTokenDevice extends Model
{
    protected $fillable = [
        'member_id',
        'registration_token',
        'topic',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];
}
