<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property boolean $bought
 * @property ?string $custom_name
 * @property ?string $unit
 * @property ?string $value
 * @property-read ShoppingList $shoppingList
 * @property-read ?Product $product
 */

class ShoppingListProduct extends Model
{
    use HasFactory;

    public function shoppingList() : BelongsTo
    {
        return $this->belongsTo(ShoppingList::class);
    }

    public function product() : BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
