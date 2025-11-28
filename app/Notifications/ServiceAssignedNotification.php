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

    /**
     * Create a new notification instance.
     */
    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $clientName = $this->service->client->name ?? 'Cliente';
        $scheduledDate = $this->service->scheduled_date 
            ? $this->service->scheduled_date->format('d/m/Y H:i') 
            : 'Fecha no definida';
        
        return (new MailMessage)
                    ->subject('Nuevo Servicio Asignado')
                    ->line("Se te ha asignado un nuevo servicio para {$clientName} el {$scheduledDate}")
                    ->action('Ver Servicio', route('technician.service.detail', $this->service->id));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable): array
    {
        $clientName = $this->service->client->name ?? 'Cliente';
        $scheduledDate = $this->service->scheduled_date 
            ? $this->service->scheduled_date->format('d/m/Y H:i') 
            : 'Fecha no definida';
        
        $message = "Se te ha asignado un nuevo servicio para {$clientName} el {$scheduledDate}";
        
        // Si es reasignación, cambiar el mensaje
        if (request()->has('reassigned')) {
            $message = "Se te ha reasignado el servicio para {$clientName} el {$scheduledDate}";
        }
        
        return [
            'title' => request()->has('reassigned') ? 'Servicio Reasignado' : 'Nuevo Servicio Asignado',
            'message' => $message,
            'type' => 'info',
            'service_id' => $this->service->id,
            'url' => route('technician.service.detail', $this->service->id),
            'metadata' => [
                'service_type' => $this->service->serviceType->name ?? 'N/A',
                'client_name' => $clientName,
                'priority' => $this->service->priority,
            ]
        ];
    }
}

