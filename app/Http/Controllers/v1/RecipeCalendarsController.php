<?php

namespace App\Http\Controllers\v1;

use App\Http\Resources\v1\RecipeCalendarResource;
use App\Http\Services\RecipeCalendars\DrawRecipesService;
use App\Http\Services\RecipeCalendarsService;
use App\Models\RecipeCalendar;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class RecipeCalendarsController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */

    public function index(Request $request) : JsonResponse
    {
        $data = $request->validate([
            'week' => ['nullable', 'integer', 'lt:0']
        ]);

        $recipes = RecipeCalendar
            ::query()
            ->when(isset($data['week']), function (Builder $q) use($data) {
                return $q->whereBetween('day', [
                    now()->subWeeks($data['week'])->startOfWeek(),
                    now()->subWeeks($data['week'])->endOfWeek(),
                ]);
            }, function (Builder $q) {
                return $q->whereBetween('day', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ]);
            })
            ->where('user_id', Auth::id())
            ->get();

        return $this->successResponse(
            RecipeCalendarResource::collection(
                $recipes
            )
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */

    public function reDrawRecipeForDay(Request $request) : JsonResponse
    {
        $data = $request->validate([
            'day' => ['required', 'date'],
            'category' => ['required', 'exists:categories,id']
        ]);

        // TODO Wywołać serwis do losowania przepisów
    }

    /**
     * @param Request $request
     * @param RecipeCalendarsService $recipeCalendarsService
     * @return JsonResponse
     */

    public function assignRecipeForDay(Request $request, RecipeCalendarsService $recipeCalendarsService) : JsonResponse
    {
        $data = $request->validate([
            'recipe_id' => ['required', 'exists:recipes,id'],
            'category' => ['required', 'exists:categories,id'],
            'day' => ['required', 'date']
        ]);

        try {
            $recipeCalendarsService->assignAttributes(
                $data['recipe_id'],
                $data['day']
            );
        } catch (\Exception $exception) {
            reportError($exception);

            return $this->errorResponse(
                __('messages.Something went wrong'),
                status: 500
            );
        }

        return $this->successResponse(
            __('messages.Recipe assigned to day.')
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */

    public function removeRecipeForDay(Request $request) : JsonResponse
    {
        $data = $request->validate([
            'day' => ['required', 'date']
        ]);

        try {
            RecipeCalendar
                ::query()
                ->where([
                    'user_id' => Auth::id(),
                    'day' => $data['day']
                ])->delete();
        } catch (\Exception $exception) {
            reportError($exception);

            return $this->errorResponse(
                __('messages.Something went wrong'),
                status: 500
            );
        }

        return $this->successResponse(
            __('messages.Recipe remove from day.')
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */

    public function drawRecipes(Request $request) : JsonResponse
    {
        $data = $request->validate([
            'days' => ['nullable', 'integer', 'min:1'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['required', 'exists:categories,id']
        ]);

        try {
            $drawRecipesService = new DrawRecipesService(
                $data['categories'],
                Arr::get($data, 'days', 7),
            );

            $recipes = $drawRecipesService->drawRecipes();
        } catch (\Exception $exception) {
            reportError($exception);

            return $this->errorResponse(
                __('messages.Something went wrong'),
                status: 500
            );
        }

        return $this->successResponse(
            RecipeCalendarResource::collection(
                $recipes
            )
        );
    }
}
