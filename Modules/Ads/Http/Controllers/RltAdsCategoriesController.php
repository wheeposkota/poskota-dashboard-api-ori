<?php

namespace Modules\Ads\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\CoreController;
use Gdevilbat\SpardaCMS\Modules\Core\Repositories\Repository;

use Modules\Ads\Entities\MstAds as MstAds_m;
use Modules\Ads\Entities\MstAdCategories as MstAdCategories_m;
use Modules\Ads\Entities\RltAdsCategories as RltAdsCategories_m;

use DB;
use Validator;
use Auth;
use View;

class RltAdsCategoriesController extends CoreController
{
    public function __construct(\Modules\Ads\Repositories\RltAdsCategoriesRepository $repository)
    {
        parent::__construct();
        $this->rlt_ads_categories_m = new RltAdsCategories_m;
        $this->rlt_ads_categories_repository = $repository;
        $this->rlt_ads_categories_repository->setModule('master-ads');
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('ads::admin.'.$this->data['theme_cms']->value.'.content.RltAdsCategories.master', $this->data);
    }

    public function serviceMaster(Request $request)
    {
        $column = [RltAdsCategories_m::getPrimaryKey(), 'mst_ad_cat_name', 'mst_ad_name', 'min_line','max_line', 'char_on_line', 'price','created_at'];

        $length = !empty($request->input('length')) ? $request->input('length') : 10 ;
        $column = !empty($request->input('order.0.column')) ? $column[$request->input('order.0.column')] : RltAdsCategories_m::getPrimaryKey() ;
        $dir = !empty($request->input('order.0.dir')) ? $request->input('order.0.dir') : 'DESC' ;
        $searchValue = $request->input('search')['value'];

        $query = $this->rlt_ads_categories_repository->with('mstAd', 'mstAdsCategory')
                                            ->buildQueryByCreatedUser([])
                                            ->orderBy($column, $dir);

        $recordsTotal = $query->count();
        $filtered = $query;

        if($searchValue)
        {
            $filtered->where(DB::raw("CONCAT(mst_ad_name,'-',mst_ad_cat_name,'-',min_line,'-',max_line,'-',char_on_line,'-',price, ifnull(".RltAdsCategories_m::getTableName().".created_at,'-'))"), 'like', '%'.$searchValue.'%');
        }

        $filteredTotal = $filtered->count();

        $this->data['length'] = $length;
        $this->data['column'] = $column;
        $this->data['dir'] = $dir;
        $this->data['rlt_ads_categories'] = $filtered->offset($request->input('start'))->limit($length)->get();

        /*=========================================
        =            Parsing Datatable            =
        =========================================*/
            
            $data = array();
            $i = 0;
            foreach ($this->data['rlt_ads_categories'] as $key_user => $rlt_ad_category) 
            {
                if(Auth::user()->can('read-master-ads', $rlt_ad_category))
                {
                    $data[$i][] = $rlt_ad_category->getKey();
                    $data[$i][] = $rlt_ad_category->mstAdsCategory->mst_ad_cat_name;
                    $data[$i][] = $rlt_ad_category->mstAd->mst_ad_name;
                    $data[$i][] = $rlt_ad_category->min_line;
                    $data[$i][] = $rlt_ad_category->max_line;
                    $data[$i][] = $rlt_ad_category->char_on_line;
                    $data[$i][] = $rlt_ad_category->price;

                    $data[$i][] = !empty($rlt_ad_category->created_at) ? $rlt_ad_category->created_at->format('Y-m-d H:s:i') : '';
                    $data[$i][] = $this->getActionTable($rlt_ad_category);
                    $i++;
                }
            }
        
        /*=====  End of Parsing Datatable  ======*/

        return ['data' => $data, 'draw' => (integer)$request->input('draw'), 'recordsTotal' => $recordsTotal, 'recordsFiltered' => $filteredTotal];
    }

