<?php

namespace App\Transformers;

use App\Models\EPaper;
use App\Models\TrxOrder;
use App\Models\MstOrderStatus;
use App\Models\TrxOrderMeta;
use App\Models\EPaperPackage;
use Illuminate\Database\Eloquent\Collection;

class EpaperTransformer extends Transformer
{
    public function listEPaper(Collection $data)
    {
        return $data->map(function ($item) {
            return [
                'placeholder' => no_pic('epaper'),
                'title' => $item->epaper_title,
                'edition' => $item->epaper_edition,
                'file' => generate_cdn($item->epaper_file),
            ];
        });
    }

    public function detailEPaper(EPaper $data)
    {
        return [
            'placeholder' => no_pic('epaper'),
            'title' => $data->epaper_title,
            'edition' => $data->epaper_edition,
            'file' => generate_cdn($data->epaper_file),
        ];
    }

    public function packageEPaper(Collection $data)
    {
        return $data->map(function (EPaperPackage $item) {
            return [
                'id' => $item->id,
                'name' => $item->package_name,
                'price' => (int) $item->package_price,
                'period' => (int) $item->package_period,
                'description' => $item->package_description,
            ];
        });
    }

    public function packageEPaperDetail(EPaperPackage $data)
    {
        return [
            'id' => $data->id,
            'name' => $data->package_name,
            'price' => (int) $data->package_price,
            'period' => (int) $data->package_period,
            'description' => $data->package_description,
        ];
    }

    public function listEPaperSubs(Collection $data)
    {
        $statusses = MstOrderStatus::whereIn('id', $data->unique('order_status_id')->pluck('order_status_id'))->get();

        return $data->map(function (TrxOrder $item) use ($statusses) {

            $package = [
                'name' => '',
                'period' => 0,
                'description' => '',
            ];

            $subs = [
                'start_date' => '',
                'end_date' => '',
            ];

            $method = '';

            $payload = [];

            $status = $statusses->where('id', $item->order_status_id)->first()->status_name;

            foreach($item->meta as $meta) {
                switch ($meta->meta_key) {
                    case TrxOrderMeta::KEY_ITEM:
                        $package = [
                            'name' => $meta->meta_value['package_name'],
                            'period' => (int) $meta->meta_value['package_period'],
                            'description' => $meta->meta_value['package_description'],
                        ];
                    break;
                    case TrxOrderMeta::PAYLOAD_EPAPER:
                        $subs = [
                            'start_date' => $meta->meta_value['start_date'],
                            'end_date' => $meta->meta_value['end_date'],
                        ];
                    break;
                    case TrxOrderMeta::KEY_GOPAY_ACTION:
                        $method = 'gopay';
                        $payload = $meta->meta_value['action'];
                    break;
                    case TrxOrderMeta::KEY_BANK_ACCOUNT:
                        $method = 'bank';
                        $payload = $meta->meta_value;
                    break;
                }
            }

            return [
                'id' => $item->id,
                'price' => (int) $item->trx_order_price,
                'order_date' => $item->trx_order_date,
                'status' => $status,
                'package' => $package,
                'subs' => $subs,
                'method' => $method,
                'payload' => $payload,
            ];
        });
    }
}
