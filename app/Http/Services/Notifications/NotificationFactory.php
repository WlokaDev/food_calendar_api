<?php

namespace App\Http\Services\Notifications;


use App\Http\Services\Notifications\Types\EmailNotificationType;
use App\Models\NotificationTemplate;
use Exception;
use Illuminate\Database\Eloquent\Model;

class NotificationFactory
{
    /**
     * @var array
     */

    private array $data;

    /**
     * @var Model
     */

    private Model $notifiable;

    /**
     * @var array|null
     */

    private ?array $files = null;

    /**
     * @var NotificationTemplate
     */

    private NotificationTemplate $notificationTemplate;

    /**
     * @var array|string[]
     */

    private array $notificationsTypes = [
        'email' => EmailNotificationType::class,
    ];

    /**
     * @param array $data
     * @param Model $notifiable
     */

    public function __construct(
        array $data,
        Model $notifiable
    )
    {
        $this->data = $data;
        $this->notifiable = $notifiable;
    }

    /**
     * @param array $files
     * @return $this
     */

    public function withFiles(array $files): NotificationFactory
    {
        $this->files = $files;
        return $this;
    }

    /**
     * @throws Exception
     */

    public function send(NotificationTemplate $notificationTemplate): void
    {
        if (!array_key_exists($notificationTemplate->type, $this->notificationsTypes)) {
            throw new \RuntimeException('Notification type ' . $notificationTemplate->type . ' does not exist');
        }

        (new $this->notificationsTypes[$notificationTemplate->type]($this->data, $this->notifiable, $notificationTemplate))->send($this->files);

    }
}
