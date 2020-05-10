<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostRelationship extends Model
{
    protected $table = 'term_relationships';
    protected $primaryKey = 'id_term_relationships';

    protected $fillable = [
        'object_id',
        'term_taxonomy_id',
        'term_order',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'object_id');
    }

}
