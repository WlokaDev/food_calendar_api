<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property mixed $notification_template_id
 * @property Carbon|mixed $read_at
 * @property array|mixed $data
 * @property mixed $user_id
 */
class Notification extends Model
{
    use HasFactory;
}
