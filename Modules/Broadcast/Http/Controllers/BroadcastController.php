<?php

namespace Modules\Broadcast\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\CoreController;
use Gdevilbat\SpardaCMS\Modules\Core\Repositories\Repository;

class BroadcastController extends CoreController
{
    public function __construct(\Gdevilbat\SpardaCMS\Modules\Core\Entities\Setting $setting)
    {
        parent::__construct();
        $this->setting_m = $setting;
        $this->setting_repository = new Repository(new $setting, resolve(\Gdevilbat\SpardaCMS\Modules\Role\Repositories\Contract\AuthenticationRepository::class));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('broadcast::admin.'.$this->data['theme_cms']->value.'.content.setting', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->except('_token', '_method');

        foreach ($data as $key => $value) 
        {
            $length = strlen(json_encode($value));
            if($length > 65535)
            {
                $validator->errors()->add($key, $key.' Max Lenght 65,535 Characters');

                return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
            }
        }

        $settings = $this->setting_repository->all();
        foreach ($data as $key => $value) 
        {
            $filtered = $settings->where('name', $key);
            if($filtered->count() > 0)
            {
                $setting = $this->setting_m->where('name', $key);
                if(!$setting->update(['value' => json_encode($value)]))
                {
                    return redirect()->back()->with('global_message',['status' => 400, 'message' => 'Failed To Update '.$key]);
                }
            }
            else
            {
                $setting = new $this->setting_m;
                $setting['name'] = $key;
                $setting['value'] = $value;
                if(!$setting->save())
                {
                    return redirect()->back()->with('global_message',['status' => 400, 'message' => 'Failed To Create '.$key]);
                }
            }
        }

        return redirect(action('\\'.get_class($this).'@create'))->with('global_message',['status' => 200, 'message' => 'Success To Update Setting']);
    }
}
