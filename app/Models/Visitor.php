<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\HybridRelations;

class Visitor extends Model
{
    use HybridRelations;

    protected $table = 'visitor_log';

    protected $collection = 'visitor_log';

    protected $connection = 'mongo';

    public $timestamps = true;

    protected $guarded = ['_id'];
}
