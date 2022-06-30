<?php

namespace App\Http\Controllers\v1;

use App\Enums\AcceptableChangeActionTypeEnum;
use App\Http\Requests\RecipeFeedbackRequest;
use App\Http\Resources\v1\RecipeFeedbackCollection;
use App\Http\Services\AcceptableChangesService;
use App\Http\Services\ImagesService;
use App\Http\Services\RecipeFeedbacksService;
use App\Models\Recipe;
use App\Models\RecipeFeedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class RecipeFeedbacksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Recipe $recipe
     * @return JsonResponse
     */

    public function index(Recipe $recipe) : JsonResponse
    {
        $feedbacks = $recipe
            ->feedbacks()
            ->with(['user', 'images'])
            ->paginate();

        return $this->successResponse(
            new RecipeFeedbackCollection(
                $feedbacks
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param RecipeFeedbackRequest $request
     * @param Recipe $recipe
     * @param AcceptableChangesService $acceptableChangesService
     * @param RecipeFeedbacksService $feedbacksService
     * @return JsonResponse
     */

    public function store(
        RecipeFeedbackRequest $request,
        Recipe $recipe,
        AcceptableChangesService $acceptableChangesService,
        RecipeFeedbacksService $feedbacksService
    ) : JsonResponse
    {
        $data = $request->validated();

        try {
            DB::transaction(function() use($data, $recipe, $acceptableChangesService, $feedbacksService) {
                $recipeFeedback = $feedbacksService->assignAttributes(
                    $recipe,
                    Arr::get($data, 'feedback'),
                    Arr::get($data, 'feedback_star'),
                    false
                )->getRecipeFeedback();

                $recipeFeedbackAcceptable = $acceptableChangesService->saveForApproval(
                    $recipeFeedback,
                    AcceptableChangeActionTypeEnum::NEW
                );

                $childAcceptable = [];

                if(isset($data['images'])) {
                    foreach($data['images'] as $imageData) {
                        $image = (new ImagesService())->storeInS3(
                            $recipeFeedback,
                            $imageData,
                            save: false
                        );

                        $childAcceptable[] = $acceptableChangesService->saveForApproval(
                            $image,
                            AcceptableChangeActionTypeEnum::NEW
                        )->id;
                    }

                    $acceptableChangesService->associateChildAcceptableChange(
                        $recipeFeedbackAcceptable,
                        $childAcceptable
                    );
                }
            }, 3);
        } catch (\Exception $exception) {
            reportError($exception);

            return $this->errorResponse(
                __('messages.Something went wrong'),
                status: 500
            );
        }

        return $this->successResponse(
            __('messages.Feedback sent to verification')
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param RecipeFeedbackRequest $request
     * @param Recipe $recipe
     * @param RecipeFeedback $recipeFeedback
     * @param AcceptableChangesService $acceptableChangesService
     * @return JsonResponse
     */

    public function update(
        RecipeFeedbackRequest $request,
        Recipe $recipe,
        RecipeFeedback $recipeFeedback,
        AcceptableChangesService $acceptableChangesService
    ) : JsonResponse
    {
        $data = $request->validated();

        try {
            DB::transaction(function() use($data, $recipe, $acceptableChangesService, $recipeFeedback) {
                $recipeFeedback = (new RecipeFeedbacksService($recipeFeedback))->assignAttributes(
                    $recipe,
                    Arr::get($data, 'feedback'),
                    Arr::get($data, 'feedback_star'),
                    false
                )->getRecipeFeedback();

                $recipeFeedbackAcceptable = $acceptableChangesService->saveForApproval(
                    $recipeFeedback,
                    AcceptableChangeActionTypeEnum::UPDATE
                );

                $childAcceptable = [];

                if(isset($data['images'])) {
                    foreach($data['images'] as $imageData) {
                        $image = (new ImagesService())->storeInS3(
                            $recipeFeedback,
                            $imageData,
                            save: false
                        );

                        $childAcceptable[] = $acceptableChangesService->saveForApproval(
                            $image,
                            AcceptableChangeActionTypeEnum::UPDATE
                        )->id;
                    }

                    $acceptableChangesService->associateChildAcceptableChange(
                        $recipeFeedbackAcceptable,
                        $childAcceptable
                    );
                }
            }, 3);
        } catch (\Exception $exception) {
            reportError($exception);

            return $this->errorResponse(
                __('messages.Something went wrong'),
                status: 500
            );
        }

        return $this->successResponse(
            __('messages.Feedback sent to verification')
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Recipe $recipe
     * @param RecipeFeedback $recipeFeedback
     * @return JsonResponse
     */

    public function destroy(Recipe $recipe, RecipeFeedback $recipeFeedback) : JsonResponse
    {
        $recipeFeedback->delete();

        return $this->successResponse(
            __('messages.Feedback removed')
        );
    }
}
