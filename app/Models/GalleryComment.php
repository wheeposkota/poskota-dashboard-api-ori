<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GalleryComment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'object_id',
        'member_id',
        'comment',
    ];

    protected $dates = ['deleted_at'];
}
