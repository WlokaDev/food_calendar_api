<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $name
 * @property ?string $description
 * @property string $status
 * @property-read Collection|ShoppingListProduct[] $shoppingListProducts
 * @property-read User $user
 */

class ShoppingList extends Model
{
    use HasFactory;

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shoppingListProducts() : HasMany
    {
        return $this->hasMany(ShoppingListProduct::class);
    }
}
