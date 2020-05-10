<?php

namespace Modules\Payment\Traits;

trait MidtransPayment {
	public function configMidtransPayment()
	{
		// Set your Merchant Server Key
        \Midtrans\Config::$serverKey = config('services.midtrans');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;
	}
}