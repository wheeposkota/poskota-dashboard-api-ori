<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostTermTaxo extends Model
{
    protected $table = 'term_taxonomy';
    protected $primaryKey = 'id_term_taxonomy';

    const TYPE_CATEGORY = 'category';
    const TYPE_TAG = 'tag';

    protected $fillable = [
        'term_id',
        'description',
        'taxonomy',
        'parent_id',
        'created_by',
        'modified_by',
    ];
}
