<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

/**
 * @property-read ProductCategory $productCategory
 * @property ?string $image_path
 * @property string $name
 * @property ?string $description
 * @property-read ProductCategory $category
 */

class Product extends Model
{
    use HasTranslations, SoftDeletes;

    public $translatable = [
        'name',
        'description'
    ];

    protected $fillable = [
        'category_id',
        'image_path',
        'name',
        'description'
    ];

    public function category() : BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }
}
