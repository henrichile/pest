<?php

namespace App\Notifications;

use App\Models\Service;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServiceAssignedNotification extends Notification
{
    use Queueable;

    protected $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'service_id' => $this->service->id,
            'client_name' => $this->service->client->name,
            'service_type' => $this->service->service_type,
            'title' => 'Nuevo Servicio Asignado',
            'message' => 'Se te ha asignado un nuevo servicio: ' . $this->service->client->name,
            'type' => 'info',
        ];
    }
}
