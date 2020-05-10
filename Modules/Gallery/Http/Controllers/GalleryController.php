<?php

namespace Modules\Gallery\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\CoreController;
use Modules\Gallery\Entities\Gallery;
use Gdevilbat\SpardaCMS\Modules\Core\Repositories\Repository;

use Validator;
use Auth;
use View;
use DB;

class GalleryController extends CoreController
{
    protected $submodule;

    public function __construct()
    {
        parent::__construct();
        $this->gallery_m = new Gallery;
        $this->gallery_repository = new Repository(new Gallery, resolve(\Gdevilbat\SpardaCMS\Modules\Role\Repositories\Contract\AuthenticationRepository::class));
        $this->gallery_repository->setModule('gallery');
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('gallery::admin.'.$this->data['theme_cms']->value.'.content.'.ucfirst($this->getGalleryType()).'.master', $this->data);
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
            $this->data['gallery'] = $this->gallery_repository->find(decrypt($_GET['code']));
            $this->data['method'] = method_field('PUT');
            $this->authorize('update-gallery', $this->data['gallery']);
        }

        return view('gallery::admin.'.$this->data['theme_cms']->value.'.content.'.ucfirst($this->getGalleryType()).'.form', $this->data);
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
                'gallery.gallery_slug' => 'unique:'.$this->gallery_m->getTable().',gallery_slug'
            ]);
        }
        else
        {
            $validator->addRules([
                'gallery.gallery_slug' => 'unique:'.$this->gallery_m->getTable().',gallery_slug,'.decrypt($request->input(Gallery::getPrimaryKey())).','.Gallery::getPrimaryKey()
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
            $gallery = new $this->gallery_m;
        }
        else
        {
            $data = $request->except('_token', '_method', Gallery::getPrimaryKey());
            $gallery = $this->gallery_repository->findOrFail(decrypt($request->input(Gallery::getPrimaryKey())));
            $this->authorize('update-gallery', $gallery);
        }

        foreach ($data['gallery'] as $key => $value) 
        {
            $gallery->$key = $value;
        }

        if($request->isMethod('POST'))
        {
            $gallery->created_by = Auth::id();
        }

        $gallery->gallery_status = $request->has('gallery.gallery_status') ? $request->input('gallery.gallery_status') : '';
        $gallery->gallery_type = $this->getGalleryType();
        $gallery->modified_by = Auth::id();

        if($gallery->save())
        {
            if($request->isMethod('POST'))
            {
                return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Add '.ucfirst($this->getGalleryType()).'!'));
            }
            else
            {
                return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Update '.ucfirst($this->getGalleryType()).'!'));
            }
        }
        else
        {
            if($request->isMethod('POST'))
            {
                return redirect()->back()->with('global_message', array('status' => 400, 'message' => 'Failed To Add '.ucfirst($this->getGalleryType()).'!'));
            }
            else
            {
                return redirect()->back()->with('global_message', array('status' => 400, 'message' => 'Failed To Update '.ucfirst($this->getGalleryType()).'!'));
            }
        }
    }

    public function serviceMaster(Request $request)
    {
        $column = [Gallery::getPrimaryKey(), 'gallery_title', 'author', 'gallery_status', 'created_at'];;

        $length = !empty($request->input('length')) ? $request->input('length') : 10 ;
        $column = !empty($request->input('order.0.column')) ? $column[$request->input('order.0.column')] : Gallery::getPrimaryKey() ;
        $dir = !empty($request->input('order.0.dir')) ? $request->input('order.0.dir') : 'DESC' ;
        $searchValue = $request->input('search')['value'];

        $query = $this->getQuerybuilder($column, $dir);

        $recordsTotal = $query->count();
        $filtered = $query;

        if($searchValue)
        {
            $filtered->where(function($query) use ($searchValue){
                        $query->where(DB::raw("CONCAT(gallery_title,'-',gallery_status,'-',".$this->gallery_m::getTableName().".created_at)"), 'like', '%'.$searchValue.'%')
                                ;

                    });
        }

        $filteredTotal = $filtered->count();

        $this->data['length'] = $length;
        $this->data['column'] = $column;
        $this->data['dir'] = $dir;
        $this->data['galleries'] = $filtered->offset($request->input('start'))->limit($length)->get();

        $table =  $this->parsingDataTable($this->data['galleries']);

        return ['data' => $table, 'draw' => (integer)$request->input('draw'), 'recordsTotal' => $recordsTotal, 'recordsFiltered' => $filteredTotal];
    }

    public function parsingDataTable($galleries)
    {
        /*=========================================
        =            Parsing Datatable            =
        =========================================*/
            
            $data = array();
            $i = 0;
            foreach ($galleries as $key_gallery => $gallery) 
            {
                $data[$i][] = $gallery->getKey();
                $data[$i][] = $gallery->gallery_title;
                $data[$i][] = $gallery->author->name;

                if($gallery->gallery_status_bool)
                {
                    $data[$i][] = '<a href="#" class="btn btn-success p-1">'.$gallery->gallery_status.'</a>';;
                }
                else
                {
                    $data[$i][] = '<a href="#" class="btn btn-warning p-1">'.$gallery->gallery_status.'</a>';;
                }

                $data[$i][] = $gallery->created_at->toDateTimeString();
                $data[$i][] = $this->getActionTable($gallery);
                $i++;
            }

            return $data;
        
        /*=====  End of Parsing Datatable  ======*/
    }

    public function getActionTable($gallery)
    {
        $view = View::make('gallery::admin.'.$this->data['theme_cms']->value.'.content.'.ucfirst($this->getGalleryType()).'.service_master', [
            'gallery' => $gallery
        ]);

        $html = $view->render();
       
       return $html;
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy(Request $request)
    {
        $query = $this->gallery_m->findOrFail(decrypt($request->input(Gallery::getPrimaryKey())));
        $this->authorize('delete-gallery', $query);

        try {
            
            if($query->delete())
            {
                return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Delete '.ucfirst($this->getGalleryType()).'!'));
            }
            
        } catch (\Exception $e) {
            return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Failed Delete '.ucfirst($this->getGalleryType()).', It\'s Has Been Used!'));
        }
    }

    public function validatePost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gallery.gallery_title' => 'required|max:191',
            'gallery.gallery_slug' => 'required|max:191',
            'gallery.gallery_content' => 'required',
            'gallery.gallery_source' => 'required'
        ]);

        return $validator;
    }

    protected function setGalleryType($value)
    {
        $this->submodule = $value;
    }

    public function getGalleryType()
    {
        return $this->submodule;
    }
}
