<?php

namespace App\Http\Controllers\CMS;

use Illuminate\Http\Request;
use Gdevilbat\SpardaCMS\Modules\Post\Http\Controllers\PostController;
use Gdevilbat\SpardaCMS\Modules\Core\Repositories\Repository;
use App\Models\PostCMS;
use App\Models\RelatedPost;

use Auth;
use Carbon\Carbon;

class Post extends PostController
{
    public function __construct(\Gdevilbat\SpardaCMS\Modules\Post\Repositories\PostRepository $post_repository)
    {
        parent::__construct($post_repository);
        $this->post_m = new PostCMS;
        $this->post_repository = new \Gdevilbat\SpardaCMS\Modules\Post\Repositories\PostRepository(new PostCMS, resolve(\Gdevilbat\SpardaCMS\Modules\Role\Repositories\Contract\AuthenticationRepository::class));
        $this->post_repository->setModule($this->getModule());
        $this->post_transformer = new \App\Transformers\PostTransformer;
    }
    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $this->data['method'] = method_field('POST');
        $this->data['categories'] = $this->term_taxonomy_m->with('term')->where(['taxonomy' => $this->getCategory()])->get();
        $this->data['tags'] = $this->term_taxonomy_m->with('term')->where(['taxonomy' => $this->getTag()])->get();
        if(isset($_GET['code']))
        {
            $this->data['post'] = $this->post_repository->with(['postMeta', 'taxonomies', 'relatedPost'])->find(decrypt($_GET['code']));
            $this->data['method'] = method_field('PUT');
            $this->authorize('update-'.$this->getModule(), $this->data['post']);
        }

        return view($this->getModule().'::admin.'.$this->data['theme_cms']->value.'.content.'.ucfirst($this->getPostType()).'.form', $this->data);
    }

    public function saveRelatedPost(Request $request, $post)
    {
        
        /*======================================
        =            Broadcast Post            =
        ======================================*/
        
            $post = \App\Models\Post::findOrFail($post->getKey());

            if($post->post_status == 'publish')
            {
                if(!empty($post->publish_date) && Carbon::createFromFormat('Y-m-d H:i:s', $post->publish_date)->gt(Carbon::now()))
                {
                    if(Carbon::createFromFormat('Y-m-d H:i:s', $post->publish_date)->gt(Carbon::now()))
                    {
                        /**\App\Jobs\BroadcastPublishedPost::dispatch($post)
                                                        ->delay(Carbon::createFromFormat('Y-m-d H:i:s', $post->publish_date));**/
                    }
                    else
                    {
                        //\App\Jobs\BroadcastPublishedPost::dispatch($post);
                    }

                }
                else
                {
                    //\App\Jobs\BroadcastPublishedPost::dispatch($post);
                }
            }
        
        /*=====  End of Broadcast Post  ======*/
        
        
        if($request->has('related_post'))
        {
            foreach ($request->input('related_post') as $key => $value) 
            {
                $related_post = RelatedPost::where(['post_id' => $post->getKey(), 'related_post_id' => $value])->first();

                if(empty($related_post))
                {
                    $related_post = new RelatedPost;
                    $related_post->post_id = $post->getKey();
                    $related_post->related_post_id = $value;
                }

                $related_post->save();
            }

            $remove_related_relation = RelatedPost::where('post_id', $post->getKey())
                                                    ->whereNotIn('related_post_id', $request->input('related_post'))
                                                    ->pluck('id');

            RelatedPost::whereIn('id', $remove_related_relation)->delete();
        }
        else
        {
            RelatedPost::where('post_id', $post->getKey())->delete();
        }

    }

    public function parsingDataTable($posts)
    {
        /*=========================================
        =            Parsing Datatable            =
        =========================================*/
            
            $data = array();
            $i = 0;
            foreach ($posts as $key_post => $post) 
            {
                if(Auth::user()->can('read-'.$this->getModule(), $post))
                {
                    $data[$i][] = !empty($post->publish_date) ? $post->publish_date : '-';
                    $data[$i][] = $post->post_title;
                    $data[$i][] = $post->author->name;
                    $data[$i][] = $post->editor->name;

                    $categories = $post->taxonomies->where('taxonomy', $this->getCategory());
                    if($categories->count() > 0)
                    {
                        $data[$i][] = '';
                        foreach ($categories as $key => $category) 
                        {
                            $data[$i][count($data[$i]) - 1] .= '<span class="badge badge-danger mx-1">'.$category->term->name.'</span>';
                        }
                    }
                    else
                    {
                        $data[$i][] = '-';
                    }

                    if($post->int_highlight)
                    {
	                    $data[$i][] = 'Yes';
                    	
                    }
                    else
                    {
	                    $data[$i][] = 'No';
                    }

                    if($post->int_popular)
                    {
	                    $data[$i][] = 'Yes';
                    	
                    }
                    else
                    {
	                    $data[$i][] = 'No';
                    }

                    if($post->post_status_bool)
                    {
                        $data[$i][] = '<a href="#" class="btn btn-success p-1">'.$post->post_status.'</a>';;
                    }
                    else
                    {
                        $data[$i][] = '<a href="#" class="btn btn-warning p-1">'.$post->post_status.'</a>';;
                    }

                    if(!empty($post->publish_date) && Carbon::createFromFormat('Y-m-d H:i:s', $post->publish_date)->gt(Carbon::now()))
                        $data[$i][count($data[$i]) - 1] .= '<br/><a href="#" class="btn btn-warning p-1 mt-1">'.'Waiting Publish Time'.'</a>';;

                    $data[$i][] = $post->created_at->toDateTimeString();
                    $data[$i][] = $this->getActionTable($post);
                    $i++;
                }
            }

            return $data;
        
        /*=====  End of Parsing Datatable  ======*/
    }

    public function getColumnOrder()
    {
        return ['publish_date', 'post_title', \Gdevilbat\SpardaCMS\Modules\Core\Entities\User::getTableName().'.name',  \Gdevilbat\SpardaCMS\Modules\Taxonomy\Entities\Terms::getTableName().'.name', 'int_highlight', 'int_popular', 'post_status','created_at'];
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy(Request $request)
    {
        try {
                $query = $this->post_m->findOrFail(decrypt($request->input(\Gdevilbat\SpardaCMS\Modules\Post\Entities\Post::getPrimaryKey())));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 400,'message' => 'Token Invalid, Try Again'));
        }

        $this->authorize('delete-'.$this->getModule(), $query);

        try {
            
            if($query->delete())
            {
                return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 200,'message' => 'Successfully Delete '.ucfirst($this->getPostType()).'!'));
            }
            
        } catch (\Exception $e) {
            return redirect(action('\\'.get_class($this).'@index'))->with('global_message', array('status' => 400,'message' => 'Failed Delete Post, It\'s Has Been Used!'));
        }
    }

    public function getSuggestionRelated(Request $request)
    {
        return PostCMS::where('post_title', 'LIKE', '%'.$request->input('q').'%')
                      ->select([PostCMS::getPrimaryKey(), 'post_title'])
                      ->where(PostCMS::getPrimaryKey(), '!=', $request->input('id'))
                      ->get();
    }
}
