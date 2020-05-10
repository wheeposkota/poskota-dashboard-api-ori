<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostWpTermTaxo extends Model
{
    protected $table = 'wp_term_taxonomy';

    protected $connection = 'mysql_v1';
}
