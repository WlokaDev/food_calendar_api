<?php

namespace App\Http\Resources\v1;

use App\Enums\AcceptableChangeActionTypeEnum;
use App\Enums\AcceptableChangeStatusEnum;
use App\Models\AcceptableChange;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin AcceptableChange
 */

class AcceptableChangeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'model' => $this->model,
            'model_id' => $this->model_id,
            'action' => $this->action,
            'status' => $this->status,
            'changed_attributes' => $this->changed_attributes,
            'created_at' => $this->created_at->format('d.m.Y H:i')
        ];
    }
}
