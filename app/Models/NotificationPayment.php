<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationPayment extends Model
{
    protected $fillable = [
        'order_id',
        'status',
        'message',
        'response'
    ];

    protected $casts = [
        'response' => 'array',
    ];
}
