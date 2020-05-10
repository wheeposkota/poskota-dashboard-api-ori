<?php

namespace Modules\Ads\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\CoreController;
use Gdevilbat\SpardaCMS\Modules\Core\Repositories\Repository;

use Modules\Ads\Entities\MstAdCategories as MstAdCategories_m;
use Modules\Ads\Entities\AdTaxonomy as AdTaxonomy_m;
use Modules\Ads\Entities\MstCities as MstCities_m;
use Modules\Ads\Entities\RltAdsCategories as RltAdsCategories_m;

use DB;
use Auth;
use View;
use Validator;

class MstAdCategoriesController extends CoreController
{
    public function __construct(\Modules\Ads\Repositories\MstAdCategoriesRepository $repository)
    {
        parent::__construct();
        $this->mstadcategories_m = new MstAdCategories_m;
        $this->mstadcategories_repository = $repository;
        $this->mstadcategories_repository->setModule('master-ads');
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('ads::admin.'.$this->data['theme_cms']->value.'.content.MstAdCategories.master', $this->data);
    }

    public function serviceMaster(Request $request)
    {
        $column = [MstAdCategories_m::getPrimaryKey(), 'mst_ad_cat_name', 'mst_ad_cat_slug', 'parent_name', 'created_at'];

        $length = !empty($request->input('length')) ? $request->input('length') : 10 ;
        $column = !empty($request->input('order.0.column')) ? $column[$request->input('order.0.column')] : MstAdCategories_m::getPrimaryKey() ;
        $dir = !empty($request->input('order.0.dir')) ? $request->input('order.0.dir') : 'DESC' ;
        $searchValue = $request->input('search')['value'];

        $query = $this->mstadcategories_repository->with('parent')
                                       ->buildQueryByCreatedUser([])
                                       ->orderBy($column, $dir);

        $recordsTotal = $query->count();
        $filtered = $query;

        if($searchValue)
        {
            $filtered->where(DB::raw("CONCAT(".MstAdCategories_m::getTableName().".mst_ad_cat_name,'-',".MstAdCategories_m::getTableName().".mst_ad_cat_slug,'-',ifnull(".MstAdCategories_m::getTableName().".created_at, ''))"), 'like', '%'.$searchValue.'%');
        }

        $filteredTotal = $filtered->count();

        $this->data['length'] = $length;
        $this->data['column'] = $column;
        $this->data['dir'] = $dir;
        $this->data['mst_ad_categories'] = $filtered->offset($request->input('start'))->limit($length)->get();

        /*=========================================
        =            Parsing Datatable            =
        =========================================*/

            $data = array();
            $i = 0;
            foreach ($this->data['mst_ad_categories'] as $key_user => $mst_ad_category)
            {
                $data[$i][] = $mst_ad_category->getKey();
                $data[$i][] = $mst_ad_category->mst_ad_cat_name;
                $data[$i][] = $mst_ad_category->mst_ad_cat_slug;

                if(!empty($mst_ad_category->parent))
                {
                    $data[$i][3] = '<span class="badge badge-danger">'.$mst_ad_category->parent->mst_ad_cat_name.'</span>';
                }
                else
                {
                    $data[$i][3] = '-';
                }

                $data[$i][] = $mst_ad_category->created_a;
                $data[$i][] = $this->getActionTable($mst_ad_category);
                $i++;
            }

        /*=====  End of Parsing Datatable  ======*/

        return ['data' => $data, 'draw' => (integer)$request->input('draw'), 'recordsTotal' => $recordsTotal, 'recordsFiltered' => $filteredTotal];
    }

