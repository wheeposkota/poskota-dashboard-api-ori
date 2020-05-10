<?php

namespace Modules\Agent\Facades;

use Illuminate\Support\Facades\Facade;

class AgentRepository extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Modules\Agent\Repositories\AgentRepository::class;
    }
}
