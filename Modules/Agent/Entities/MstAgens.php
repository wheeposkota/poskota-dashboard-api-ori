<?php

namespace Modules\Agent\Entities;

use Illuminate\Database\Eloquent\Model;

class MstAgens extends Model
{
    protected $fillable = [];
    protected $table = 'mst_agens';

    public function users()
    {
    	return $this->belongsToMany('\Gdevilbat\SpardaCMS\Modules\Core\Entities\User', 'rlt_mst_agens_users', 'mst_agens_id', 'user_id');
    }

    public function rltMstAgentUser()
    {
        return $this->hasMany(RltMstAgensUsers::class, 'mst_agens_id');
    }

    public function discount()
    {
        return $this->belongsTo(MstDiscount::class, 'mst_discount_id');
    }

    public static function getTableName()
    {
        return with(new Static)->getTable();
    }

    public static function getPrimaryKey()
    {
        return with(new Static)->getKeyName();
    }
}
