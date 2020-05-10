<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostWpTaxo extends Model
{
    protected $table = 'wp_term_relationships';

    protected $connection = 'mysql_v1';
}
