<?php

namespace Modules\Gallery\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class VideoController extends GalleryController
{
    public function __construct()
    {
        parent::__construct();
        $this->setGalleryType('video');
    }

    public function validatePost(Request $request)
    {
        $validator = parent::validatePost($request);

        $validator->addRules([
                'gallery.gallery_source.*.video' => 'required',
        ]);

        return $validator;
    }

    function getQuerybuilder($column, $dir)
    {
        $query = $this->gallery_m->where('gallery_type', $this->getGalleryType())
                                ->orderBy($column, $dir);

        return $query;
    }
}
