<?php

namespace App\Jobs;

use App\Models\NotificationTemplate;
use App\Notifications\EmailNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EmailNotificationSendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $data;

    public Model $notifiable;

    public NotificationTemplate $notificationTemplate;

    public ?array $files = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data, Model $notifiable, NotificationTemplate $notificationTemplate, array $files = null)
    {
        $this->notifiable = $notifiable;
        $this->data = $data;
        $this->notificationTemplate = $notificationTemplate;
        $this->files = $files;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->notifiable->notify(new EmailNotification($this->data, $this->notificationTemplate, $this->files));
    }
}
