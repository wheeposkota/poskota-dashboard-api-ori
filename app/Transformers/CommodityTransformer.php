<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Collection;

class CommodityTransformer extends Transformer
{
    const PUBLISH = true;
    const PERSONAL = false;

    public function listCommodity(Collection $data, bool $is_publish = false)
    {
        return $data->map(function ($item) use ($is_publish) {
            $i = [
                'id' => $item->id,
                'type_id' => $item->type_id,
                'content' => $item->content,
                'price' => $item->price,
                'type_name' => (string)$item->type_name,
                'created_at' => $item->created_at->format('d M Y'),
            ];

            if ($is_publish) {
                $i['log'] = $item->log;
                $i['price_before'] = $item->price_before;
            }

            return $i;
        });
    }
}
