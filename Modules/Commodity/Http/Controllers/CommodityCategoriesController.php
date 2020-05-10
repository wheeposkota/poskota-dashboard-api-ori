<?php

namespace Modules\Commodity\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\CoreController;

use App\Models\CommodityType as CommodityType_m;
use Gdevilbat\SpardaCMS\Modules\Core\Repositories\Repository;

use Validator;
use DB;
use View;

class CommodityCategoriesController extends CoreController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function __construct()
    {
        parent::__construct();
        $this->commodity_type_m = new CommodityType_m;
        $this->commodity_type_repository = new Repository(new CommodityType_m, resolve(\Gdevilbat\SpardaCMS\Modules\Role\Repositories\Contract\AuthenticationRepository::class));
        $this->commodity_type_repository->setModule('commodity');
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('commodity::admin.'.$this->data['theme_cms']->value.'.content.CommodityCategories.master', $this->data);
    }

    public function serviceMaster(Request $request)
    {
        $column = [CommodityType_m::getPrimaryKey(), 'type', 'created_at'];;

        $length = !empty($request->input('length')) ? $request->input('length') : 10 ;
        $column = !empty($request->input('order.0.column')) ? $column[$request->input('order.0.column')] : CommodityType_m::getPrimaryKey() ;
        $dir = !empty($request->input('order.0.dir')) ? $request->input('order.0.dir') : 'DESC' ;
        $searchValue = $request->input('search')['value'];

        $query = $this->getQuerybuilder($column, $dir);

        $recordsTotal = $query->count();
        $filtered = $query;

        if($searchValue)
        {
            $filtered->where(function($query) use ($searchValue){
                        $query->where(DB::raw("CONCAT(type,'-',".$this->commodity_type_m::getTableName().".created_at)"), 'like', '%'.$searchValue.'%')
                                ;

                    });
        }

        $filteredTotal = $filtered->count();

        $this->data['length'] = $length;
        $this->data['column'] = $column;
        $this->data['dir'] = $dir;
        $this->data['commodity_types'] = $filtered->offset($request->input('start'))->limit($length)->get();

        $table =  $this->parsingDataTable($this->data['commodity_types']);

        return ['data' => $table, 'draw' => (integer)$request->input('draw'), 'recordsTotal' => $recordsTotal, 'recordsFiltered' => $filteredTotal];
    }

    public function parsingDataTable($commodity_types)
    {
        /*=========================================
        =            Parsing Datatable            =
        =========================================*/
            
            $data = array();
            $i = 0;
            foreach ($commodity_types as $key_package => $commodity_type) 
            {
                $data[$i][] = $commodity_type->getKey();
                $data[$i][] = $commodity_type->type;
                $data[$i][] = $commodity_type->created_at->toDateTimeString();
                $data[$i][] = $this->getActionTable($commodity_type);
                $i++;
            }

            return $data;
        
        /*=====  End of Parsing Datatable  ======*/
    }

    public function getActionTable($commodity_type)
    {
        $view = View::make('commodity::admin.'.$this->data['theme_cms']->value.'.content.CommodityCategories.service_master', [
            'commodity_type' => $commodity_type
        ]);

        $html = $view->render();
       
       return $html;
    }

    private function getQuerybuilder($column, $dir)
    {
        $query = $this->commodity_type_repository->buildQueryByCreatedUser([])->orderBy($column, $dir);

        return $query;
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
            $this->data['category_type'] = $this->commodity_type_repository->find(decrypt($_GET['code']));
            $this->data['method'] = method_field('PUT');
            $this->authorize('update-commodity', $this->data['category_type']);
        }

        return view('commodity::admin.'.$this->data['theme_cms']->value.'.content.CommodityCategories.form', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|max:191',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        if($request->isMethod('POST'))
        {
            $data = $request->except('_token', '_method');
            $commodity_type = new $this->commodity_type_m;
        }
        else
        {
            $data = $request->except('_token', '_method', CommodityType_m::getPrimaryKey());
            $commodity_type = $this->commodity_type_repository->findOrFail(decrypt($request->input(CommodityType_m::getPrimaryKey())));
            $this->authorize('update-commodity', $commodity_type);
        }

        foreach ($data as $key => $value) 
        {
            $commodity_type->$key = $value;
        }

        if($commodity_type->save())
        {
            if($request->isMethod('POST'))
            {
                return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Add Commodity Category !'));
            }
            else
            {
                return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Update Commodity Category !'));
            }
        }
        else
        {
            if($request->isMethod('POST'))
            {
                return redirect()->back()->with('global_message', array('status' => 400, 'message' => 'Failed To Add Commodity Category !'));
            }
            else
            {
                return redirect()->back()->with('global_message', array('status' => 400, 'message' => 'Failed To Update Commodity Category !'));
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
        $query = $this->commodity_type_m->findOrFail(decrypt($request->input(CommodityType_m::getPrimaryKey())));
        $this->authorize('delete-commodity', $query);

        try {
            
            if($query->delete())
            {
                return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Delete Commodity Category !'));
            }
            
        } catch (\Exception $e) {
            return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Failed Delete Commodity Category, It\'s Has Been Used!'));
        }
    }
}
