<?php

namespace App\Services;

use App\Models\EPaperSubscription;
use App\Models\MstOrderStatus;
use App\Models\TrxOrder;
use App\Models\TrxOrderMeta;
use Carbon\Carbon;
use Exception;
use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Notification;
use App\Events\TransactionNotification;

class Payment
{
    const BANK_BCA = 'bca';
    const BANK_MANDIRI = 'mandiri';
    const BANK_PERMATA = 'permata';
    const BANK_BNI = 'bni';
    const GOPAY = 'gopay';

    const STATUS_CAPTURE = 'capture';
    const STATUS_SETTLEMENT = 'settlement';
    const STATUS_PENDING = 'pending';
    const STATUS_DENY = 'deny';
    const STATUS_EXPIRE = 'expire';
    const STATUS_CANCEL = 'cancel';
    const STATUS_REFUND = 'refund';

    const TRX_PENDING = 'Menunggu';
    const TRX_CONFIRM = 'Pembayaran Dikonfirmasi';
    const TRX_DONE = 'Lunas';
    const TRX_APPROVED = 'Approved Editor';
    const TRX_EXPIRE = 'Sesi Ditutup';

    const TYPE_EPAPER = 'epaper';

    const METHOD_TRANSFER = 'transfer';
    const METHOD_GOPAY = 'gopay';

    public static function getStatus($status_name)
    {
        $statusses = [
            self::TRX_PENDING,
            self::TRX_CONFIRM,
            self::TRX_DONE,
            self::TRX_APPROVED,
            self::TRX_EXPIRE,
        ];
        if (!in_array($status_name, $statusses))
            throw new \Exception('Invalid transaction status');

        $status = MstOrderStatus::where('status_name', $status_name)->first();
        if (!$status)
            $status = MstOrderStatus::create([
                'status_name' => $status_name,
            ]);

        return $status->getKey();
    }

    public static function notifHandler()
    {
        self::setupConfig();

        $notif = new Notification();

        $transaction = $notif->transaction_status;
        $fraud = $notif->fraud_status;
        $type = $notif->payment_type;
        $order_id = $notif->order_id;

        event(new TransactionNotification($notif));

        return [
            $transaction,
            $fraud,
            $type,
            $order_id,
        ];
    }

