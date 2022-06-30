<?php

namespace App\StateMachines;

use App\Enums\AcceptableChangeStatusEnum;
use Asantibanez\LaravelEloquentStateMachines\StateMachines\StateMachine;

class AcceptableChangeStatusStateMachine extends StateMachine
{
    public function recordHistory(): bool
    {
        return true;
    }

    public function transitions(): array
    {
        return [
            AcceptableChangeStatusEnum::PROCESSING->value => [
                AcceptableChangeStatusEnum::TO_IMPROVE->value,
                AcceptableChangeStatusEnum::ACCEPTED->value,
                AcceptableChangeStatusEnum::REJECTED->value,
                AcceptableChangeStatusEnum::DELETED->value
            ]
        ];
    }

    public function defaultState(): ?string
    {
        return AcceptableChangeStatusEnum::PROCESSING->value;
    }
}