    private function getActionTable($mst_ad_category)
    {
        $view = View::make('ads::admin.'.$this->data['theme_cms']->value.'.content.MstAdCategories.service_master', [
            'mst_ad_category' => $mst_ad_category
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
            $this->data['mst_ad_category'] = $this->mstadcategories_m::where(MstAdCategories_m::getPrimaryKey(), decrypt($_GET['code']))->first();
            $this->data['parents'] = $this->mstadcategories_m->where(MstAdCategories_m::getPrimaryKey(), '!=', decrypt($_GET['code']))
                                                            ->get();
            $this->data['method'] = method_field('PUT');
            $this->authorize('update-master-ads', $this->data['mst_ad_category']);
        }
        else
        {
            $this->data['parents'] = $this->mstadcategories_m->get();
        }

        return view('ads::admin.'.$this->data['theme_cms']->value.'.content.MstAdCategories.form', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mst_ad_cat_name' => 'required|max:191',
        ]);

        if($request->isMethod('POST'))
        {
            $validator->addRules([
                'mst_ad_cat_slug' => 'max:191|unique:'.$this->mstadcategories_m->getTable().',mst_ad_cat_slug'
            ]);
        }
        else
        {
            $validator->addRules([
                'mst_ad_cat_slug' => 'max:191|unique:'.$this->mstadcategories_m->getTable().',mst_ad_cat_slug,'.decrypt($request->input(MstAdCategories_m::getPrimaryKey())).','.MstAdCategories_m::getPrimaryKey()
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
            $term = new $this->mstadcategories_m;
        }
        else
        {
            $data = $request->except('_token', '_method', MstAdCategories_m::getPrimaryKey());
            $term = $this->mstadcategories_repository->findOrFail(decrypt($request->input(MstAdCategories_m::getPrimaryKey())));
            $this->authorize('update-master-ads', $term);
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
                return redirect(action('\Modules\Ads\Http\Controllers\MstAdCategoriesController@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Add Category!'));
            }
            else
            {
                return redirect(action('\Modules\Ads\Http\Controllers\MstAdCategoriesController@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Update Category!'));
            }
        }
        else
        {
            if($request->isMethod('POST'))
            {
                return redirect()->back()->with('global_message', array('status' => 400, 'message' => 'Failed To Add Category!'));
            }
            else
            {
                return redirect()->back()->with('global_message', array('status' => 400, 'message' => 'Failed To Update Category!'));
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
        $query = $this->mstadcategories_m->findOrFail(decrypt($request->input(MstAdCategories_m::getPrimaryKey())));
        $this->authorize('delete-master-ads', $query);

        try {
            if($query->delete())
            {
                return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Delete Category!'));
            }

        } catch (\Exception $e) {
            return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Failed Delete Category, It\'s Has Been Used!'));
        }
    }

    public function subCategory(Request $request)
    {

        $data = [];

        if($request->has('category_id'))
        {
            $avail_sub = RltAdsCategories_m::where(RltAdsCategories_m::getPrimaryKey(), decrypt($request->input('id_rlt')))
                                            ->whereHas('mstAdsCategory', function($query){
                                                $query->whereIn('mst_ad_cat_slug', array_merge(MstAdCategories_m::SUB_CATEGORY, MstAdCategories_m::SUB_LOCATION));
                                            })
                                            ->get();

            if($avail_sub->count() > 0)
            {
                $category_slug = MstAdCategories_m::where(MstAdCategories_m::getPrimaryKey(), decrypt($request->input('category_id')))->first()->mst_ad_cat_slug;

                if($category_slug == 'rumah')
                {
                    $data['locations'] = MstCities_m::whereHas('province', function($query){
                                                      $query->whereIn('name', MstAdCategories_m::HOME_PROVINCES);
                                                    })
                                                    ->get();
                }
                else
                {
                    $data['taxonomies'] = AdTaxonomy_m::with('term')
                                                ->where(['category_id' => decrypt($request->input('category_id')), 'taxonomy' => MstAdCategories_m::TAXONOMY_BRAND])
                                                ->get();
                }
            }

            return view('ads::admin.'.$this->data['theme_cms']->value.'.content.MstAdCategories.subcategory', $data);
        }

        if($request->has('ad_taxonomy_id'))
        {
            $category_id = AdTaxonomy_m::where(AdTaxonomy_m::getPrimaryKey(), decrypt($request->input('ad_taxonomy_id')))->pluck('category_id');


            $data['ad_taxonomy_id'] = decrypt($request->input('ad_taxonomy_id'));
            $data['taxonomies'] = AdTaxonomy_m::with('term')
                                            ->whereIn('category_id', $category_id)
                                            ->where(['taxonomy' => MstAdCategories_m::TAXONOMY_BRAND])
                                            ->get();

            return view('ads::admin.'.$this->data['theme_cms']->value.'.content.MstAdCategories.editsubcategory', $data);
        }

        if($request->has('mst_city_id'))
        {
            $data['mst_city_id'] = decrypt($request->input('mst_city_id'));
            $data['locations'] = MstCities_m::whereHas('province', function($query){
                                                  $query->whereIn('name', MstAdCategories_m::HOME_PROVINCES);
                                                })
                                                ->get();

            return view('ads::admin.'.$this->data['theme_cms']->value.'.content.MstAdCategories.locationsubcategory', $data);
        }
    }
}
