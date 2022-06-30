<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property-read int $id
 * @property-read Carbon $created_at;
 * @property-read Carbon $updated_at;
 * @method static find(string|array $id)
 */

class Model extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;
}
