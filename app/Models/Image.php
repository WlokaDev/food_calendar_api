<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string $path
 * @property string $imageable_type,
 * @property int|null $imageable_id
 * @property boolean $main
 */

class Image extends Model
{
    public function imageable() : MorphTo
    {
        return $this->morphTo('imageable');
    }

    /**
     * @param int $id
     * @return void
     */

    public function setParentIdAttribute(int $id): void
    {
        $this->attributes['imageable_id'] = $id;
    }
}
