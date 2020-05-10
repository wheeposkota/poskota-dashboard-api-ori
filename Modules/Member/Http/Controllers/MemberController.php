<?php

namespace Modules\Member\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\CoreController;
use Gdevilbat\SpardaCMS\Modules\Core\Repositories\Repository;

use App\Models\Member as Member_m;

use Auth;
use View;
use DB; 
use Validator;

class MemberController extends CoreController
{
	public function __construct()
    {
        parent::__construct();
        $this->member_m = new Member_m;
        $this->member_repository = new Repository(new Member_m, resolve(\Gdevilbat\SpardaCMS\Modules\Role\Repositories\Contract\AuthenticationRepository::class));
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('member::admin.'.$this->data['theme_cms']->value.'.content.master', $this->data);
    }

    public function serviceMaster(Request $request)
    {
        $column = [Member_m::getPrimaryKey(), 'name', 'email', 'type','created_at'];;

        $length = !empty($request->input('length')) ? $request->input('length') : 10 ;
        $column = !empty($request->input('order.0.column')) ? $column[$request->input('order.0.column')] : Member_m::getPrimaryKey() ;
        $dir = !empty($request->input('order.0.dir')) ? $request->input('order.0.dir') : 'DESC' ;
        $searchValue = $request->input('search')['value'];

        $query = $this->member_m->orderBy($column, $dir);

        $recordsTotal = $query->count();
        $filtered = $query;

        if($searchValue)
        {
            $filtered->where(function($query) use ($searchValue){
                        $query->where(DB::raw("CONCAT(name,'-',email,'-',type,'-',ifnull(".$this->member_m::getTableName().".created_at,''))"), 'like', '%'.$searchValue.'%')
                                ;

                    });
        }

        $filteredTotal = $filtered->count();

        $this->data['length'] = $length;
        $this->data['column'] = $column;
        $this->data['dir'] = $dir;
        $this->data['members'] = $filtered->offset($request->input('start'))->limit($length)->get();

        $table =  $this->parsingDataTable($this->data['members']);

        return ['data' => $table, 'draw' => (integer)$request->input('draw'), 'recordsTotal' => $recordsTotal, 'recordsFiltered' => $filteredTotal];
    }

    public function parsingDataTable($members)
    {
        /*=========================================
        =            Parsing Datatable            =
        =========================================*/
            
            $data = array();
            $i = 0;
            foreach ($members as $key_member => $member) 
            {
                if(Auth::user()->can('read-member', $member))
                {
                    $data[$i][] = sprintf('<a class=".member" href="javascript:void(0)" onclick="Member.getData(%s)">%s</a>', $member->getKey(), $member->getKey());
                    $data[$i][] = $member->name;
                    $data[$i][] = $member->email;
                    $data[$i][] = $member->type;

                    if(!empty($member->created_at))
                    {
	                    $data[$i][] = $member->created_at->toDateTimeString();
                    }
                    else
                    {
	                    $data[$i][] = '-';
                    }

                    $data[$i][] = $this->getActionTable($member);
                    $i++;
                }
            }

            return $data;
        
        /*=====  End of Parsing Datatable  ======*/
    }

    public function getActionTable($member)
    {
        $view = View::make('member::admin.'.$this->data['theme_cms']->value.'.content.service_master', [
            'member' => $member
        ]);

        $html = $view->render();
       
       return $html;
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $this->data['method'] = method_field('POST');
        if(isset($_GET['code']))
        {
            $this->data['member'] = $this->member_repository->find(decrypt($_GET['code']));
            $this->data['method'] = method_field('PUT');
            $this->authorize('update-member', $this->data['member']);
        }

        return view('member::admin.'.$this->data['theme_cms']->value.'.content.form', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'email' => 'required|email|max:191',
            'password' => 'confirmed'
        ]);

        $validator->sometimes('password', 'min:8', function ($input) {
            return strlen($input->password) >= 1;
        });

        if($request->isMethod('POST'))
        {
            $validator->addRules([
                'email' => 'unique:'.$this->member_m->getTable().',email',
                'password' => 'required'
            ]);
        }
        else
        {
            $validator->addRules([
                'email' => 'unique:'.$this->member_m->getTable().',email,'.decrypt($request->input('id')).',id'
            ]);
        }

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        if($request->isMethod('POST'))
        {
            $data = $request->except('_token', '_method', 'password', 'password_confirmation');
            $member = new $this->member_m;
        }
        else
        {
            $data = $request->except('_token', '_method', 'password', 'password_confirmation', 'id');
            $member = $this->member_repository->findOrFail(decrypt($request->input('id')));
            $this->authorize('update-member', $member);
        }

        foreach ($data as $key => $value) 
        {
            $member->$key = $value;
        }

        if(empty($member->metadata))
        	$member->metadata = json_encode([]);

        if(empty($member->address))
        	$member->address = '';

        if(!empty($request->input('password')))
        {
        	$member->password = bcrypt($request->input('password'));
        }

        $member->type = 'verified';

        if($member->save())
        {
            if($request->isMethod('POST'))
            {
                return redirect(action('\Modules\Member\Http\Controllers\MemberController@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Add Member!'));
            }
            else
            {
                return redirect(action('\Modules\Member\Http\Controllers\MemberController@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Update Member!'));
            }
        }
        else
        {
            if($request->isMethod('POST'))
            {
                return redirect()->back()->with('global_message', array('status' => 400, 'message' => 'Failed To Add Member!'));
            }
            else
            {
                return redirect()->back()->with('global_message', array('status' => 400, 'message' => 'Failed To Update Member!'));
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
        $query = $this->member_m->findOrFail(decrypt($request->input('id')));
        $this->authorize('delete-member', $query);

        try {
            if($query->delete())
            {
                return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Delete Member!'));
            }
            
        } catch (\Exception $e) {
            return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Failed Delete Member, It\'s Has Been Used!'));
        }
    }

    public function getData(Request $request)
    {
        $data = $this->member_m
              ->where(Member_m::getPrimaryKey(), $request->input('id'))
              ->first();

        if(!empty($data))
        {
            $response = [
                'status' => 200,
                'data' => $data
            ];
        }
        else
        {
            $response = [
                'status' => 200,
                'data' => []
            ];
        }

        return $response;
    }
}
