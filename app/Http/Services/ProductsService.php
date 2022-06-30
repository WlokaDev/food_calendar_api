<?php

namespace App\Http\Services;

use App\Enums\AcceptableChangeActionTypeEnum;
use App\Models\Product;
use App\Models\ProductCategory;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProductsService
{
    /**
     * @var Product
     */

    protected Product $product;

    /**
     * @param Product|null $product
     */

    public function __construct(?Product $product = null)
    {
        $this->product = $product ?: new Product();
    }

    /**
     * @param ProductCategory|int $productCategory
     * @param string $name
     * @param string|null $description
     * @param null $image
     * @param bool $save
     * @return $this
     */

    public function assignAttributes(
        ProductCategory|int $productCategory,
        string $name,
        ?string $description = null,
        $image = null,
        bool $save = true
    ) : self
    {
        $this->product->category()->associate($productCategory);
        $this->product->name = $name;
        $this->product->description = $description;

        if($image) {
            $this->product->image_path = $this->storeImage($image);

        }

        if($save) {
            $this->product->save();
        }

        return $this;
    }

    /**
     * @param UploadedFile $image
     * @return string
     */

    public function storeImage(
        UploadedFile $image
    ) : string
    {
        $extension = $image->getClientOriginalExtension();
        $content = file_get_contents($image);

        if(!$content) {
            throw new \RuntimeException('Content of image is empty.');
        }

        $fileName = Str::uuid() . '.' . $extension;
        $path = "/products/images/" . $fileName;

        if(Storage::disk('local')->put($path, $content)) {
            return $path;
        }

        throw new \RuntimeException('Unable to save file.');
    }

    /**
     * @return Product
     */

    public function getProduct(): Product
    {
        return $this->product;
    }
}
