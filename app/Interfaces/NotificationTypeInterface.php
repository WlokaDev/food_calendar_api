<?php

namespace App\Interfaces;

use App\Models\NotificationTemplate;
use Illuminate\Database\Eloquent\Model;

interface NotificationTypeInterface
{

    public function __construct(array $data, Model $notifiable, NotificationTemplate $notificationTemplate);

    public function send();
}
