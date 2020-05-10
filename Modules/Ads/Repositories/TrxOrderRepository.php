<?php

namespace Modules\Ads\Repositories;

use Gdevilbat\SpardaCMS\Modules\Core\Repositories;
use Gdevilbat\SpardaCMS\Modules\Core\Repositories\AbstractRepository;

use Carbon\Carbon;

/**
 * Class EloquentCoreRepository
 *
 * @package Gdevilbat\SpardaCMS\Modules\Core\Repositories\Eloquent
 */
class TrxOrderRepository extends AbstractRepository
{
	public function getOrderNumber()
	{
		$check_number = $this->model->where('order_number', 'LIKE', '%'.Carbon::now()->format('ymd').'%');

		if($check_number->count() == 0)
		{
			$order_number = Carbon::now()->format('ymd').'0001';
		}
		else
		{
			$used_number = $check_number->pluck('order_number');

			$integer_un = $used_number->map(function($item, $key){
				return (integer) str_replace(Carbon::now()->format('ymd'), '', $item);
			});

			$available_number = collect(range(1, 9999))->diff($integer_un);

			$digit = 4;
			$number = (string) $available_number->first();
			$order_number = $number;

			for ($i= 1; $i <= ($digit - strlen($number)) ; $i++) { 
				$order_number = '0'.$order_number;
			}

			$order_number = Carbon::now()->format('ymd').$order_number;
		}

		$validation_number = $this->model->where('order_number', $order_number)->count();

		if($validation_number > 0)
			return $this->getOrderNumber();

		return $order_number;
	}
}
