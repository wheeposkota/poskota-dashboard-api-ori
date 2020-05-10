<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostWpMeta extends Model
{
    protected $table = 'wp_postmeta';

    protected $connection = 'mysql_v1';
}
