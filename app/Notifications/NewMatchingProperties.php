<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class NewMatchingProperties extends Notification
{
    use Queueable;

    public function __construct(
        public Collection $properties,
        public string $searchName,
        public string $searchUrl,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Novos imóveis para a sua busca: '.$this->searchName)
            ->greeting('Olá, '.$notifiable->name.'!')
            ->line('Encontramos '.$this->properties->count().' novo(s) imóvel(is) que combinam com a sua busca salva "'.$this->searchName.'":');

        foreach ($this->properties->take(5) as $property) {
            $price = $property->price
                ? 'R$ '.number_format((float) $property->price, 0, ',', '.')
                : 'Consulte o valor';

            $mail->line("• {$property->title} — {$price}");
        }

        if ($this->properties->count() > 5) {
            $mail->line('...e mais '.($this->properties->count() - 5).' imóvel(is).');
        }

        return $mail->action('Ver todos os resultados', $this->searchUrl)
            ->line('Você recebe este e-mail porque salvou esta busca no site.');
    }
}
