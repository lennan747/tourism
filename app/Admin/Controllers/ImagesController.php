<?php

namespace App\Admin\Controllers;

use App\Models\Image;
use App\Handlers\ImageUploadHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ImageRequest;


class ImagesController extends Controller
{
    public function store(ImageRequest $request, ImageUploadHandler $uploader, Image $image)
    {
        $size = $request->type == 'avatar' ? 362 : 1024;

        $result = $uploader->save($request->image, str_plural($request->type), '');




        $urls = [];

        foreach ($request->file() as $file) {
            $image->path = $result['path'];
            $image->type = $request->type;
            $image->save();
            $urls[] = Storage::url($file->store('images'));
        }

        return [
            "errno" => 0,
            "data"  => $urls,
        ];
    }

}
