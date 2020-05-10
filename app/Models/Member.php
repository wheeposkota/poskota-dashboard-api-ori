<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Member extends Authenticatable
{
    const VIA_WEB = 'web';
    const VIA_MOBILE = 'mobile';

    const TYPE_REGULAR = 'regular';
    const TYPE_VERIFIED = 'verified';

    protected $fillable = [
        'name',
        'email',
        'password',
        'oauth_token',
        'phone',
        'address',
        'avatar',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public static function getTableName()
    {
        return with(new Static)->getTable();
    }

    public static function getPrimaryKey()
    {
        return with(new Static)->getKeyName();
    }
}