    public static function chargePayment($payload)
    {
        //create transaction row
        $status_id = Payment::getStatus(Payment::TRX_PENDING);
        try {
            $trx = TrxOrder::create([
                'trx_order_quantity' => 1,
                'trx_order_price' => $payload['amount'],
                'trx_order_discount' => 0,
                'trx_order_date' => date('Y-m-d H:i:s'),
                'order_status_id' => $status_id,
            ]);
        } catch (Exception $e) {
            return [false, null, $e->getMessage()];
        }

        //request builder
        $transaction_detail = [
            'order_id' => $trx->getKey(),
            'gross_amount' => $payload['amount']
        ];

        if ($payload['type'] == self::TYPE_EPAPER) {
            $item_detail = [
                'id' => $payload['package']->getKey(),
                'price' => $payload['amount'],
                'quantity' => 1,
                'name' => $payload['package']->package_name,
            ];
        }

        $transaction_data = [
            'transaction_details' => $transaction_detail,
            'item_details' => [$item_detail],
        ];

        //charge
        try {
            $response = self::paymentCharge($payload, $transaction_data);
        } catch (Exception $e) {
            return [false, null, $e->getMessage()];
        }

        //get payload
        if ($payload['method'] == self::METHOD_TRANSFER) {
            $va = null;
            switch ($payload['method_payment']) {
                case Payment::BANK_MANDIRI:
                    $va = $response->biller_code . $response->bill_key ?? '-';
                    break;
                case Payment::BANK_PERMATA:
                    $va = $response->permata_va_number ?? '-';
                    break;
                case Payment::BANK_BNI:
                case Payment::BANK_BCA:
                default:
                    $va = $response->va_numbers[0]->va_number ?? '-';
                    break;
            }
            $result = $va;
        }
        if ($payload['method'] == self::METHOD_GOPAY) {
            $result = $response->actions;
        }

        //logging meta
        TrxOrderMeta::create([
            'trx_order_id' => $trx->getKey(),
            'meta_key' => TrxOrderMeta::KEY_PAYLOAD,
            'meta_value' => $transaction_detail,
        ]);

        TrxOrderMeta::create([
            'trx_order_id' => $trx->getKey(),
            'meta_key' => TrxOrderMeta::KEY_ITEM,
            'meta_value' => $payload['package'],
        ]);

        TrxOrderMeta::create([
            'trx_order_id' => $trx->getKey(),
            'meta_key' => TrxOrderMeta::KEY_PARAM,
            'meta_value' => $payload['request'],
        ]);

        if ($payload['type'] == self::TYPE_EPAPER) {
            TrxOrderMeta::create([
                'trx_order_id' => $trx->getKey(),
                'meta_key' => TrxOrderMeta::KEY_TYPE,
                'meta_value' => TrxOrderMeta::TYPE_EPAPER,
            ]);

            // create epaper_subscription payload
            $days = (int) ($payload['package']->package_period ?? 0);
            $start_date = $payload['request']['start_date'];
            $end_date = Carbon::createFromFormat('Y-m-d', $start_date)->addDays($days)->format('Y-m-d');

            TrxOrderMeta::create([
                'trx_order_id' => $trx->getKey(),
                'meta_key' => TrxOrderMeta::PAYLOAD_EPAPER,
                'meta_value' => [
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'order_id' => $trx->getKey(),
                    'package_id' => $payload['request']['package_id'],
                    'member_id' => $payload['user']->getKey() ?? 0,
                ],
            ]);
        }

        TrxOrderMeta::create([
            'trx_order_id' => $trx->getKey(),
            'meta_key' => TrxOrderMeta::KEY_RESPONSE,
            'meta_value' => $response,
        ]);

        if ($payload['method'] == self::METHOD_TRANSFER) {
            TrxOrderMeta::create([
                'trx_order_id' => $trx->getKey(),
                'meta_key' => TrxOrderMeta::KEY_BANK_ACCOUNT,
                'meta_value' => [
                    'bank' => $payload['method_payment'],
                    'account' => $result,
                ],
            ]);
        }

        if ($payload['method'] == self::METHOD_GOPAY) {
            TrxOrderMeta::create([
                'trx_order_id' => $trx->getKey(),
                'meta_key' => TrxOrderMeta::KEY_GOPAY_ACTION,
                'meta_value' => [
                    'action' => $result,
                ],
            ]);
        }

        $transaction_data['trx_id'] = $trx->getKey();

        return [true, $transaction_data, $result];
    }

    private static function paymentCharge($payload, $transaction_data)
    {
        self::setupConfig();

        $transaction_data['payment_type'] = 'bank_transfer';
        switch ($payload['method_payment']) {
            case Payment::GOPAY:
                $transaction_data['payment_type'] = 'gopay';
                $transaction_data['gopay'] = [
                    'enable_callback' => true,
                    'callback_url' => 'POSKOTADEV://poskota.tv',
                ];
                break;
            case Payment::BANK_MANDIRI:
                $transaction_data['payment_type'] = 'echannel';
                $transaction_data['echannel'] = [
                    'bill_info1' => 'Untuk pembayaran:', //10
                    'bill_info2' => 'PoskotaNews' //30
                ];
                break;
            case Payment::BANK_BNI:
                $transaction_data['bank_transfer']['bank'] = 'bni';
                break;
            case Payment::BANK_PERMATA:
                $transaction_data['bank_transfer']['bank'] = 'permata';
                break;
            case Payment::BANK_BCA:
            default:
                $transaction_data['bank_transfer']['bank'] = 'bca';
                break;
        }

        $response = CoreApi::charge($transaction_data);

        return $response;
    }

    private static function setupConfig()
    {
        // Set your Merchant Server Key
        Config::$serverKey = config('services.midtrans');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        Config::$isProduction = false;
        // Set sanitization on (default)
        Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        Config::$is3ds = false;

        //override notification handler endpoint
        if (false) {
            $notificationHandler = sprintf('%s/api/payment/notification', config('app.url'));
            Config::$curlOptions[CURLOPT_HTTPHEADER] = [
                'X-Override-Notification' => $notificationHandler,
            ];
        }
    }
}
