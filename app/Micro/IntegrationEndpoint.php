<?php

namespace App\Micro;

use App\Events\TransactionSettled;
use App\Models\NotificationPayment;
use App\Models\TrxOrder;
use App\Services\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IntegrationEndpoint extends Controller
{
    public function notification(Request $request)
    {
        list($transaction, $fraud, $type, $order_id) = Payment::notifHandler();

        $message = "";
        $trx_status = null;

        if ($transaction == Payment::STATUS_CAPTURE) {
            // For credit card transaction, we need to check whether transaction is challenge by FDS or not
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {
                    // TODO set payment status in merchant's database to 'Challenge by FDS'
                    // TODO merchant should decide whether this transaction is authorized or not in MAP
                    $message .= "Transaction order_id: " . $order_id . " is challenged by FDS";
                } else {
                    // TODO set payment status in merchant's database to 'Success'
                    $message .= "Transaction order_id: " . $order_id . " successfully captured using " . $type;
                    $trx_status = Payment::TRX_DONE;
                }
            }
        } else if ($transaction == Payment::STATUS_SETTLEMENT) {
            // TODO set payment status in merchant's database to 'Settlement'
            $message .= "Transaction order_id: " . $order_id . " successfully transfered using " . $type;
            $trx_status = Payment::TRX_DONE;
        } else if ($transaction == Payment::STATUS_PENDING) {
            // TODO set payment status in merchant's database to 'Pending'
            $message .= "Waiting customer to finish transaction order_id: " . $order_id . " using " . $type;
            $trx_status = Payment::TRX_PENDING;
        } else if ($transaction == Payment::STATUS_DENY) {
            // TODO set payment status in merchant's database to 'Denied'
            $message .= "Payment using " . $type . " for transaction order_id: " . $order_id . " is denied.";
        } else if ($transaction == Payment::STATUS_EXPIRE) {
            // TODO set payment status in merchant's database to 'expire'
            $message .= "Payment using " . $type . " for transaction order_id: " . $order_id . " is expired.";
            $trx_status = Payment::TRX_EXPIRE;
        } else if ($transaction == Payment::STATUS_CANCEL) {
            // TODO set payment status in merchant's database to 'Denied'
            $message .= "Payment using " . $type . " for transaction order_id: " . $order_id . " is canceled.";
            $trx_status = Payment::TRX_EXPIRE;
        } else if ($transaction == Payment::STATUS_REFUND) { //special case gopay
            // TODO set payment status in merchant's database to 'Refund'
            $message .= "Payment using " . $type . " for transaction order_id: " . $order_id . " is refund.";
            $trx_status = Payment::TRX_EXPIRE;
        }

        if ($transaction == Payment::STATUS_CAPTURE) {
            if ($fraud == 'challenge') {
                // TODO Set payment status in merchant's database to 'challenge'
                $message .= "Transaction order_id: " . $order_id . " is challenge";
            } else if ($fraud == 'accept') {
                // TODO Set payment status in merchant's database to 'success'
                $message .= "Transaction order_id: " . $order_id . " is success";
                $trx_status = Payment::TRX_DONE;
            }
        } else if ($transaction == Payment::STATUS_CANCEL) {
            if ($fraud == 'challenge') {
                // TODO Set payment status in merchant's database to 'failure'
                $message .= "Payment using " . $type . " for transaction order_id: " . $order_id . " is failure.";
                $trx_status = Payment::TRX_EXPIRE;
            } else if ($fraud == 'accept') {
                // TODO Set payment status in merchant's database to 'failure'
                $message .= "Payment using " . $type . " for transaction order_id: " . $order_id . " is failure.";
                $trx_status = Payment::TRX_EXPIRE;
            }
        } else if ($transaction == Payment::STATUS_DENY) {
            // TODO Set payment status in merchant's database to 'failure'
            $message .= "Payment using " . $type . " for transaction order_id: " . $order_id . " is failure.";
        }

        NotificationPayment::create([
            'order_id' => $order_id,
            'status' => $transaction,
            'message' => $message,
            'response' => $request->all()
        ]);


        $order = TrxOrder::find($order_id);
        if (!$order) { //handle if not found
            Log::info('notif-midtrans', [
                's' => 'trx not found',
                'r' => $request->all(),
                'p' => [$transaction, $fraud, $type, $order]
            ]);
            return;
        }

        if (is_null($trx_status)) { //status is invalid
            Log::info('notif-midtrans', [
                's' => 'status invalid',
                'r' => $request->all(),
                'p' => [$transaction, $fraud, $type, $order]
            ]);
            return;
        }

        if ($trx_status == Payment::TRX_DONE) {
            event(new TransactionSettled($order_id));
        }

        //set the status transaction
        $order->order_status_id = Payment::getStatus($trx_status);
        $order->save();
    }
}
