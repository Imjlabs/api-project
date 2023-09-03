<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserDeletedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $userName = $notifiable->first_name . $notifiable->name; // Remplacez par le nom de l'utilisateur supprimé
        $fileCount = $notifiable->files()->count(); // Comptez les fichiers supprimés

        return (new MailMessage)
            ->line('Un utilisateur a supprimé son compte :')
            ->line('Nom du client : ' . $userName)
            ->line('Nombre de fichiers supprimés : ' . $fileCount);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
