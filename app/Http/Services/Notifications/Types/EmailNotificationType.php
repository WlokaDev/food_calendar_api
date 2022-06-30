<?php
namespace App\Http\Services\Notifications\Types;


use App\Interfaces\NotificationTypeInterface;
use App\Jobs\EmailNotificationSendJob;
use App\Models\NotificationTemplate;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class EmailNotificationType implements NotificationTypeInterface
{

    private array $data;

    private Model $notifiable;

    private NotificationTemplate $notificationTemplate;

    /**
     * @param array $data
     * @param Model $notifiable
     * @param NotificationTemplate $notificationTemplate
     */
    public function __construct(array $data, Model $notifiable, NotificationTemplate $notificationTemplate)
    {
        $this->notifiable = $notifiable;
        $this->data = $data;
        $this->notificationTemplate = $notificationTemplate;
    }

    public function send(array $files = null) :void
    {
        EmailNotificationSendJob::dispatch($this->data, $this->notifiable, $this->notificationTemplate, $files);
    }
}
