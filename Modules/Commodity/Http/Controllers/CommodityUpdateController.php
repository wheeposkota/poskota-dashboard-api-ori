<?php

namespace Modules\Commodity\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\CoreController;

use App\Models\CommodityUpdate as CommodityUpdate_m;

use View;
use DB;

class CommodityUpdateController extends CoreController
{
    public function __construct(\Modules\Commodity\Repositories\CommodityUpdateRepository $repository)
    {
        parent::__construct();
        $this->commodity_update_m = new CommodityUpdate_m;
        $this->commodity_update_repository = $repository;
        $this->commodity_update_repository->setModule('commodity');
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('commodity::admin.'.$this->data['theme_cms']->value.'.content.CommodityUpdates.master', $this->data);
    }

    public function serviceMaster(Request $request)
    {
        $column = [CommodityUpdate_m::getPrimaryKey(), 'type', 'price', 'author','created_at'];;

        $length = !empty($request->input('length')) ? $request->input('length') : 10 ;
        $column = !empty($request->input('order.0.column')) ? $column[$request->input('order.0.column')] : CommodityUpdate_m::getPrimaryKey() ;
        $dir = !empty($request->input('order.0.dir')) ? $request->input('order.0.dir') : 'DESC' ;
        $searchValue = $request->input('search')['value'];

        $query = $this->commodity_update_repository->buildQueryByCreatedUser([])->orderBy($column, $dir);

        $recordsTotal = $query->count();
        $filtered = $query;

        if($searchValue)
        {
            $filtered->where(function($query) use ($searchValue){
                        $query->where(DB::raw("CONCAT(category.type,'-',content,'-',price,'-',author.name,'-',ifnull(".$this->commodity_update_m::getTableName().".created_at,''))"), 'like', '%'.$searchValue.'%')
                                ;

                    });
        }

        $filteredTotal = $filtered->count();

        $this->data['length'] = $length;
        $this->data['column'] = $column;
        $this->data['dir'] = $dir;
        $this->data['commodity_updates'] = $filtered->offset($request->input('start'))->limit($length)->get();

        $table =  $this->parsingDataTable($this->data['commodity_updates']);

        return ['data' => $table, 'draw' => (integer)$request->input('draw'), 'recordsTotal' => $recordsTotal, 'recordsFiltered' => $filteredTotal];
    }

    public function parsingDataTable($commodity_updates)
    {
        /*=========================================
        =            Parsing Datatable            =
        =========================================*/
            
            $data = array();
            $i = 0;
            foreach ($commodity_updates as $key_commodity_update => $commodity_update) 
            {
                $data[$i][] = sprintf('<a href="javascript:void(0)" onclick="CommodityUpdate.getData(%s)">%s</a>', $commodity_update->getKey(), $commodity_update->getKey());
                $data[$i][] = $commodity_update->type;
                $data[$i][] = sprintf('Rp. %s', number_format($commodity_update->price));
                $data[$i][] = $commodity_update->author;

                if(!empty($commodity_update->created_at))
                {
                    $data[$i][] = $commodity_update->created_at->toDateTimeString();
                }
                else
                {
                    $data[$i][] = '-';
                }

                $data[$i][] = $this->getActionTable($commodity_update);
                $i++;
            }

            return $data;
        
        /*=====  End of Parsing Datatable  ======*/
    }

    public function getActionTable($commodity_update)
    {
        $view = View::make('commodity::admin.'.$this->data['theme_cms']->value.'.content.CommodityUpdates.service_master', [
            'commodity_update' => $commodity_update
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
        $query = $this->commodity_update_m->findOrFail(decrypt($request->input(CommodityUpdate_m::getPrimaryKey())));
        $this->authorize('delete-commodity', $query);

        try {
            if($query->delete())
            {
                return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Delete Commodity!'));
            }
            
        } catch (\Exception $e) {
            return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Failed Delete Commodity, It\'s Has Been Used!'));
        }
    }

    public function getData(Request $request)
    {
        $data = $this->commodity_update_repository
              ->buildQueryByCreatedUser([CommodityUpdate_m::getTableName().'.'.CommodityUpdate_m::getPrimaryKey() => $request->input('id')])
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
