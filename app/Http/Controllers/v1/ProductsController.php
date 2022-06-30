<?php

namespace App\Http\Controllers\v1;


use App\Enums\AcceptableChangeActionTypeEnum;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\v1\ProductCollection;
use App\Http\Services\AcceptableChangesService;
use App\Http\Services\ProductsService;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductsController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */

    public function index(Request $request): JsonResponse
    {
        $products = Product
            ::query()
            ->when($request->has('q'), function (Builder $q) use ($request) {
                return $q
                    ->where('name->' . app()->getLocale(), 'like', '%' . $request->get('q') . '%')
                    ->orWhere('description->' . app()->getLocale(), 'like', '%' . $request->get('q') . '%');
            })->paginate(
                $request->get('per_page', 15)
            );

        return $this->successResponse(
            new ProductCollection(
                $products
            )
        );
    }

    /**
     * @param StoreProductRequest $request
     * @param ProductsService $productsService
     * @param AcceptableChangesService $acceptableChangesService
     * @return JsonResponse
     */

    public function store(
        StoreProductRequest $request,
        ProductsService $productsService,
        AcceptableChangesService $acceptableChangesService
    ): JsonResponse
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use ($data, $productsService, $acceptableChangesService) {
                $product = $productsService->assignAttributes(
                    $data['category_id'],
                    $data['name'],
                    Arr::get($data, 'description'),
                    Arr::get($data, 'image'),
                    false
                )->getProduct();

                $acceptableChangesService->saveForApproval(
                    $product,
                    AcceptableChangeActionTypeEnum::NEW
                );
            }, 3);
        } catch (\Exception $exception) {
            reportError($exception);

            return $this->errorResponse(
                __('messages.Something wen wrong')
            );
        }

        return $this->successResponse(
            __('messages.Product has been send to check.')
        );
    }

    /**
     * @param UpdateProductRequest $request
     * @param Product $product
     * @param AcceptableChangesService $acceptableChangesService
     * @return JsonResponse
     */

    public function update(
        UpdateProductRequest $request,
        Product $product,
        AcceptableChangesService $acceptableChangesService
    ) : JsonResponse
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use($data, $product, $acceptableChangesService) {
                $productService = new ProductsService($product);
                $product = $productService
                    ->assignAttributes(
                        Arr::get($data, 'category_id'),
                        $data['name'],
                        Arr::get($data, 'description'),
                        Arr::get($data, 'image'),
                        false
                    )->getProduct();

                $acceptableChangesService->saveForApproval(
                    $product,
                    AcceptableChangeActionTypeEnum::UPDATE
                );
            }, 3);
        } catch (\Exception $exception) {
            reportError($exception);

            return $this->errorResponse(
                __('messages.Something went wrong'),
                status: 500
            );
        }

        return $this->successResponse(
            __('messages.Product has been send to check.')
        );
    }

    /**
     * @param Product $product
     * @return JsonResponse
     */

    public function destroy(Product $product) : JsonResponse
    {
        $product->delete();

        return $this->successResponse(
            __('messages.Product trashed')
        );
    }

    /**
     * @param Product $product
     * @return StreamedResponse|void
     */

    public function getImage(Product $product)
    {
        if($product->image_path && Storage::disk('s3')->exists($product->image_path)) {
            return response()->stream(function () use($product) {
                echo Storage::disk('s3')->get($product->image_path);
            });
        }

        abort(404);
    }
}