    private function getActionTable($rlt_ad_category)
    {
        $view = View::make('ads::admin.'.$this->data['theme_cms']->value.'.content.RltAdsCategories.service_master', [
            'rlt_ad_category' => $rlt_ad_category
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
        $this->data['mst_ad_categories'] = MstAdCategories_m::get();
        $this->data['mst_ads'] = MstAds_m::whereIn('mst_ad_slug', ['iklan-umum', 'iklan-khusus'])->get();
        if(isset($_GET['code']))
        {
            $this->data['rlt_ad_category'] = $this->rlt_ads_categories_m::where(RltAdsCategories_m::getPrimaryKey(), decrypt($_GET['code']))->first();
            $this->data['method'] = method_field('PUT');
            $this->authorize('update-master-ads', $this->data['rlt_ad_category']);
        }

        return view('ads::admin.'.$this->data['theme_cms']->value.'.content.RltAdsCategories.form', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'ads_id' => 'required',
            'max_line' => 'required|min:1',
            'char_on_line' => 'required|min:1',
            'price' => 'required',
        ]);

        if($request->isMethod('POST'))
        {
            $validator->addRules([
                'category_id' => [
                    function ($attribute, $value, $fail) use ($request) {
                            $rlt_ad_category = RltAdsCategories_m::where(['category_id' => $value, 'ads_id' => $request->input('ads_id')])->first();
                            if (!empty($rlt_ad_category)) {
                                $fail('Ads Category On Current Selected Type is Exist try Another Type or <a class="text-white" href="'.action('\Modules\Ads\Http\Controllers\RltAdsCategoriesController@create').'?code='.encrypt($rlt_ad_category->getKey()).'"> Edit Here</a>');
                            }
                    },
                ]
            ]);
        }
        else
        {
            $validator->addRules([
                'category_id' => [
                    function ($attribute, $value, $fail) use ($request) {
                            $rlt_ad_category = RltAdsCategories_m::where(['category_id' => $value, 'ads_id' => $request->input('ads_id')])
                                                                ->where(RltAdsCategories_m::getPrimaryKey(), '!=', decrypt($request->input(RltAdsCategories_m::getPrimaryKey())))
                                                                ->first();
                            if (!empty($rlt_ad_category)) {
                                $fail('Ads Category On Current Selected Type is Exist try Another Type or <a class="text-white" href="'.action('\Modules\Ads\Http\Controllers\RltAdsCategoriesController@create').'?code='.encrypt($rlt_ad_category->getKey()).'"> Edit Here</a>');
                            }
                    },
                ]
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
            $rlt_ad_category = new $this->rlt_ads_categories_m;
        }
        else
        {
            $data = $request->except('_token', '_method', RltAdsCategories_m::getPrimaryKey());
            $rlt_ad_category = $this->rlt_ads_categories_repository->findOrFail(decrypt($request->input(RltAdsCategories_m::getPrimaryKey())));
            $this->authorize('update-master-ads', $rlt_ad_category);
        }

        foreach ($data as $key => $value) 
        {
            $rlt_ad_category->$key = $value;
        }

        if($request->isMethod('POST'))
        {
            $rlt_ad_category->created_by = Auth::id();
            $rlt_ad_category->modified_by = Auth::id();
        }
        else
        {
            $rlt_ad_category->modified_by = Auth::id();
        }

        if($rlt_ad_category->save())
        {
            if($request->isMethod('POST'))
            {
                return redirect(action('\Modules\Ads\Http\Controllers\RltAdsCategoriesController@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Add Ads Package!'));
            }
            else
            {
                return redirect(action('\Modules\Ads\Http\Controllers\RltAdsCategoriesController@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Update Ads Package!'));
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
        try {
            $query = $this->rlt_ads_categories_m->findOrFail(decrypt($request->input(RltAdsCategories_m::getPrimaryKey())));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 400,'message' => 'Token Mismatch, Try Again !'));
        }

        $this->authorize('delete-master-ads', $query);

        try {
            if($query->delete())
            {
                return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Delete Ads Package!'));
            }
            
        } catch (\Exception $e) {
            return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 400,'message' => 'Failed Delete Ads Package, It\'s Has Been Used!'));
        }
    }
}
