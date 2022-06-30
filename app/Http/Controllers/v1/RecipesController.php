<?php

namespace App\Http\Controllers\v1;

use App\Enums\AcceptableChangeActionTypeEnum;
use App\Enums\CurrencyEnum;
use App\Enums\UnitEnum;
use App\Http\Requests\RecipeSearchRequest;
use App\Http\Requests\StoreRecipeRequest;
use App\Http\Requests\UpdateRecipeRequest;
use App\Http\Resources\v1\LastAddedRecipeResource;
use App\Http\Resources\v1\RecipeCollection;
use App\Http\Resources\v1\RecipeResource;
use App\Http\Services\AcceptableChangesService;
use App\Http\Services\ImagesService;
use App\Http\Services\Recipes\RecipeProductsService;
use App\Http\Services\Recipes\RecipesService;
use App\Http\Services\Recipes\RecipeStagesService;
use App\Models\Image;
use App\Models\Recipe;
use App\Models\RecipeProduct;
use App\Models\RecipeStage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class RecipesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param RecipeSearchRequest $request
     * @return JsonResponse
     */

    public function index(RecipeSearchRequest $request) : JsonResponse
    {
        $data = $request->validated();

        $recipes = Recipe
            ::query()
            ->when(isset($data['q']), function (Builder $q) use($data) {
                $search = "%" . $data['q'] . "%";
                $locale = app()->getLocale();

                return $q
                    ->where("title->$locale", 'like', $search)
                    ->orWhere("description->$locale", 'like', $search);
            })->when(isset($data['min_price']), function (Builder $q) use($data) {
                return $q->where('price', '>=', $data['min_price']);
            })->when(isset($data['max_price']), function (Builder $q) use($data) {
                return $q->where('price', '<=', $data['max_price']);
            })->when(isset($data['min_calories']), function (Builder $q) use($data) {
                return $q->where('calories', '>=', $data['min_calories']);
            })->when(isset($data['max_calories']), function (Builder $q) use($data) {
                return $q->where('calories', '<=', $data['max_calories']);
            })->when(isset($data['difficulty_of_execution']), function (Builder $q) use($data) {
                return $q->where('difficulty_of_execution', $data['difficulty_of_execution']);
            })->when(isset($data['max_execution_time']), function (Builder $q) use($data) {
                return $q->where('execution_time', '<=', $data['max_execution_time']);
            })->paginate(
                Arr::get($data, 'per_page', 15)
            );

        return $this->successResponse(
            new RecipeCollection(
                $recipes
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRecipeRequest $request
     * @param RecipesService $recipesService
     * @param AcceptableChangesService $acceptableChangesService
     * @return JsonResponse
     */

    public function store(
        StoreRecipeRequest $request,
        RecipesService $recipesService,
        AcceptableChangesService $acceptableChangesService
    ) : JsonResponse
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use($data, $recipesService, $acceptableChangesService) {
                $recipe = $recipesService->assignAttributes(
                    $data['title'],
                    $data['description'],
                    $data['difficulty_of_execution'],
                    $data['execution_time'],
                    Arr::get($data, 'price'),
                    CurrencyEnum::from(
                        Arr::get($data, 'currency')
                    ),
                    Arr::get($data, 'calories'),
                    false
                )->getRecipe();

                $recipeAcceptable = $acceptableChangesService->saveForApproval(
                    $recipe,
                    AcceptableChangeActionTypeEnum::NEW
                );

                $relationship = [];

                foreach($data['stages'] as $stageData) {
                    $stage = (new RecipeStagesService())->assignAttributes(
                        null,
                        $stageData['description'],
                        $stageData['sort'],
                        false
                    )->getRecipeStage();

                    $relationship[] = $acceptableChangesService->saveForApproval(
                        $stage,
                        AcceptableChangeActionTypeEnum::NEW
                    )->id;
                }

                foreach ($data['products'] as $productData) {
                    $recipeProduct = (new RecipeProductsService())->assignAttributes(
                        null,
                        $productData['id'],
                        UnitEnum::from($productData['unit']),
                        $productData['value'],
                        false
                    )->getRecipeProduct();

                    $relationship[] = $acceptableChangesService->saveForApproval(
                        $recipeProduct,
                        AcceptableChangeActionTypeEnum::NEW
                    )->id;
                }

                if(isset($data['images'])) {
                    foreach ($data['images'] as $imageData) {
                        $image = (new ImagesService())->storeInS3(
                            $recipe,
                            $imageData['file'],
                            $imageData['main'],
                            false
                        );

                        $relationship[] = $acceptableChangesService->saveForApproval(
                            $image,
                            AcceptableChangeActionTypeEnum::NEW
                        )->id;
                    }
                }

                $acceptableChangesService->associateChildAcceptableChange(
                    $recipeAcceptable,
                    $relationship
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
            __('messages.Recipe send to accepted')
        );
    }

    /**
     * Display the specified resource.
     *
     * @param Recipe $recipe
     * @return JsonResponse
     */
    public function show(Recipe $recipe) : JsonResponse
    {
        $recipe->load(['images', 'stages', 'products']);

        return $this->successResponse(
            new RecipeResource(
                $recipe
            )
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRecipeRequest $request
     * @param Recipe $recipe
     * @param AcceptableChangesService $acceptableChangesService
     * @return JsonResponse
     */

    public function update(UpdateRecipeRequest $request, Recipe $recipe, AcceptableChangesService $acceptableChangesService) : JsonResponse
    {
        $data = $request->validated();

        try {
            DB::transaction(static function () use($data, $recipe, $acceptableChangesService) {
                $recipe = (new RecipesService($recipe))->assignAttributes(
                    $data['title'],
                    $data['description'],
                    $data['difficulty_of_execution'],
                    $data['execution_time'],
                    Arr::get($data, 'price'),
                    CurrencyEnum::from(
                        Arr::get($data, 'currency')
                    ),
                    Arr::get($data, 'calories'),
                    false
                )->getRecipe();

                $recipeAcceptable = $acceptableChangesService->saveForApproval(
                    $recipe,
                    AcceptableChangeActionTypeEnum::UPDATE
                );

                $relationship = [];

                foreach($data['stages'] as $stageData) {
                    if(isset($stageData['id'])) {
                        $stage = RecipeStage::find($stageData['id']);
                        $actionType = AcceptableChangeActionTypeEnum::UPDATE;
                    } else {
                        $stage = null;
                        $actionType = AcceptableChangeActionTypeEnum::NEW;
                    }

                    $stage = (new RecipeStagesService($stage))->assignAttributes(
                        null,
                        $stageData['description'],
                        $stageData['sort'],
                        false
                    )->getRecipeStage();

                    $relationship[] = $acceptableChangesService->saveForApproval(
                        $stage,
                        $actionType
                    )->id;
                }

                foreach ($data['products'] as $productData) {
                    $recipeProduct = RecipeProduct
                        ::query()
                        ->where('product_id', $productData['id'])
                        ->first();

                    if($recipeProduct) {
                        $actionType = AcceptableChangeActionTypeEnum::UPDATE;
                    } else {
                        $actionType = AcceptableChangeActionTypeEnum::NEW;
                    }

                    $recipeProduct = (new RecipeProductsService($recipeProduct))->assignAttributes(
                        null,
                        $productData['id'],
                        UnitEnum::from($productData['unit']),
                        $productData['value'],
                        false
                    )->getRecipeProduct();

                    $relationship[] = $acceptableChangesService->saveForApproval(
                        $recipeProduct,
                        $actionType
                    )->id;
                }

                if(isset($data['images'])) {
                    foreach ($data['images'] as $imageData) {
                        $image = (new ImagesService())->storeInS3(
                            $recipe,
                            $imageData['file'],
                            $imageData['main'],
                            false
                        );

                        $relationship[] = $acceptableChangesService->saveForApproval(
                            $image,
                            AcceptableChangeActionTypeEnum::NEW
                        )->id;
                    }
                }

                $acceptableChangesService->associateChildAcceptableChange(
                    $recipeAcceptable,
                    $relationship
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
            __('messages.Recipe send to accepted')
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Recipe $recipe
     * @return JsonResponse
     */

    public function destroy(Recipe $recipe) : JsonResponse
    {
        $recipe->delete();

        return $this->successResponse(
            __('messages.Recipe trashed')
        );
    }

    /**
     * @param Recipe $recipe
     * @param Image $image
     * @return JsonResponse
     */


    public function destroyImage(Recipe $recipe, Image $image) : JsonResponse
    {
        $image->delete();

        return $this->successResponse(
            __('messages.Image trashed')
        );
    }

    /**
     * @return JsonResponse
     */

    public function getLastAddedRecipe() : JsonResponse
    {
        $recipe = Recipe::query()
            ->select('title', 'calories', 'id', 'execution_time')
            ->withAvg('feedbacks', 'feedback_star')
            ->with(['images' => function ($q) {
                return $q->where('main', true);
            }])
            ->latest()
            ->firstOrFail();

        return $this->successResponse(
            new LastAddedRecipeResource(
                $recipe
            )
        );
    }
}
