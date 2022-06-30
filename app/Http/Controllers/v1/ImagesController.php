<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImagesController extends Controller
{
    /**
     * @param Image $image
     * @return StreamedResponse
     */

    public function show(Image $image) : StreamedResponse
    {
        if(Storage::disk('s3')->exists($image->path)) {
            return Storage::disk('s3')->download($image->path);
        }

        abort(404);
    }
}
