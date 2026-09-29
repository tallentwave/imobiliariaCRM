<?php

namespace App\Notifications;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewLeadReceived extends Notification
{
    use Queueable;

    public function __construct(public Lead $lead) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Novo contato recebido: '.$this->lead->name)
            ->greeting('Você recebeu um novo lead!')
            ->line('Nome: '.$this->lead->name)
            ->line('Telefone: '.($this->lead->phone ?? '—'))
            ->line('E-mail: '.($this->lead->email ?? '—'))
            ->line('Mensagem: '.($this->lead->message ?? '—'));

        if ($this->lead->property) {
            $mail->line('Imóvel: '.$this->lead->property->title)
                ->action('Ver imóvel', route('imoveis.show', $this->lead->property));
        }

        return $mail->action('Ver no CRM', route('admin.leads.index'));
    }
}
