<?php

namespace App\Models;

use App\Enums\AcceptableChangeActionTypeEnum;
use App\Enums\AcceptableChangeStatusEnum;
use App\StateMachines\AcceptableChangeStatusStateMachine;
use Asantibanez\LaravelEloquentStateMachines\Traits\HasStateMachines;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $model;
 * @property ?int $model_id;
 * @property array $changed_attributes;
 * @property AcceptableChangeActionTypeEnum $action;
 * @property AcceptableChangeStatusEnum $status;
 * @property-read ?Carbon $deleted_at;
 * @property-read Collection|AcceptableChange[] $childAcceptableChanges
 */

class AcceptableChange extends Model
{
    use HasStateMachines;

//    public $stateMachines = [
//        'status' => AcceptableChangeStatusStateMachine::class
//    ];

    protected $fillable = [
        'status'
    ];

    protected $attributes = [
        'status' => AcceptableChangeStatusEnum::PROCESSING
    ];

    protected $casts = [
        'changed_attributes' => 'array',
        'action' => AcceptableChangeActionTypeEnum::class,
        'status' => AcceptableChangeStatusEnum::class
    ];

    public function childAcceptableChanges() : BelongsToMany
    {
        return $this->belongsToMany(self::class, 'parent_acceptable_change', 'parent_acceptable_change_id');
    }

    public function author() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
