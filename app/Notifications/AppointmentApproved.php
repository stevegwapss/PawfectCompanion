<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AppointmentApproved extends Notification
{
    use Queueable;

    protected $appointment;
    protected $status;

    public function __construct($appointment, $status = 'approved')
    {
        $this->appointment = $appointment;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $message = $this->status === 'approved' 
            ? 'Your appointment has been approved.'
            : 'Your appointment has been disapproved.';

        return (new MailMessage)
                    ->line($message)
                    ->action('View Appointment', url('/appointments/' . $this->appointment->id))
                    ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
{
    $message = $this->status === 'approved' 
        ? 'Your appointment for ' . $this->appointment->adoptionApplication->listing->name . ' has been approved.'
        : 'Your appointment for ' . $this->appointment->adoptionApplication->listing->name . ' has been disapproved.';

    $color = $this->status === 'approved' ? 'green' : 'red';
    return [
        'appointment_id' => $this->appointment->id,
        'application_id' => $this->appointment->adoptionApplication->id, // Add this line
        'message' => $message,
        'color' => $color,
        'date' => now()->toDateTimeString(),
    ];
}
}