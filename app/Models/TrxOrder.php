<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Hafael\LaraFlake\Traits\LaraFlakeTrait;

class TrxOrder extends Model
{
    use LaraFlakeTrait;

    protected $table = 'trx_orders';
    public $incrementing = false;

    protected $fillable = [
        'trx_order_quantity',
        'trx_order_price',
        'trx_order_discount',
        'trx_order_date',
        'order_status_id',
    ];

    const TRX_PENDING = 'Menunggu';
    const TRX_CONFIRM = 'Pembayaran Dikonfirmasi';
    const TRX_DONE = 'Lunas';
    const TRX_APPROVED = 'Approved Editor';
    const TRX_EXPIRE = 'Sesi Ditutup';
    const TRX_CANCEL = 'Batal';

    public function getTrxOrderTotalAttribute()
    {
        return ($this->trx_order_quantity * $this->trx_order_price) * (100-$this->trx_order_discount) / 100;
    }

    public function trxAdClassics()
    {
        return $this->hasMany(\Modules\Ads\Entities\TrxAdsClassic::class, 'order_id');
    }

    public function trxAdWeb()
    {
        return $this->hasOne(\Modules\Ads\Entities\TrxAdsWeb::class, 'order_id');
    }

    public function mstAdStatus()
    {
    	return $this->belongsTo(MstOrderStatus::class, 'order_status_id');
    }

    public function notificationPayments()
    {
        return $this->hasMany(NotificationPayment::class, 'order_id');
    }

    public function meta()
    {
        return $this->hasMany(TrxOrderMeta::class, 'trx_order_id');
    }

    public function members()
    {
        return $this->belongsToMany(Member::class, 'rlt_ads_member', 'order_id', 'member_id');
    }

    public function getInvoiceNumberAttribute()
    {
        if(empty($this->order_number))
            return $this->getKey();

        return $this->order_number;
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
