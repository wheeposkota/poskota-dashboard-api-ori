<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EPaperSubscription extends Model
{
    protected $table = 'epaper_subscription';

    public $timestamps = false;

    protected $fillable = [
        'start_date',
        'end_date',
        'order_id',
        'package_id',
        'member_id',
    ];

    public function package()
    {
        return $this->belongsTo(EPaperPackage::class, 'package_id');
    }
}
