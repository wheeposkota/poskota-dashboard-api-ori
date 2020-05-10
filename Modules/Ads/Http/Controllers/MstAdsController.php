<?php

namespace Modules\Ads\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\CoreController;
use Gdevilbat\SpardaCMS\Modules\Core\Repositories\Repository;

use Modules\Ads\Entities\MstAds as MstAds_m;

use DB;
use Auth;
use View;
use Validator;

class MstAdsController extends CoreController
{
    public function __construct()
    {
        parent::__construct();
        $this->mstads_m = new MstAds_m;
        $this->mstads_repository = new Repository(new MstAds_m, resolve(\Gdevilbat\SpardaCMS\Modules\Role\Repositories\Contract\AuthenticationRepository::class));
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('ads::admin.'.$this->data['theme_cms']->value.'.content.MstAds.master', $this->data);
    }

    public function serviceMaster(Request $request)
    {
        $column = [MstAds_m::getPrimaryKey(), 'mst_ad_name', 'mst_ad_slug', 'created_at'];

        $length = !empty($request->input('length')) ? $request->input('length') : 10 ;
        $column = !empty($request->input('order.0.column')) ? $column[$request->input('order.0.column')] : MstAds_m::getPrimaryKey() ;
        $dir = !empty($request->input('order.0.dir')) ? $request->input('order.0.dir') : 'DESC' ;
        $searchValue = $request->input('search')['value'];

        $query = $this->mstads_m->orderBy($column, $dir);

        $recordsTotal = $query->count();
        $filtered = $query;

        if($searchValue)
        {
            $filtered->where(DB::raw("CONCAT(mst_ad_name,'-',mst_ad_slug,'-',created_at)"), 'like', '%'.$searchValue.'%');
        }

        $filteredTotal = $filtered->count();

        $this->data['length'] = $length;
        $this->data['column'] = $column;
        $this->data['dir'] = $dir;
        $this->data['mst_ads'] = $filtered->offset($request->input('start'))->limit($length)->get();

        /*=========================================
        =            Parsing Datatable            =
        =========================================*/
            
            $data = array();
            $i = 0;
            foreach ($this->data['mst_ads'] as $key_user => $mst_ad) 
            {
                if(Auth::user()->can('read-terms-ads', $mst_ad))
                {
                    $data[$i][] = $mst_ad->getKey();
                    $data[$i][] = $mst_ad->mst_ad_name;
                    $data[$i][] = $mst_ad->mst_ad_slug;

                    $data[$i][] = $mst_ad->created_at->toDateTimeString();
                    $data[$i][] = $this->getActionTable($mst_ad);
                    $i++;
                }
            }
        
        /*=====  End of Parsing Datatable  ======*/

        return ['data' => $data, 'draw' => (integer)$request->input('draw'), 'recordsTotal' => $recordsTotal, 'recordsFiltered' => $filteredTotal];
    }

    private function getActionTable($mst_ad)
    {
        $view = View::make('ads::admin.'.$this->data['theme_cms']->value.'.content.MstAds.service_master', [
            'mst_ad' => $mst_ad
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
            $this->data['mst_ad'] = $this->mstads_m::where(MstAds_m::getPrimaryKey(), decrypt($_GET['code']))->first();
            $this->data['method'] = method_field('PUT');
            $this->authorize('update-terms-ads', $this->data['mst_ad']);
        }

        return view('ads::admin.'.$this->data['theme_cms']->value.'.content.MstAds.form', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mst_ad_name' => 'required|max:191',
        ]);

        if($request->isMethod('POST'))
        {
            $validator->addRules([
                'mst_ad_slug' => 'max:191|unique:'.$this->mstads_m->getTable().',mst_ad_slug'
            ]);
        }
        else
        {
            $validator->addRules([
                'mst_ad_slug' => 'max:191|unique:'.$this->mstads_m->getTable().',mst_ad_slug,'.decrypt($request->input(MstAds_m::getPrimaryKey())).','.MstAds_m::getPrimaryKey()
            ]);
        }

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        if($request->isMethod('POST'))
        {
            $data = $request->except('_token', '_method');
            $term = new $this->mstads_m;
        }
        else
        {
            $data = $request->except('_token', '_method', MstAds_m::getPrimaryKey());
            $term = $this->mstads_repository->findOrFail(decrypt($request->input(MstAds_m::getPrimaryKey())));
            $this->authorize('update-terms-ads', $term);
        }

        foreach ($data as $key => $value) 
        {
            $term->$key = $value;
        }

        if($request->isMethod('POST'))
        {
            $term->created_by = Auth::id();
            $term->modified_by = Auth::id();
        }
        else
        {
            $term->modified_by = Auth::id();
        }

        if($term->save())
        {
            if($request->isMethod('POST'))
            {
                return redirect(action('\Modules\Ads\Http\Controllers\MstAdsController@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Add Term!'));
            }
            else
            {
                return redirect(action('\Modules\Ads\Http\Controllers\MstAdsController@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Update Term!'));
            }
        }
        else
        {
            if($request->isMethod('POST'))
            {
                return redirect()->back()->with('global_message', array('status' => 400, 'message' => 'Failed To Add Term!'));
            }
            else
            {
                return redirect()->back()->with('global_message', array('status' => 400, 'message' => 'Failed To Update Term!'));
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
        $query = $this->mstads_m->findOrFail(decrypt($request->input(MstAds_m::getPrimaryKey())));
        $this->authorize('delete-terms-ads', $query);

        try {
            if($query->delete())
            {
                return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Delete Term!'));
            }
            
        } catch (\Exception $e) {
            return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Failed Delete Term, It\'s Has Been Used!'));
        }
    }
}
