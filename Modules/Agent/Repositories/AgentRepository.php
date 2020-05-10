<?php

namespace Modules\Agent\Repositories;

use Gdevilbat\SpardaCMS\Modules\Core\Repositories;
use Gdevilbat\SpardaCMS\Modules\Core\Repositories\AbstractRepository;

use Auth;
use Modules\Agent\Entities\MstDiscount;

/**
 * Class EloquentCoreRepository
 *
 * @package Gdevilbat\SpardaCMS\Modules\Core\Repositories\Eloquent
 */
class AgentRepository extends AbstractRepository
{
    public function getUserDiscount(\App\User $user)
    {
        if($this->isAgent($user))
        {
            try {
                return MstDiscount::firstOrFail()->mst_discount_total;
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                throw new \Exception($e->getMessage());
            }
        }
        else
        {
            return 0;
        }
    }

    public function isAgent(\App\User $user)
    {
        $user->load('agent');

        if($user->role->first()->slug == 'agen-iklan')
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}           