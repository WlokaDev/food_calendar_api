<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property ?float $min_price
 * @property ?float $max_price
 * @property ?int $min_calories
 * @property ?int $max_calories
 * @property ?int $min_difficulty_of_execution
 * @property ?int $max_difficulty_of_execution
 * @property ?int $min_execution_time
 * @property ?int $max_execution_time
 */

class UserPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id'
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
