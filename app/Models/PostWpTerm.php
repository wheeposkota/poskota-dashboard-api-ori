<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostWpTerm extends Model
{
    protected $table = 'wp_terms';

    protected $connection = 'mysql_v1';
}
