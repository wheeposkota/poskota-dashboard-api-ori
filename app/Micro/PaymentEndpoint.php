<?php

namespace App\Micro;

use App\Models\EPaperPackage;
use App\Services\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentEndpoint extends Controller
{
    public function bankAccount()
    {
        $acc = [
            [
                'bank' => 'BNI 46',
                'cabang' => 'Harmoni',
                'no' => '18290075',
                'an' => 'PT Media Antarkota Jaya',
            ],
            [
                'bank' => 'BCA',
                'cabang' => 'Gajah Mada',
                'no' => '0123025781',
                'an' => 'PT Media Antarkota Jaya',
            ],
            [
                'bank' => 'Mandiri',
                'cabang' => 'Duta Merlin',
                'no' => '1210003031428',
                'an' => 'PT Media Antarkota Jaya',
            ],
        ];
        return $this->res($acc, true);
    }

    public function checkout(Request $request)
    {
        $rules = [
            'bank' => 'required|in:bca,mandiri,permata,bni',
            'type' => 'required|in:epaper',
        ];
        if ($request->get('type', '') == Payment::TYPE_EPAPER) {
            $rules['start_date'] = 'required|date:Y-m-d';
            $rules['package_id'] = 'required|numeric';
        }
        $this->validate($request, $rules);

        $bank_type = $request->get('bank');
        $type = $request->get('type');

        $payload = [
            'type' => $type,
            'method' => Payment::METHOD_TRANSFER,
            'request' => $request->all(),
            'user' => Auth::user()
        ];
        if ($type == Payment::TYPE_EPAPER) {
            $package_id = $request->get('package_id');
            $package = EPaperPackage::findOrFail($package_id);
            $payload['package'] = $package;
            $payload['amount'] = $package->package_price;
        }
        $payload['method_payment'] = $bank_type;

        list($success, $transaction, $result) = Payment::chargePayment($payload);

        if (!$success)
            return $this->res($result);

        $result = [
            'transaction' => $transaction,
            'va' => $result,
            'bank_name' => $bank_type,
        ];

        return $this->res($result, true);
    }

    public function checkoutGopay(Request $request)
    {
        $rules = [
            'type' => 'required|in:epaper',
        ];
        if ($request->get('type', '') == Payment::TYPE_EPAPER) {
            $rules['start_date'] = 'required|date:Y-m-d';
            $rules['package_id'] = 'required|numeric';
        }
        $this->validate($request, $rules);

        $type = $request->get('type');

        $payload = [
            'type' => $type,
            'method' => Payment::METHOD_GOPAY,
            'request' => $request->all(),
            'user' => Auth::user()
        ];
        if ($type == Payment::TYPE_EPAPER) {
            $package_id = $request->get('package_id');
            $package = EPaperPackage::findOrFail($package_id);
            $payload['package'] = $package;
            $payload['amount'] = $package->package_price;
        }
        $payload['method_payment'] = Payment::GOPAY;

        list($success, $transaction, $result) = Payment::chargePayment($payload);

        if (!$success)
            return $this->res($result);

        $result = [
            'transaction' => $transaction,
            'action' => $result,
        ];

        return $this->res($result, true);
    }
}
