<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmail extends Notification
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
         $token = $notifiable->email_verified_token;
     
         $url = url("api/verify-email/{$token}");
     
         return (new MailMessage)
             ->line('Cliquez sur le bouton ci-dessous pour vérifier votre adresse e-mail.')
             ->action('Vérifier mon adresse e-mail', $url)
             ->line('Si vous n\'avez pas créé de compte, vous pouvez ignorer cet e-mail.');
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
