<?php

namespace App\Console\Commands;

use Exception;
use App\Models\Post;
use App\Models\Media;
use App\Models\PostMeta;
use App\Models\PostRelationship;
use App\Models\PostTerm;
use App\Models\PostTermTaxo;
use App\Models\PostWp;
use App\Models\PostWpMeta;
use App\Models\PostWpTaxo;
use App\Models\PostWpTerm;
use App\Models\PostWpTermTaxo;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MappingPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'core:mapping {--media} {--post} {--term} {--truncate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mapping post v1 to v2';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $is_truncate = $this->option('truncate');
        if ($is_truncate) {
            $this->_truncate();
        }

        $is_term = $this->option('term');
        if ($is_term) {
             $this->_termMigrate();
             $this->_termTaxoMigrate();
        }

        $is_post = $this->option('post');
        if ($is_post) {
            $this->_postMigrate();
        }

        $is_media = $this->option('media');
        if ($is_media) {
            $this->_mediaMigrate();
        }
    }

    private function _truncate()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        PostMeta::truncate();
        PostRelationship::truncate();
        Post::truncate();
        PostTermTaxo::truncate();
        PostTerm::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function _firstUser()
    {
        $user = User::first();
        if (! $user)
            $user = User::create([
                'email' => 'admin@default.app',
                'name' => 'Super Admin',
                'password' => bcrypt(1),
            ]);
        return $user->getKey();
    }

    private function _termMigrate()
    {
//        DB::statement('SET FOREIGN_KEY_CHECKS=0');
//        PostTerm::truncate();
//        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $i = 0;
        $wp_term = PostWpTerm::get();
        foreach ($wp_term as $wpt) {
            $term = PostTerm::where('id_terms', $wpt->term_id)->orWhere('slug', $wpt->slug)->first();
            if (! $term) {
                $term_group = $wpt->term_group;
                if ($term_group == 0)
                    $term_group = null;

                try {
                    $term = PostTerm::create([
                        'name' => $wpt->name,
                        'slug' => $wpt->slug,
                        'term_group' => $term_group,
                        'created_by' => $this->_firstUser(),
                        'modified_by' => $this->_firstUser(),
                    ]);
                    $term->id_terms = $wpt->term_id;
                    $term->save();

                    $i++;
                } catch (Exception $e) {
                    $this->info($e->getMessage());
                }
            }
        }

        $this->info(sprintf('Term from v1: %s, inserted: %s', $wp_term->count(), $i));
    }

    private function _termTaxoMigrate()
    {
//        DB::statement('SET FOREIGN_KEY_CHECKS=0');
//        PostTermTaxo::truncate();
//        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $i = 0;
        $wp_term_taxo = PostWpTermTaxo::query()
            ->whereIn('taxonomy', ['category', 'post_tag'])
            ->get();
        foreach ($wp_term_taxo as $wptt) {
            $term_taxo = PostTermTaxo::find($wptt->term_taxonomy_id);
            if (! $term_taxo) {
		        $term_id = $wptt->term_id;
                if ($term_id == 0 || $term_id == "")
                    $term_id = null;
                if ($term_id != null) {
                    $check_term = PostTerm::find($term_id);
                    if (! $check_term)
                        $term_id = null;
                }

                $parent_id = $wptt->parent;
                if ($parent_id == 0)
                    $parent_id = null;
                if ($parent_id != null) {
                    $check_term = PostTerm::find($parent_id);
                    if (! $check_term)
                        $parent_id = null;
                }

                $taxonomy = $wptt->taxonomy;
                if ($taxonomy == 'post_tag')
                    $taxonomy = PostTermTaxo::TYPE_TAG;

                if ($term_id == null && $wptt->description == "")
                    break;

                try {
                    $term_taxo = PostTermTaxo::create([
                        'term_id' => $term_id,
                        'description' => $wptt->description,
                        'taxonomy' => $taxonomy,
                        'parent_id' => $parent_id,
                        'created_by' => $this->_firstUser(),
                        'modified_by' => $this->_firstUser(),
                    ]);
                    $term_taxo->id_term_taxonomy = $wptt->term_taxonomy_id;
                    $term_taxo->save();

                    $i++;
                } catch (Exception $e) {
                    $this->info($e->getMessage());
                }
            }
        }

        $this->info(sprintf('Termtaxo from v1: %s, inserted: %s', $wp_term_taxo->count(), $i));
    }

    private function _postMigrate()
    {
//        DB::statement('SET FOREIGN_KEY_CHECKS=0');
//        Post::truncate();
//        PostMeta::truncate();
//        PostRelationship::truncate();
//        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $c = 0;
        $d = 0;
        $e = 0;
        $user_id = $this->_firstUser();
        PostWp::where('post_status', 'publish')
            ->where('post_type', 'post')
            ->orderBy('post_date', 'desc')
            ->chunk(100, function($posts) use ($user_id, &$c, &$d, &$e) {

                $c += $posts->count();
                $this->info('Post current: '.$c);

                $posts->each(function($p) use ($user_id, &$d, &$e) {
                    $post_id = $p->ID; //use old id

                    //check for existing
                    $post = Post::find($post_id);
                    if (! $post) {
                        //remove <p> tag
                        foreach(["<p>", "</p>", "</ p>"] as $tag)
                            $content = str_replace($tag, '', $p->post_content);

                        $post = new Post();
                        $post->id_posts = $post_id; //use old id
                        $post->post_title = $p->post_title;
                        $post->post_slug = $p->post_name;
                        $post->post_content = $content;
                        $post->post_excerpt = $p->post_excerpt;
                        $post->post_status = $p->post_status;
                        $post->comment_status = $p->comment_status;
                        $post->publish_date = $p->post_date;
                        $post->created_by = $user_id;
                        $post->modified_by = $user_id;
                        $post->created_at = $p->post_date;
                        $post->updated_at = $p->post_modified;

                        try {
                            $post->save();
                            $e++;
                        } catch (Exception $e) {
                            $this->info($e->getMessage());
                            return false;
                        }
                    }

                    //handle category, tag
                    $taxonomy = PostWpTaxo::where('object_id', $post_id)->get();
                    foreach ($taxonomy as $t) {
                        $term_taxo = PostWpTermTaxo::where('term_taxonomy_id', $t->term_taxonomy_id)->first();
                        if (! $term_taxo) return;
                        $term = PostWpTerm::where('term_id', $term_taxo->term_id)->first();
                        if (! $term) return;

                        $new_term = PostTerm::where('slug', $term->slug)->first();
                        if (! $new_term) return;
                        $new_term_taxo = PostTermTaxo::where('term_id', $new_term->getKey())->first();
                        if (! $new_term_taxo) return;

                        $tax_exist = PostRelationship::query()
                            ->where('object_id', $post_id)
                            ->where('term_taxonomy_id', $new_term_taxo->getKey())
                            ->count();
                        if (! $tax_exist) {
                            try {
                                PostRelationship::create([
                                    'object_id' => $post_id,
                                    'term_taxonomy_id' => $new_term_taxo->getKey(),
                                    'term_order' => $t->term_order,
                                ]);
                            } catch (Exception $e) {
                                dd($taxonomy->toArray(), $e->getMessage(), $tax_exist);

                                $this->info($e->getMessage());
                            }
                        }
                    }

                    //remove meta if exist
                    PostMeta::where('post_id', $post_id)->delete();

                    //handle view count, cover, meta, etc
                    $meta = PostWpMeta::where('post_id', $post_id)->get();
                    foreach($meta as $m) {
                        //case cover
                        if ($m->meta_key == '_thumbnail_id') {
                            $meta_cover = PostWpMeta::where('post_id', $m->meta_value)->where('meta_key', '_wp_attached_file')->first();
                            if ($meta_cover) {
                                //find caption
                                $caption = PostWp::where('post_parent', $post_id)->where('post_type', 'attachment')->where('post_status', 'inherit')->first()->post_excerpt ?? "";

                                $img_value = [];
                                $img_value[PostMeta::IMAGE_SIZE_ORIGINAL] = $meta_cover->meta_value;
                                $img_value[PostMeta::IMAGE_CAPTION] = $caption;

                                //find pic other version
                                $cover_versions = PostWpMeta::where('post_id', $m->meta_value)->where('meta_key', '_wp_attachment_metadata')->first();
                                if ($cover_versions) {
                                    $vals = unserialize($cover_versions->meta_value);
                                    $img_value[PostMeta::IMAGE_SIZE_MEDIUM] = $vals['sizes']['featured-image'] ?? null;
                                    $img_value[PostMeta::IMAGE_SIZE_THUMBNAIL] = $vals['sizes']['post-image'] ?? null;
                                    $img_value[PostMeta::IMAGE_SIZE_SMALL] = $vals['sizes']['slideshow-thumbnail'] ?? null;
                                    if (isset($vals['image_meta']))
                                        $img_value[PostMeta::IMAGE_META] = $vals['image_meta'];
                                }

                                PostMeta::create([
                                    'post_id' => $post_id,
                                    'meta_key' => PostMeta::KEY_COVER_IMAGE,
                                    'meta_value' => $img_value,
                                ]);
                                $d++;
                            }
                        }

                        //case view count
                        if ($m->meta_key == 'post_views_count') {
                            PostMeta::create([
                                'post_id' => $post_id,
                                'meta_key' => (int) PostMeta::KEY_VIEW_COUNT,
                                'meta_value' => $m->meta_value,
                            ]);
                            $d++;
                        }
                    }
                });
            });

        $this->info(sprintf('Post fetched: %s, inserted: %s, meta: %s', $c, $e, $d));
    }

    private function _mediaMigrate()
    {
//        DB::statement('SET FOREIGN_KEY_CHECKS=0');
//        Media::truncate();
//        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $c = 0;
        $d = 0;
        $e = 0;
        $f = 0;
        $user_id = $this->_firstUser();
        PostWp::where('post_status', 'publish')
            ->whereIn('post_type', ['gallery', 'video'])
            ->orderBy('post_date', 'desc')
            ->chunk(100, function($media) use ($user_id, &$c, &$d, &$e, &$f) {

                $e += count($media);
                $this->info('Media current: '.$e);

                $media->each(function($p) use ($user_id, &$c, &$d, &$f) {
                    $media_id = $p->ID;
                    $sources = [];

                    if ($p->post_type == 'gallery') {
                        $p->post_type = Media::TYPE_PHOTO;
                        $sources = PostWp::query()
                            ->where('post_type', 'attachment')
                            ->where('post_parent', $media_id)
                            ->orderBy('post_title', 'asc')
                            ->get()
                            ->map(function($item) {
                                $p = $item->guid;
                                $domains = [
                                    'http://poskotanews.com',
                                    'https://poskotanews.com',
                                    'http://master.poskotanews.com',
                                ];
                                foreach ($domains as $d) {
                                    $p = str_replace(sprintf('%s/cms/wp-content/uploads/', $d), '', $p);
                                }
                                return [
                                    'photo' => $p,
                                    'caption' => $item->post_excerpt,
                                ];
                            })->toArray();
                        $c += count($sources);
                    }
                    if ($p->post_type == 'video') {
                        $p->post_type = Media::TYPE_VIDEO;
                        $sources = PostWpMeta::query()
                            ->where('post_id', $media_id)
                            ->where('meta_key', '_mcf_youtube')
                            ->get()
                            ->map(function($item) {
                                return [
                                    'video' => $item['meta_value']
                                ];
                            })->toArray();
                        $d += count($sources);
                    }

                    //check for existing
                    $is_exist = Media::find($media_id);
                    if ($is_exist) {
                        return false;
                    }

                    $media = new Media();

                    //remove <p> tag
                    foreach(["<p>", "</p>", "</ p>"] as $tag) {
                            $content = str_replace($tag, '', $p->post_content);
                    }

                    $media->id_gallery = $media_id; //use old id
                    $media->gallery_title = $p->post_title;
                    $media->gallery_slug = $p->post_name;
                    $media->gallery_content = $content;
                    $media->gallery_type = $p->post_type;
                    $media->gallery_status = $p->post_status;
                    $media->gallery_source = $sources;
                    $media->created_by = $user_id;
                    $media->modified_by = $user_id;
                    $media->created_at = $p->post_date;
                    $media->updated_at = $p->post_modified;

                    try {
                        $media->save();
                        $f++;
                    } catch (Exception $e) {
                        $this->info($e->getMessage());
                        return false;
                    }
                });

                $this->info(sprintf('Current pic insert: %s, vid insert: %s', $c, $d));
            });

        $this->info(sprintf('Total pic checked: %s, vid checked: %s, media checked: %s, inserted: %s', $c, $d, $e, $f));
    }
}
