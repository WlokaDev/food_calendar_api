<?php

namespace App\Http\Controllers\v1;

use App\Enums\ShoppingListStatusEnum;
use App\Enums\UnitEnum;
use App\Http\Requests\AddProductToShoppingListRequest;
use App\Http\Requests\StoreShoppingListRequest;
use App\Http\Resources\v1\ShoppingListCollection;
use App\Http\Resources\v1\ShoppingListResource;
use App\Http\Services\ShoppingListsService;
use App\Models\ShoppingList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Enum;

class ShoppingListsController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */

    public function index(Request $request) : JsonResponse
    {
        $data = $request->validate([
            'status' => new Enum(ShoppingListStatusEnum::class)
        ]);

        $shoppingLists = Auth::user()
            ->shoppingLists()
            ->where('status', $data['status'])
            ->paginate();

        return $this->successResponse(
            new ShoppingListCollection(
                $shoppingLists
            )
        );
    }

    /**
     * @param ShoppingList $shoppingList
     * @return JsonResponse
     */

    public function show(ShoppingList $shoppingList) : JsonResponse
    {
        $shoppingList->load('shoppingListProducts');

        return $this->successResponse(
            new ShoppingListResource(
                $shoppingList
            )
        );
    }

    /**
     * @param StoreShoppingListRequest $request
     * @param ShoppingListsService $shoppingListsService
     * @return JsonResponse
     */

    public function store(StoreShoppingListRequest $request, ShoppingListsService $shoppingListsService) : JsonResponse
    {
        $data = $request->validated();

        try {
            $shoppingList = DB::transaction(function() use($data, $shoppingListsService) {
                return $shoppingListsService->assignAttributes(
                    $data['name'],
                    Arr::get($data, 'description')
                )->getShoppingList();
            }, 3);
        } catch (\Exception $exception) {
            reportError($exception);

            return $this->errorResponse(
                __('messages.Something went wrong'),
                status: 500
            );
        }

        return $this->successResponse(
            new ShoppingList(
                $shoppingList
            )
        );
    }

    /**
     * @param AddProductToShoppingListRequest $request
     * @param ShoppingList $shoppingList
     * @return JsonResponse
     */

    public function addProduct(AddProductToShoppingListRequest $request, ShoppingList $shoppingList) : JsonResponse
    {
        $data = $request->validated();

        try {
            $shoppingListProduct = DB::transaction(function () use($data, $shoppingList) {
                $service = new ShoppingListsService($shoppingList);
                return $service->addProduct(
                    UnitEnum::tryFrom(
                        Arr::get($data, 'unit')
                    ),
                    Arr::get($data, 'value'),
                    Arr::get($data, 'product_id'),
                    Arr::get($data, 'custom_name')
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

        )
    }
}
