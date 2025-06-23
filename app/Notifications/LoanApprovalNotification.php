<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoanApprovalNotification extends Notification
{
    use Queueable;

    protected $loan;
    protected $type;
    protected $message;

    public function __construct($loan, $type)
    {
        $this->loan = $loan;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        switch ($this->type) {
            case 'pending_ceo_approval':
                $this->message = "Loan has been approved by the loan manager and is pending your approval.";
                return (new MailMessage)
                    ->line($this->message)
                    ->action('Review Loan', url('/loan/' . $this->loan->id . '/show'))
                    ->line('Thank you for using our application!');
                
            case 'approved':
                $this->message = "Your loan has been approved by the CEO. View it here or in your Mail.";
                return (new MailMessage)
                    ->line($this->message)
                    ->action('View Loan', url('/loan/' . $this->loan->id . '/show'))
                    ->line('Thank you for using our application!');

            default:
                $this->message = "There has been an update to your loan.";
                return (new MailMessage)
                    ->line($this->message)
                    ->action('View Loan', url('/loan/' . $this->loan->id . '/show'))
                    ->line('Thank you for using our application!');
        }
    }

    public function toArray($notifiable)
    {
        return [
            'loan_id' => $this->loan->id,
            'type' => $this->type,
            'message' => $this->message
        ];
    }
}
