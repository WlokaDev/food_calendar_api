<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Translatable\HasTranslations;

/**
 * @property string $name
 * @property ?string $description
 * @property ?string $icon;
 * @property-read ?Carbon $deleted_at;
 */

class ProductCategory extends Model
{
    use HasTranslations;

    public $translatable = [
        'name',
        'description'
    ];
}
