<?php
namespace App\Http\Services\Notifications;

use App\Models\NotificationTemplate;

class NotificationTemplateService
{

    private NotificationTemplate $notificationTemplate;

    /**
     * @param \App\Models\NotificationTemplate|null $notificationTemplate
     */
    public function __construct(NotificationTemplate $notificationTemplate = null)
    {
        $this->notificationTemplate = $notificationTemplate ?: new NotificationTemplate();
    }

    /**
     * @param array $data
     * @return \App\Models\NotificationTemplate
     */
    public function assignData(array $data): NotificationTemplate
    {
        $this->notificationTemplate->name = $data['name'];
        $this->notificationTemplate->description = $data['description'];
        $this->notificationTemplate->type = $data['type'];
        $this->notificationTemplate->status = $data['status'];
        $this->notificationTemplate->save();
        return $this->notificationTemplate;
    }

}
