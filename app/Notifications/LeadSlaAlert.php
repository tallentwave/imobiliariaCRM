<?php

namespace App\Notifications;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeadSlaAlert extends Notification
{
    use Queueable;

    /**
     * @param  'reminder'|'escalation'|'redistributed'  $level
     */
    public function __construct(public Lead $lead, public string $level) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $minutes = $this->lead->created_at->diffInMinutes(now());

        return match ($this->level) {
            'reminder' => (new MailMessage)
                ->subject("⏰ Lembrete: responda o lead {$this->lead->name}")
                ->greeting('Lembrete de SLA')
                ->line("O lead \"{$this->lead->name}\" está aguardando o primeiro contato há {$minutes} minuto(s).")
                ->line('Responda o quanto antes para não perder a oportunidade.')
                ->action('Abrir lead', route('admin.leads.show', $this->lead)),

            'escalation' => (new MailMessage)
                ->subject("🚨 SLA estourado: lead {$this->lead->name} sem resposta")
                ->greeting('Escalonamento de SLA')
                ->line("O lead \"{$this->lead->name}\", atribuído a ".($this->lead->agent->name ?? 'um corretor').", está sem primeira resposta há {$minutes} minuto(s).")
                ->line('Este lead foi escalonado para sua atenção como responsável.')
                ->action('Abrir lead', route('admin.leads.show', $this->lead)),

            'redistributed' => (new MailMessage)
                ->subject("Novo lead redistribuído: {$this->lead->name}")
                ->greeting('Lead redistribuído para você')
                ->line("O lead \"{$this->lead->name}\" não teve resposta a tempo pelo corretor anterior e foi redistribuído para você.")
                ->action('Abrir lead', route('admin.leads.show', $this->lead)),
        };
    }
}
