<?php

namespace Modules\Payment\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\CoreController;

use App\Models\TrxOrder as TrxOrder_m;
use App\Models\TrxOrderMeta as TrxOrderMeta_m;
use App\Models\MstOrderStatus as MstOrderStatus_m;

use Modules\Payment\Traits\MidtransPayment;

use Log;

class PaymentController extends CoreController
{
    use MidtransPayment;
  
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function ePayment(Request $request)
    {
        $this->validate($request, [
          'code' =>'required',
          'repository' => 'required',
        ]);

        $this->configMidtransPayment();

        $trx_order = TrxOrder_m::findOrFail(decrypt($request->input('code')));

        $className = decrypt($request->input('repository'));

        $trx_repository = new $className($trx_order);

        if(!($trx_repository instanceof \Modules\Payment\Repositories\Contract\TransactionPayment))
          throw new \Exception("Trasaction Repository Must Be Implement Interface Modules\Payment\Repositories\Contract\TransactionPayment");
          
        try {
            $status = \Midtrans\Transaction::status($trx_order->getKey());
            if(!empty($status))
            {
              if($status->transaction_status == 'pending')
              {
                if($status->payment_type == 'bank_transfer')
                {
                    $message = 'Mohon Selesai kan Pembayaran Berikut : ';

                    foreach ($status->va_numbers as $key => $va_number) 
                    {
                      $message.= "<ul>";
                      $message.= "<li>Bank Name : ".$va_number->bank."</li>";
                      $message.= "<li>Virtual Number : ".$va_number->va_number."</li>";
                      $message.= "<li>Total : ".sprintf('%s %s', $status->currency, number_format($status->gross_amount))."</li>";
                      $message.= "</ul>";
                    }

                  return redirect($trx_repository->getErrorPendingUrl());
                }
                elseif($status->payment_type == 'cstore')
                {
                    $message = 'Mohon Selesai kan Pembayaran Berikut : ';

                    $message.= "<ul>";
                    $message.= "<li>Merchant : ".$status->store."</li>";
                    $message.= "<li>Payment Code : ".$status->payment_code."</li>";
                    $message.= "<li>Total : ".sprintf('%s %s', $status->currency, number_format($status->gross_amount))."</li>";
                    $message.= "</ul>";

                    return redirect($trx_repository->getErrorPendingUrl());
                }
              }
              elseif(($status->transaction_status == 'capture' || $status->transaction_status == 'settlement') && $status->fraud_status == 'accept')
              {
                    return redirect($trx_repository->getErrorSettlement());
              }
              elseif($status->transaction_status == 'expire' || $status->transaction_status == 'cancel')
              {
                  if($trx_order->delete())
                    return redirect($trx_repository->getErrorExpireUrl())->with('global_message', array('status' => 400,'message' => 'Transaction Has Been Expired For Payment, Try To Create New Transaction'));
              }
            }
        } catch (\Exception $e) {
          if($e->getCode() == 404)
          {
            $params = array(
                'transaction_details' => array(
                    'order_id' =>   $trx_order->getKey(),
                    'gross_amount' => $trx_order->trx_order_total,
                ),
                'callbacks' => array(
                    'finish' => $trx_repository->getRedirectUrl()
                ),
                'enabled_payments' => ["credit_card", "permata_va", "bca_va", "bni_va", "other_va", "gopay", "indomaret", "akulaku"],
            );

            $paymentUrl = \Midtrans\Snap::createTransaction($params)->redirect_url;

            return redirect($paymentUrl);
          }

          throw new \Exception($e->getMessage());
        }
    }

    public function inCash(Request $request)
    {
      try {

        $trx_order = TrxOrder_m::findOrFail(decrypt($request->input('code')));
        $mst_order_status = MstOrderStatus_m::where('status_name', 'Pembayaran Dikonfirmasi')->firstOrFail();

        $className = decrypt($request->input('repository'));

        $trx_repository = new $className($trx_order);

        if(!($trx_repository instanceof \Modules\Payment\Repositories\Contract\TransactionPayment))
          throw new \Exception("Trasaction Repository Must Be Implement Interface Modules\Payment\Repositories\Contract\TransactionPayment");

        $trx_order->order_status_id = $mst_order_status->getKey();

        if($trx_order->save())
        {
          return redirect($trx_repository->getRedirectUrl());
        }
      } catch (\Exception $e) {
            if($e->getCode() != 404)
                throw new \Exception($e->getMessage());
      }
    }

    public function webhook(Request $request)
    {
        $notif = new \Midtrans\Notification();

        $transaction = $notif->transaction_status;
        $fraud = $notif->fraud_status;

        Log::info("Order ID $notif->order_id: "."transaction status = $transaction, fraud staus = $fraud");

        if ($transaction == 'capture') {
            if ($fraud == 'challenge') {
              // TODO Set payment status in merchant's database to 'challenge'
            }
            else if ($fraud == 'accept') {
              // TODO Set payment status in merchant's database to 'success'
            }
        }
        else if ($transaction == 'cancel') {
            if ($fraud == 'challenge') {
              // TODO Set payment status in merchant's database to 'failure'
            }
            else if ($fraud == 'accept') {
              // TODO Set payment status in merchant's database to 'failure'
            }
        }
        else if ($transaction == 'deny') {
              // TODO Set payment status in merchant's database to 'failure'
        }
    }

    public function callback(Request $request)
    {
      $order = TrxOrder_m::findOrFail($request->input('order_id'));

      try {
        //$status = MstOrderStatus_m::where(['status_name' => 'Pembayaran Dikonfirmasi'])->firstOrFail();
      } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e){
          throw new \Exception($e->getMessage());
      }

        //$order->order_status_id = $status->getKey();
      if($order->save())
      {
          $trx_meta = TrxOrderMeta_m::where(['trx_order_id' => $order->getKey(), 'meta_key' => 'callback_response'])->first();

          if(empty($trx_meta))
            $trx_meta = new TrxOrderMeta_m;

          $trx_meta->meta_key = 'callback_response';
          $trx_meta->meta_value = ['status_code' =>  $request->input('status_code'), 'transaction_status' => $request->input('transaction_status')];
          $trx_meta->trx_order_id = $order->getKey();
          $trx_meta->save();
      }

      return redirect(urldecode($request->input('redirect_url')));

    }
}
