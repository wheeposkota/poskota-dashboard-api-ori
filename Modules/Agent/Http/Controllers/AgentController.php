<?php

namespace Modules\Agent\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\CoreController;
use Gdevilbat\SpardaCMS\Modules\Core\Repositories\Repository;

use Gdevilbat\SpardaCMS\Modules\Core\Entities\User as User_m;
use Modules\Agent\Entities\MstAgens as MstAgens_m;
use Modules\Agent\Entities\RltMstAgensUsers as RltMstAgensUsers_m;

use Validator;
use DB;
use View;
use Auth;

class AgentController extends CoreController
{
    public function __construct(\Modules\Agent\Repositories\AgentRepository $repository)
    {
        parent::__construct();
        $this->agent_repository = $repository;
        $this->agent_repository->setModule('agent');
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('agent::admin.'.$this->data['theme_cms']->value.'.content.master', $this->data);
    }

    public function serviceMaster(Request $request)
    {
        $column = [MstAgens_m::getPrimaryKey(), 'mst_ptlagen_name', 'mst_ptlagen_name', 'mst_ptlagen_region','total_staff','created_at'];;

        $length = !empty($request->input('length')) ? $request->input('length') : 10 ;
        $column = !empty($request->input('order.0.column')) ? $column[$request->input('order.0.column')] : MstAgens_m::getPrimaryKey() ;
        $dir = !empty($request->input('order.0.dir')) ? $request->input('order.0.dir') : 'DESC' ;
        $searchValue = $request->input('search')['value'];

        $query = $this->agent_repository->buildQueryByCreatedUser([])
                                        ->with('users')
                                        ->orderBy($column, $dir);

        $recordsTotal = $query->count();
        $filtered = $query;

        if($searchValue)
        {
            $filtered->where(function($query) use ($searchValue){
                        $query->where(DB::raw("CONCAT(mst_ptlagen_name,'-',mst_ptlagen_name,'-',mst_ptlagen_region,'-',ifnull(".$this->agent_repository::getTableName().".created_at,''))"), 'like', '%'.$searchValue.'%')
                                ;

                    });
        }

        $filteredTotal = $filtered->count();

        $this->data['length'] = $length;
        $this->data['column'] = $column;
        $this->data['dir'] = $dir;
        $this->data['agents'] = $filtered->offset($request->input('start'))->limit($length)->get();

        $table =  $this->parsingDataTable($this->data['agents']);

        return ['data' => $table, 'draw' => (integer)$request->input('draw'), 'recordsTotal' => $recordsTotal, 'recordsFiltered' => $filteredTotal];
    }

    public function parsingDataTable($agents)
    {
        /*=========================================
        =            Parsing Datatable            =
        =========================================*/
            
            $data = array();
            $i = 0;
            foreach ($agents as $key_agent => $agent) 
            {
                if(Auth::user()->can('read-agent', $agent))
                {
                    $data[$i][] = $agent->getKey();
                    $data[$i][] = $agent->mst_ptlagen_name;
                    $data[$i][] = $agent->mst_ptlagen_address;
                    $data[$i][] = $agent->mst_ptlagen_region;
                    $data[$i][] = $agent->users->count();

                    if(!empty($agent->created_at))
                    {
                        $data[$i][] = $agent->created_at->toDateTimeString();
                    }
                    else
                    {
                        $data[$i][] = '-';
                    }

                    $data[$i][] = $this->getActionTable($agent);
                    $i++;
                }
            }

            return $data;
        
        /*=====  End of Parsing Datatable  ======*/
    }

    public function getActionTable($agent)
    {
        $view = View::make('agent::admin.'.$this->data['theme_cms']->value.'.content.service_master', [
            'agent' => $agent
        ]);

        $html = $view->render();
       
       return $html;
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {
        $this->data['method'] = method_field('POST');
        $this->data['users'] = User_m::with('role', 'agent')->whereDoesntHave('role', function($query){
                                                                $query->where('slug', 'super-admin')
                                                                    ->orWhere('slug', 'admin');
                                                            })
                                                        ->get();
        if(isset($_GET['code']))
        {
            $this->data['agent'] = $this->agent_repository->with('users')->findOrFail(decrypt($request->input('code')));
            $this->data['method'] = method_field('PUT');
            $this->authorize('update-agent', $this->data['agent']);
        }

        return view('agent::admin.'.$this->data['theme_cms']->value.'.content.form', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'mst_ptlagen_name' => 'required|max:191',
            'mst_ptlagen_address' => 'required',
            'mst_ptlagen_region' => 'required|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        if($request->isMethod('POST'))
        {
            $data = $request->except('_token', '_method', 'user_id');
            $agent = new MstAgens_m;
        }
        else
        {
            $data = $request->except('_token', '_method', 'user_id', 'id');
            $agent = $this->agent_repository->findOrFail(decrypt($request->input('id')));
            $this->authorize('update-agent', $agent);
        }

        foreach ($data as $key => $value) 
        {
            $agent->$key = $value;
        }

        if($request->isMethod('POST'))
        {
            $agent->created_by = Auth::user()->id;
        }

        $agent->modified_by = Auth::user()->id;

        if($agent->save())
        {

            if($request->has('user_id'))
            {
                $rlt_mst_agens_users = [];
                foreach ($request->input('user_id') as $key => $value) 
                {
                    $rlt_mst_agens_user = RltMstAgensUsers_m::where(['user_id' => decrypt($value), 'mst_agens_id' => $agent->getKey()])->first();
                    if(empty($rlt_mst_agens_user))
                    {
                        $rlt_mst_agens_user = new RltMstAgensUsers_m;
                        $rlt_mst_agens_user->created_by = Auth::user()->id;
                    }

                    $rlt_mst_agens_user->user_id = decrypt($value);

                    if($request->isMethod('POST'))
                    {
                        $rlt_mst_agens_user->created_by = Auth::user()->id;
                    }

                    $rlt_mst_agens_user->modified_by = Auth::user()->id;

                    $rlt_mst_agens_users[] = $rlt_mst_agens_user;
                }

                $agent->rltMstAgentUser()->saveMany($rlt_mst_agens_users);


                $remove_related_relation = RltMstAgensUsers_m::where('mst_agens_id', $agent->getKey())
                                                        ->whereNotIn('user_id', collect($rlt_mst_agens_users)->pluck('user_id'))
                                                        ->pluck('id');

                RltMstAgensUsers_m::whereIn(RltMstAgensUsers_m::getPrimaryKey(), $remove_related_relation)->delete();
            }
            else
            {
                RltMstAgensUsers_m::where('mst_agens_id', $agent->getKey())->delete();
            }
            
            if($request->isMethod('POST'))
            {
                return redirect(action('\Modules\Agent\Http\Controllers\AgentController@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Add Agent!'));
            }
            else
            {
                return redirect(action('\Modules\Agent\Http\Controllers\AgentController@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Update Agent!'));
            }
        }
        else
        {
            if($request->isMethod('POST'))
            {
                return redirect()->back()->with('global_message', array('status' => 400, 'message' => 'Failed To Add Agent!'));
            }
            else
            {
                return redirect()->back()->with('global_message', array('status' => 400, 'message' => 'Failed To Update Agent!'));
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy(Request $request)
    {
        $query = MstAgens_m::findOrFail(decrypt($request->input('id')));
        $this->authorize('delete-agent', $query);

        try {
            if($query->delete())
            {
                return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Delete Agent!'));
            }
            
        } catch (\Exception $e) {
            return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Failed Delete Agent, It\'s Has Been Used!'));
        }
    }
}
