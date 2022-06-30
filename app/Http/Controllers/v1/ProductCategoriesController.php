<?php

namespace App\Http\Controllers\v1;

use App\Http\Resources\v1\ProductCategoryCollection;
use App\Http\Resources\v1\ProductCategoryResource;
use App\Models\ProductCategory;
use Illuminate\Http\JsonResponse;

class ProductCategoriesController extends Controller
{
    /**
     * @return JsonResponse
     */

    public function index() : JsonResponse
    {
        $productCategories = ProductCategory
            ::query()
            ->paginate();

        return $this->successResponse(
            new ProductCategoryCollection(
                $productCategories
            )
        );
    }

    /**
     * @param ProductCategory $productCategory
     * @return JsonResponse
     */

    public function show(ProductCategory $productCategory) : JsonResponse
    {
        return $this->successResponse(
            new ProductCategoryResource(
                $productCategory
            )
        );
    }
}
