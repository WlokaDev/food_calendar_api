<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Translatable\HasTranslations;

/**
 * @property mixed $status
 * @property mixed $type
 * @property mixed $description
 * @property mixed $name
 * @property mixed $id
 */
class NotificationTemplate extends Model
{
    use HasTranslations, HasFactory;

    public $translatable = [
        'content',
    ];

    protected $casts = [
        'content' => 'array'
    ];

    protected $fillable = [
        'status',
        'type',
        'description',
        'name',
        'content'
    ];

}
