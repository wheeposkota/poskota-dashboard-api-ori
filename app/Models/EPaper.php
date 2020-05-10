<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EPaper extends Model
{
    protected $table = 'electronic_paper';

    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISH = 'publish';
}
