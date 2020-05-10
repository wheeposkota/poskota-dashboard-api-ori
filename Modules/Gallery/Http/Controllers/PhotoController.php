<?php

namespace Modules\Gallery\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class PhotoController extends GalleryController
{
    public function __construct()
    {
        parent::__construct();
        $this->setGalleryType('photo');
    }

    public function validatePost(Request $request)
    {
        $validator = parent::validatePost($request);

        $validator->addRules([
                'gallery.gallery_source.*.photo' => 'required',
        ]);

        return $validator;
    }

    function getQuerybuilder($column, $dir)
    {
        $query = $this->gallery_repository->buildQueryByCreatedUser(['gallery_type' => $this->getGalleryType()])
                                ->orderBy($column, $dir);

        return $query;
    }
}
