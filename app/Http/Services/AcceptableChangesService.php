<?php

namespace App\Http\Services;

use App\Enums\AcceptableChangeActionTypeEnum;
use App\Enums\AcceptableChangeStatusEnum;
use App\Models\AcceptableChange;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AcceptableChangesService
{
    /**
     * @param Model $model
     * @param AcceptableChangeActionTypeEnum $actionType
     * @return AcceptableChange
     * @throws Exception
     */

    public function saveForApproval(
        Model                          $model,
        AcceptableChangeActionTypeEnum $actionType
    ): AcceptableChange
    {
        if ($actionType === AcceptableChangeActionTypeEnum::UPDATE && !$model->id) {
            throw new \RuntimeException('Update action without provide model id is not available');
        }

        $acceptableChange = new AcceptableChange();
        $acceptableChange->model = $model::class;
        $acceptableChange->model_id = $model->id;
        $acceptableChange->author()->associate(Auth::user());
        $acceptableChange->changed_attributes = $model->toArray();
        $acceptableChange->action = $actionType->value;
        $acceptableChange->save();

        return $acceptableChange;
    }

    /**
     * @param array $data
     * @param string $model
     * @param int|null $parentId
     * @return int
     */

    private function addRecord(
        array  $data,
        string $model,
        ?int   $parentId = null
    ): int
    {
        $record = new $model;
        $record->fill($data);

        if ($parentId) {
            $record->parent_id = $parentId;
        }

        $record->save();

        return $record->id;
    }

    /**
     * @param array $data
     * @param string $model
     * @param int $modelId
     * @return int
     */

    private function updateRecord(
        array  $data,
        string $model,
        int    $modelId
    ): int
    {
        $record = $model::findOrFail($modelId);
        $record->fill($data);
        $record->save();

        return $record->id;
    }

    /**
     * @param AcceptableChange $acceptableChange
     * @param int|null $parentId
     * @return void
     * @throws Exception
     */

    public function acceptChanges(
        AcceptableChange $acceptableChange,
        ?int             $parentId = null
    ): void
    {
        $recordId = match ($acceptableChange->action) {
            AcceptableChangeActionTypeEnum::NEW => $this->addRecord(
                $acceptableChange->changed_attributes,
                $acceptableChange->model,
                $parentId
            ),
            AcceptableChangeActionTypeEnum::UPDATE => $this->updateRecord(
                $acceptableChange->changed_attributes, $acceptableChange->model,
                $acceptableChange->model_id
            )
        };

        $acceptableChange->update([
            'status' => AcceptableChangeStatusEnum::ACCEPTED
        ]);

//        $acceptableChange->status()->transitionTo(
//            AcceptableChangeStatusEnum::ACCEPTED->value
//        );

        foreach ($acceptableChange->childAcceptableChanges as $childAcceptableChange) {
            $this->acceptChanges($childAcceptableChange, $recordId);
        }
    }

    /**
     * @param AcceptableChange $acceptableChange
     * @param string $reason
     * @return void
     */

    public function rejectChanges(
        AcceptableChange $acceptableChange,
        string           $reason
    ): void
    {
//        $acceptableChange->status()->transitionTo(
//            AcceptableChangeStatusEnum::REJECTED->value,
//            [
//                'reason' => $reason,
//                'created_at' => now()->toDateTimeString()
//            ]
//        );

        $acceptableChange->update([
            'status' => AcceptableChangeStatusEnum::REJECTED
        ]);

        foreach ($acceptableChange->childAcceptableChanges as $childAcceptableChange) {
            $this->rejectChanges($childAcceptableChange, $reason);
        }

        // TODO Dodać notyfikację
    }

    /**
     * @param AcceptableChange $acceptableChange
     * @param string $reason
     * @return void
     */

    public function setToImprove(
        AcceptableChange $acceptableChange,
        string           $reason
    ): void
    {
//        $acceptableChange->status()->transitionTo(
//            AcceptableChangeStatusEnum::TO_IMPROVE->value,
//            [
//                'reason' => $reason,
//                'created_at' => now()->toDateTimeString()
//            ]
//        );

        $acceptableChange->update([
            'status' => AcceptableChangeStatusEnum::TO_IMPROVE
        ]);

        foreach ($acceptableChange->childAcceptableChanges as $childAcceptableChange) {
            $this->setToImprove($childAcceptableChange, $reason);
        }

        // TODO Dodać notyfikacje
    }

    /**
     * @param AcceptableChange $parentAcceptableChange
     * @param array $childAcceptableChanges
     * @return void
     */

    public function associateChildAcceptableChange(
        AcceptableChange $parentAcceptableChange,
        array            $childAcceptableChanges
    ): void
    {
        $parentAcceptableChange->childAcceptableChanges()->attach($childAcceptableChanges);
    }
}
