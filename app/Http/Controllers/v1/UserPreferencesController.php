<?php

namespace App\Http\Controllers\v1;


use App\Http\Requests\UserPreferenceRequest;
use App\Http\Resources\v1\UserExcludeProductResource;
use App\Http\Resources\v1\UserPreferenceResource;
use App\Http\Services\UserPreferencesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class UserPreferencesController extends Controller
{
    /**
     * @return JsonResponse
     */

    public function show() : JsonResponse
    {
        return $this->successResponse(
            new UserPreferenceResource(
                Auth::user()->preferences
            )
        );
    }

    /**
     * @param UserPreferenceRequest $request
     * @param UserPreferencesService $preferencesService
     * @return JsonResponse
     */

    public function store(UserPreferenceRequest $request, UserPreferencesService $preferencesService) : JsonResponse
    {
        $data = $request->validated();

        try {
            $preferencesService->assignAttributes(
                Arr::get($data, 'min_price'),
                Arr::get($data, 'max_price'),
                Arr::get($data, 'min_calories'),
                Arr::get($data, 'max_calories'),
                Arr::get($data, 'min_difficulty_of_execution'),
                Arr::get($data, 'max_difficulty_of_execution'),
                Arr::get($data, 'min_execution_time'),
                Arr::get($data, 'max_execution_time')
            );
        } catch (\Exception $exception) {
            reportError($exception);

            return $this->errorResponse(
                __('messages.Something went wrong'),
                status: 500
            );
        }

        return $this->successResponse(
            __('messages.Preferences saved')
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */

    public function updateListOfExcludedProducts(Request $request) : JsonResponse
    {
        $data = $request->validate([
            'products' => [
                'array',
                'required'
            ],
            'products.*' => [
                'exists:products,id'
            ]
        ]);

        try {
            Auth::user()->excludedProducts()->sync($data['products']);
        } catch (\Exception $exception) {
            reportError($exception);

            return $this->errorResponse(
                __('messages.Something went wrong'),
                status: 500
            );
        }

        return $this->successResponse(
            __('messages.Excluded products synced')
        );
    }

    /**
     * @return JsonResponse
     */

    public function showListOfExcludedProducts() : JsonResponse
    {
        $products = Auth::user()->excludedProducts;

        return $this->successResponse(
            UserExcludeProductResource::collection(
                $products
            )
        );
    }
}
