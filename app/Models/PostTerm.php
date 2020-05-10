<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostTerm extends Model
{
    use SoftDeletes;

    protected $table = 'terms';
    protected $primaryKey = 'id_terms';

    protected $fillable = [
        'name',
        'slug',
        'term_group',
        'created_by',
        'modified_by',
    ];
}
