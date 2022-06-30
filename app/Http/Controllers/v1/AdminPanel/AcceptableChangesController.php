<?php

namespace App\Http\Controllers\v1\AdminPanel;

use App\Enums\AcceptableChangeStatusEnum;
use App\Http\Controllers\v1\Controller;
use App\Http\Resources\v1\AcceptableChangeCollection;
use App\Http\Services\AcceptableChangesService;
use App\Models\AcceptableChange;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class AcceptableChangesController extends Controller
{
    public function __construct()
    {
//        $this->authorizeResource(AcceptableChange::class, 'acceptable_change');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */

    public function index(Request $request) : JsonResponse
    {
        $data = $request->validate([
            'per_page' => ['nullable', 'integer']
        ]);

        $acceptableChanges = AcceptableChange
            ::query()
            ->orderByRaw('FIELD(status, ' . sprintf("'%s'", implode("', '", AcceptableChangeStatusEnum::values())) . ')')
            ->paginate(
                Arr::get($data, 'per_page', 15)
            );

        return $this->successResponse(
            new AcceptableChangeCollection($acceptableChanges)
        );
    }

    /**
     * @param AcceptableChange $acceptableChange
     * @param AcceptableChangesService $acceptableChangesService
     * @return JsonResponse
     */

    public function accept(AcceptableChange $acceptableChange, AcceptableChangesService $acceptableChangesService) : JsonResponse
    {
        try {
            $acceptableChangesService->acceptChanges(
                $acceptableChange
            );
        } catch (\Exception $exception) {
            reportError($exception);

            return $this->errorResponse(
                __('messages.Something went wrong'),
                status: 500
            );
        }

        return $this->successResponse(
            __('messages.Changes has been accepted')
        );
    }

    /**
     * @param Request $request
     * @param AcceptableChange $acceptableChange
     * @param AcceptableChangesService $acceptableChangesService
     * @return JsonResponse
     */

    public function reject(
        Request $request,
        AcceptableChange $acceptableChange,
        AcceptableChangesService $acceptableChangesService
    ) : JsonResponse
    {
        $data = $request->validate([
            'reason' => ['required', 'min:10', 'string']
        ]);

        try {
            $acceptableChangesService->rejectChanges(
                $acceptableChange,
                $data['reason']
            );
        } catch (\Exception $exception) {
            reportError($exception);

            return $this->errorResponse(
                __('messages.Something went wrong'),
                status: 500
            );
        }

        return $this->successResponse(
            __('messages.Changes has been rejected')
        );
    }
}
