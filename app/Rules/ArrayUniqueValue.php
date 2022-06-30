<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ArrayUniqueValue implements Rule
{
    /**
     * @var bool|string|int|float
     */

    protected bool|string|int|float $value;

    /**
     * @var string
     */

    protected string $propertyName;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(bool|string|int|float $value, string $propertyName)
    {
        $this->value = $value;
        $this->propertyName = $propertyName;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if($this->multidimensional_array_key_exists($value, $this->propertyName)) {
            $count = 0;

            foreach(array_column($value, $this->propertyName) as $property) {
                if($property === $this->value) {
                    $count++;
                }
            }

            return $count <= 1;
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The value has to be unique in :attribute';
    }

    private function multidimensional_array_key_exists($array, $keySearch): bool
    {
        foreach ($array as $key => $item) {
            if ($key == $keySearch) {
                return true;
            } elseif (is_array($item) && self::multidimensional_array_key_exists($item, $keySearch)) {
                return true;
            }
        }
        return false;
    }
}
