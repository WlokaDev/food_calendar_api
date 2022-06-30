<?php
namespace App\Http\Services\Notifications;

use App\Models\Notification;
use App\Models\NotificationTemplate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class NotificationService
{

    private Notification $notification;

    public function __construct(Notification $notification = null)
    {
        $this->notification = $notification ?: new Notification();
    }

    /**
     * @param array $data
     * @param NotificationTemplate $notificationTemplate
     * @param \Illuminate\Database\Eloquent\Model $notifiable
     * @return Notification
     */
    public function assignData(array $data, NotificationTemplate $notificationTemplate, Model $notifiable): Notification
    {
        $this->notification->notification_template_id = $notificationTemplate->id;
        $this->notification->user_id = $notifiable->id;
        $this->notification->data = $data;

        $this->notification->save();

        return $this->notification;
    }


    /**
     * @param Notification|null $notification
     * @return Notification
     */
    public function read(Notification $notification = null): Notification
    {
        $notification = $notification ?: $this->notification;
        $notification->read_at = Carbon::now();
        $notification->save();
        return $notification;
    }
}
