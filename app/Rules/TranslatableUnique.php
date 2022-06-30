<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class TranslatableUnique implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    /**
     * @var string
     */

    protected string $table;

    /**
     * @var string
     */

    protected string $locale;

    /**
     * @var string
     */

    protected string $column;

    /**
     * @var int|null
     */

    protected ?int $id;

    public function __construct(string $table, string $column, ?int $id = null)
    {
        $this->table = $table;
        $this->column = $column;
        $this->locale = app()->getLocale();
        $this->id = $id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return DB::table($this->table)
            ->where("$this->column->$this->locale", $value)
            ->when(isset($this->id), function (Builder $q) {
                return $q->where('id', '!=', $this->id);
            })
            ->doesntExist();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'The :attribute has already been taken.';
    }
}
