<?php

namespace App\Http\Services;

use App\Enums\ShoppingListStatusEnum;
use App\Enums\UnitEnum;
use App\Models\Product;
use App\Models\ShoppingList;
use App\Models\ShoppingListProduct;
use Illuminate\Support\Facades\Auth;

class ShoppingListsService
{
    /**
     * @var ShoppingList
     */

    protected ShoppingList $shoppingList;

    public function __construct(?ShoppingList $shoppingList = null)
    {
        $this->shoppingList = $shoppingList ?: new ShoppingList();
    }

    /**
     * @param string $name
     * @param string|null $description
     * @param ShoppingListStatusEnum $status
     * @return $this
     */

    public function assignAttributes(
        string $name,
        ?string $description = null,
        ShoppingListStatusEnum $status = ShoppingListStatusEnum::ACTIVE
    ) : self
    {
        $this->shoppingList->name = $name;
        $this->shoppingList->description = $description;
        $this->shoppingList->status = $status;
        $this->shoppingList->user()->associate(Auth::user());
        $this->shoppingList->save();

        return $this;
    }

    /**
     * @param UnitEnum|null $unit
     * @param string|null $value
     * @param Product|int|null $product
     * @param string|null $customName
     * @return ShoppingListProduct
     */

    public function addProduct(
        ?UnitEnum $unit = null,
        ?string $value = null,
        Product|int|null $product = null,
        ?string $customName = null
    ) : ShoppingListProduct
    {
        $shoppingListProduct = new ShoppingListProduct();
        $shoppingListProduct->shoppingList()->associate($this->shoppingList);
        $shoppingListProduct->product()->associate($product);
        $shoppingListProduct->unit = $unit->value;
        $shoppingListProduct->value = $value;
        $shoppingListProduct->custom_name = $customName;
        $shoppingListProduct->save();

        return $shoppingListProduct;
    }

    /**
     * @return ShoppingList
     */

    public function getShoppingList(): ShoppingList
    {
        return $this->shoppingList;
    }

}
