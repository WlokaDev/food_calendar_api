<?php

namespace App\Http\Services;

use App\Models\Image;
use App\Models\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImagesService
{
    /**
     * @param Model $model
     * @param UploadedFile $image
     * @param bool $main
     * @param bool $save
     * @return Image
     */

    public function storeInS3(
        Model $model,
        UploadedFile $image,
        bool $main = false,
        bool $save = true
    ) : Image
    {
        $path = $this->prepareForSave($image, $model);

        $imageModel = new Image();
        $imageModel->imageable_type = $model::class;
        $imageModel->imageable_id = $model->id;
        $imageModel->main = $main;
        $imageModel->path = $path;

        if(Storage::disk('s3')->put($path, file_get_contents($image))) {
            if($save) {
                $imageModel->save();
            }

            return $imageModel;
        }

        throw new \RuntimeException('Unable to save image in s3');
    }

    /**
     * @param UploadedFile $image
     * @param Model $model
     * @return string|false
     */

    private function prepareForSave(UploadedFile $image, Model $model) : string|false
    {
        try {
            $extension = $image->getClientOriginalExtension();
            $content = file_get_contents($image);

            if(!$content) {
                throw new \RuntimeException('Content of image is empty.');
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return false;
        }

        $shortClass = Str::of(
            (new \ReflectionClass($model))->getShortName()
        )->lower();

        $fileName = base64_encode(Str::uuid()) . '.' . $extension;

        return "/images/$shortClass/$fileName";
    }
}
