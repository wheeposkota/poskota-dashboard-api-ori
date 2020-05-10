<?php

namespace Modules\Ads\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\CoreController;
use Gdevilbat\SpardaCMS\Modules\Core\Repositories\Repository;

use Modules\Ads\Entities\MstAdsPosition as MstAdsPosition_m;

use Validator;
use DB;
use Auth;
use View;

class MstAdsWebController extends CoreController
{
    public function __construct()
    {
        parent::__construct();
        $this->mst_ads_position_m = new MstAdsPosition_m;
        $this->mst_ads_position_repository = new Repository(new MstAdsPosition_m, resolve(\Gdevilbat\SpardaCMS\Modules\Role\Repositories\Contract\AuthenticationRepository::class));
        $this->mst_ads_position_repository->setModule('master-ads');
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('ads::admin.'.$this->data['theme_cms']->value.'.content.MstAdsWeb.master', $this->data);
    }

    public function serviceMaster(Request $request)
    {
        $column = [MstAdsPosition_m::getPrimaryKey(), 'ads_position', 'price','created_at'];

        $length = !empty($request->input('length')) ? $request->input('length') : 10 ;
        $column = !empty($request->input('order.0.column')) ? $column[$request->input('order.0.column')] : MstAdsPosition_m::getPrimaryKey() ;
        $dir = !empty($request->input('order.0.dir')) ? $request->input('order.0.dir') : 'DESC' ;
        $searchValue = $request->input('search')['value'];

        $query = $this->mst_ads_position_repository->buildQueryByCreatedUser([])->orderBy($column, $dir);

        $recordsTotal = $query->count();
        $filtered = $query;

        if($searchValue)
        {
            $filtered->where(DB::raw("CONCAT(ads_position,'-',price, ".MstAdsPosition_m::getTableName().".created_at)"), 'like', '%'.$searchValue.'%');
        }

        $filteredTotal = $filtered->count();

        $this->data['length'] = $length;
        $this->data['column'] = $column;
        $this->data['dir'] = $dir;
        $this->data['mst_ads_web'] = $filtered->offset($request->input('start'))->limit($length)->get();

        /*=========================================
        =            Parsing Datatable            =
        =========================================*/
            
            $data = array();
            $i = 0;
            foreach ($this->data['mst_ads_web'] as $key_user => $mst_ad_web) 
            {
                $data[$i][] = $mst_ad_web->getKey();
                $data[$i][] = $mst_ad_web->ads_position;
                $data[$i][] = $mst_ad_web->price;

                $data[$i][] = $mst_ad_web->created_at->toDateTimeString();
                $data[$i][] = $this->getActionTable($mst_ad_web);
                $i++;
            }
        
        /*=====  End of Parsing Datatable  ======*/

        return ['data' => $data, 'draw' => (integer)$request->input('draw'), 'recordsTotal' => $recordsTotal, 'recordsFiltered' => $filteredTotal];
    }

    private function getActionTable($mst_ad_web)
    {
        $view = View::make('ads::admin.'.$this->data['theme_cms']->value.'.content.MstAdsWeb.service_master', [
            'mst_ad_web' => $mst_ad_web
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
            $this->data['mst_ad_web'] = $this->mst_ads_position_m::where(MstAdsPosition_m::getPrimaryKey(), decrypt($_GET['code']))->first();
            $this->data['method'] = method_field('PUT');
            $this->authorize('update-master-ads', $this->data['mst_ad_web']);
        }

        return view('ads::admin.'.$this->data['theme_cms']->value.'.content.MstAdsWeb.form', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ads_position' => 'required|max:191',
            'price' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        if($request->isMethod('POST'))
        {
            $data = $request->except('_token', '_method');
            $mst_ad_web = new $this->mst_ads_position_m;
        }
        else
        {
            $data = $request->except('_token', '_method', MstAdsPosition_m::getPrimaryKey());
            $mst_ad_web = $this->mst_ads_position_repository->findOrFail(decrypt($request->input(MstAdsPosition_m::getPrimaryKey())));
            $this->authorize('update-master-ads', $mst_ad_web);
        }

        foreach ($data as $key => $value) 
        {
            $mst_ad_web->$key = $value;
        }

        if($request->isMethod('POST'))
        {
            $mst_ad_web->created_by = Auth::id();
            $mst_ad_web->modified_by = Auth::id();
        }
        else
        {
            $mst_ad_web->modified_by = Auth::id();
        }

        if($mst_ad_web->save())
        {
            if($request->isMethod('POST'))
            {
                return redirect(action('\Modules\Ads\Http\Controllers\MstAdsWebController@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Add Ads Package!'));
            }
            else
            {
                return redirect(action('\Modules\Ads\Http\Controllers\MstAdsWebController@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Update Ads Package!'));
            }
        }
        else
        {
            if($request->isMethod('POST'))
            {
                return redirect()->back()->with('global_message', array('status' => 400, 'message' => 'Failed To Add Ads Package!'));
            }
            else
            {
                return redirect()->back()->with('global_message', array('status' => 400, 'message' => 'Failed To Update Ads Package!'));
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
        $query = $this->mst_ads_position_m->findOrFail(decrypt($request->input(MstAdsPosition_m::getPrimaryKey())));
        $this->authorize('delete-master-ads', $query);

        try {
            if($query->delete())
            {
                return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Delete Ads Package!'));
            }
            
        } catch (\Exception $e) {
            return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Failed Delete Ads Package, It\'s Has Been Used!'));
        }
    }
}
