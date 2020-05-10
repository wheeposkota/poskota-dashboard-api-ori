<?php

namespace Modules\EPaper\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

use Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\CoreController;

use Modules\EPaper\Entities\EPaper;
use Gdevilbat\SpardaCMS\Modules\Core\Repositories\Repository;

use Validator;
use Auth;
use View;
use Storage;
use DB;

class EPaperController extends CoreController
{
    public function __construct()
    {
        parent::__construct();
        $this->epaper_m = new EPaper;
        $this->epaper_repository = new Repository(new EPaper, resolve(\Gdevilbat\SpardaCMS\Modules\Role\Repositories\Contract\AuthenticationRepository::class));
        $this->epaper_repository->setModule('epaper');
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('epaper::admin.'.$this->data['theme_cms']->value.'.content.epaper.master', $this->data);
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
            $this->data['epaper'] = $this->epaper_repository->find(decrypt($_GET['code']));
            $this->data['method'] = method_field('PUT');
            $this->authorize('update-epaper', $this->data['epaper']);
        }

        return view('epaper::admin.'.$this->data['theme_cms']->value.'.content.epaper.form', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = $this->validatePost($request);

        if($request->isMethod('POST'))
        {
            $validator->addRules([
                'epaper.epaper_slug' => 'unique:'.$this->epaper_m->getTable().',epaper_slug',
                'epaper.epaper_file' => 'required|mimes:pdf|max:50000000'
            ]);
        }
        else
        {
            $validator->addRules([
                'epaper.epaper_slug' => 'unique:'.$this->epaper_m->getTable().',epaper_slug,'.decrypt($request->input(EPaper::getPrimaryKey())).','.EPaper::getPrimaryKey()
            ]);
        }

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        if($request->isMethod('POST'))
        {
            $data = $request->except('_token', '_method', 'epaper.epaper_file');
            $epaper = new $this->epaper_m;
        }
        else
        {
            $data = $request->except('_token', '_method', 'epaper.epaper_file', EPaper::getPrimaryKey());
            $epaper = $this->epaper_repository->findOrFail(decrypt($request->input(EPaper::getPrimaryKey())));
            $this->authorize('update-epaper', $epaper);
        }

        foreach ($data['epaper'] as $key => $value) 
        {
            $epaper->$key = $value;
        }

        if($request->isMethod('POST'))
        {
            $epaper->created_by = Auth::id();
        }

        $epaper->epaper_status = $request->has('epaper.epaper_status') ? $request->input('epaper.epaper_status') : '';
        $epaper->modified_by = Auth::id();

        if($request->hasFile('epaper.epaper_file'))
        {
            $path = $request->file('epaper.epaper_file')->storeAs('E-Paper', ($request->file('epaper.epaper_file')->getClientOriginalName()));

            if(!empty($epaper->epaper_file) && $path != $epaper->epaper_file)
            {
                Storage::delete($epaper->epaper_file);
            }

            $epaper->epaper_file = $path;
        }

        if($epaper->save())
        {
            if($request->isMethod('POST'))
            {
                return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Add E-Paper !'));
            }
            else
            {
                return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Update E-Paper !'));
            }
        }
        else
        {
            if($request->isMethod('POST'))
            {
                return redirect()->back()->with('global_message', array('status' => 400, 'message' => 'Failed To Add E-Paper !'));
            }
            else
            {
                return redirect()->back()->with('global_message', array('status' => 400, 'message' => 'Failed To Update E-Paper !'));
            }
        }
    }

    public function serviceMaster(Request $request)
    {
        $column = [EPaper::getPrimaryKey(), 'epaper_title', 'author', 'epaper_status', 'epaper_edition','created_at'];;

        $length = !empty($request->input('length')) ? $request->input('length') : 10 ;
        $column = !empty($request->input('order.0.column')) ? $column[$request->input('order.0.column')] : EPaper::getPrimaryKey() ;
        $dir = !empty($request->input('order.0.dir')) ? $request->input('order.0.dir') : 'DESC' ;
        $searchValue = $request->input('search')['value'];

        $query = $this->getQuerybuilder($column, $dir);

        $recordsTotal = $query->count();
        $filtered = $query;

        if($searchValue)
        {
            $filtered->where(function($query) use ($searchValue){
                        $query->where(DB::raw("CONCAT(epaper_title,'-',epaper_status,'-',".$this->epaper_m::getTableName().".created_at)"), 'like', '%'.$searchValue.'%')
                                ;

                    });
        }

        $filteredTotal = $filtered->count();

        $this->data['length'] = $length;
        $this->data['column'] = $column;
        $this->data['dir'] = $dir;
        $this->data['epapers'] = $filtered->offset($request->input('start'))->limit($length)->get();

        $table =  $this->parsingDataTable($this->data['epapers']);

        return ['data' => $table, 'draw' => (integer)$request->input('draw'), 'recordsTotal' => $recordsTotal, 'recordsFiltered' => $filteredTotal];
    }

    public function parsingDataTable($epapers)
    {
        /*=========================================
        =            Parsing Datatable            =
        =========================================*/
            
            $data = array();
            $i = 0;
            foreach ($epapers as $key_epaper => $epaper) 
            {
                $data[$i][] = $epaper->getKey();
                $data[$i][] = $epaper->epaper_title;
                $data[$i][] = $epaper->author->name;

                if($epaper->epaper_status_bool)
                {
                    $data[$i][] = '<a href="#" class="btn btn-success p-1">'.$epaper->epaper_status.'</a>';;
                }
                else
                {
                    $data[$i][] = '<a href="#" class="btn btn-warning p-1">'.$epaper->epaper_status.'</a>';;
                }

                $data[$i][] = $epaper->epaper_edition;
                $data[$i][] = $epaper->created_at->toDateTimeString();
                $data[$i][] = $this->getActionTable($epaper);
                $i++;
            }

            return $data;
        
        /*=====  End of Parsing Datatable  ======*/
    }

    public function getActionTable($epaper)
    {
        $view = View::make('epaper::admin.'.$this->data['theme_cms']->value.'.content.epaper.service_master', [
            'epaper' => $epaper
        ]);

        $html = $view->render();
       
       return $html;
    }

    private function getQuerybuilder($column, $dir)
    {
        $query = $this->epaper_repository->buildQueryByCreatedUser([])->orderBy($column, $dir);

        return $query;
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy(Request $request)
    {
        $query = $this->epaper_m->findOrFail(decrypt($request->input(EPaper::getPrimaryKey())));
        $this->authorize('delete-epaper', $query);

        try {
            
            if($query->delete())
            {
                return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Delete E-Paper !'));
            }
            
        } catch (\Exception $e) {
            return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Failed Delete E-Paper, It\'s Has Been Used!'));
        }
    }

    public function validatePost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'epaper.epaper_title' => 'required|max:191',
            'epaper.epaper_slug' => 'required|max:191',
            'epaper.epaper_file' => [
                function ($attribute, $value, $fail) use ($request) {
                    if (Storage::exists('E-Paper/'.$request->file('epaper.epaper_file')->getClientOriginalName())) {
                        $fail($attribute.' Is Exist. Please Use Another Filename.');
                    }
                },
            ],
            'epaper.epaper_edition' => 'required|date_format:"Y-m-d"'
        ]);

        return $validator;
    }
}
