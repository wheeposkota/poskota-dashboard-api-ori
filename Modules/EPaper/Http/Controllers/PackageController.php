<?php

namespace Modules\EPaper\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\CoreController;

use Modules\EPaper\Entities\Package;
use Gdevilbat\SpardaCMS\Modules\Core\Repositories\Repository;

use Validator;
use DB;
use Auth;
use View;

class PackageController extends CoreController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function __construct()
    {
        parent::__construct();
        $this->package_m = new Package;
        $this->package_repository = new Repository(new Package, resolve(\Gdevilbat\SpardaCMS\Modules\Role\Repositories\Contract\AuthenticationRepository::class));
        $this->package_repository->setModule('epaper');
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('epaper::admin.'.$this->data['theme_cms']->value.'.content.Package.master', $this->data);
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
            $this->data['package'] = $this->package_repository->find(decrypt($_GET['code']));
            $this->data['method'] = method_field('PUT');
            $this->authorize('update-epaper', $this->data['package']);
        }

        return view('epaper::admin.'.$this->data['theme_cms']->value.'.content.Package.form', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package.package_name' => 'required|max:191',
            'package.package_period' => 'required|min:1',
            'package.package_price' => 'required|max:11',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        if($request->isMethod('POST'))
        {
            $data = $request->except('_token', '_method');
            $package = new $this->package_m;
        }
        else
        {
            $data = $request->except('_token', '_method', Package::getPrimaryKey());
            $package = $this->package_repository->findOrFail(decrypt($request->input(Package::getPrimaryKey())));
            $this->authorize('update-epaper', $package);
        }

        foreach ($data['package'] as $key => $value) 
        {
            $package->$key = $value;
        }

        if($request->isMethod('POST'))
        {
            $package->created_by = Auth::id();
        }

        $package->modified_by = Auth::id();


        if($package->save())
        {
            if($request->isMethod('POST'))
            {
                return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Add Package !'));
            }
            else
            {
                return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Update Package !'));
            }
        }
        else
        {
            if($request->isMethod('POST'))
            {
                return redirect()->back()->with('global_message', array('status' => 400, 'message' => 'Failed To Add Package !'));
            }
            else
            {
                return redirect()->back()->with('global_message', array('status' => 400, 'message' => 'Failed To Update Package !'));
            }
        }
    }

    public function serviceMaster(Request $request)
    {
        $column = [Package::getPrimaryKey(), 'package_name', 'author', 'package_period', 'package_price','created_at'];;

        $length = !empty($request->input('length')) ? $request->input('length') : 10 ;
        $column = !empty($request->input('order.0.column')) ? $column[$request->input('order.0.column')] : Package::getPrimaryKey() ;
        $dir = !empty($request->input('order.0.dir')) ? $request->input('order.0.dir') : 'DESC' ;
        $searchValue = $request->input('search')['value'];

        $query = $this->getQuerybuilder($column, $dir);

        $recordsTotal = $query->count();
        $filtered = $query;

        if($searchValue)
        {
            $filtered->where(function($query) use ($searchValue){
                        $query->where(DB::raw("CONCAT(package_name,'-',package_period,'-',package_price,'-',".$this->package_m::getTableName().".created_at)"), 'like', '%'.$searchValue.'%')
                                ;

                    });
        }

        $filteredTotal = $filtered->count();

        $this->data['length'] = $length;
        $this->data['column'] = $column;
        $this->data['dir'] = $dir;
        $this->data['packages'] = $filtered->offset($request->input('start'))->limit($length)->get();

        $table =  $this->parsingDataTable($this->data['packages']);

        return ['data' => $table, 'draw' => (integer)$request->input('draw'), 'recordsTotal' => $recordsTotal, 'recordsFiltered' => $filteredTotal];
    }

    public function parsingDataTable($packages)
    {
        /*=========================================
        =            Parsing Datatable            =
        =========================================*/
            
            $data = array();
            $i = 0;
            foreach ($packages as $key_package => $package) 
            {
                $data[$i][] = $package->getKey();
                $data[$i][] = $package->package_name;
                $data[$i][] = $package->author->name;

                $data[$i][] = $package->package_period.' Days';
                $data[$i][] = 'Rp. '.number_format($package->package_price);
                $data[$i][] = $package->created_at->toDateTimeString();
                $data[$i][] = $this->getActionTable($package);
                $i++;
            }

            return $data;
        
        /*=====  End of Parsing Datatable  ======*/
    }

    public function getActionTable($package)
    {
        $view = View::make('epaper::admin.'.$this->data['theme_cms']->value.'.content.Package.service_master', [
            'package' => $package
        ]);

        $html = $view->render();
       
       return $html;
    }

    private function getQuerybuilder($column, $dir)
    {
        $query = $this->package_repository->buildQueryByCreatedUser([])->orderBy($column, $dir);

        return $query;
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy(Request $request)
    {
        $query = $this->package_m->findOrFail(decrypt($request->input(Package::getPrimaryKey())));
        $this->authorize('delete-epaper', $query);

        try {
            
            if($query->delete())
            {
                return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Delete Package !'));
            }
            
        } catch (\Exception $e) {
            return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Failed Delete Package, It\'s Has Been Used!'));
        }
    }
}
