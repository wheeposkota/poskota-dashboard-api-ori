<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrxOrderMeta extends Model
{
    protected $table = 'trx_orders_meta';

    const KEY_PAYLOAD = 'detail';
    const KEY_ITEM = 'item';
    const KEY_PARAM = 'parameter';
    const KEY_RESPONSE = 'response';
    const KEY_BANK_ACCOUNT = 'bank_account';
    const KEY_GOPAY_ACTION = 'gopay_action';
    const KEY_TYPE = 'type';
    const KEY_CONFIRMATION = 'confirmation';

    const PAYMENT_INFO = 'payment_info';

    const PAYLOAD_EPAPER = 'payload_epaper';

    const TYPE_EPAPER = 'epaper';

    protected $fillable = [
        'trx_order_id',
        'meta_key',
        'meta_value',
    ];

    protected $casts = [
        'meta_value' => 'array',
    ];

    public function trx_order()
    {
        return $this->belongsTo(TrxOrder::class, 'trx_order_id');
    }
}
