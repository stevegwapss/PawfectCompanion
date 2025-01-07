<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ApplicationApproved extends Notification
{
    use Queueable;

    protected $application;
    protected $status;

    public function __construct($application, $status = 'approved')
    {
        $this->application = $application;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $message = $this->status === 'approved' 
            ? 'Your adoption application has been approved.'
            : 'Your adoption application has been disapproved.';

        return (new MailMessage)
                    ->line($message)
                    ->action('View Application', url('/applications/' . $this->application->id))
                    ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        $message = $this->status === 'approved' 
            ? 'Your adoption application for ' . $this->application->listing->name . ' has been approved.'
            : 'Your adoption application for ' . $this->application->listing->name . ' has been disapproved.';

            $color = $this->status === 'approved' ? 'green' : 'red';
        return [
            'application_id' => $this->application->id,
            'message' => $message,
            'color' => $color,
            'date' => now()->toDateTimeString(),
        ];
    }
}