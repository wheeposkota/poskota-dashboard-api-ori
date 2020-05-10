<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostWp extends Model
{
    protected $table = 'wp_posts';

    protected $connection = 'mysql_v1';
}
