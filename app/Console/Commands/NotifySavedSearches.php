<?php

namespace App\Console\Commands;

use App\Models\Property;
use App\Models\SavedSearch;
use App\Notifications\NewMatchingProperties;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class NotifySavedSearches extends Command
{
    protected $signature = 'app:notify-saved-searches';

    protected $description = 'Envia e-mail aos clientes com novos imóveis que combinam com suas buscas salvas';

    public function handle(): int
    {
        $searches = SavedSearch::query()
            ->where('notify_by_email', true)
            ->with('user')
            ->get();

        $notified = 0;

        foreach ($searches as $search) {
            if (! $search->user) {
                continue;
            }

            $since = $search->last_notified_at ?? $search->created_at;

            $matches = Property::query()
                ->published()
                ->filter($search->filters)
                ->where('published_at', '>', $since)
                ->latest('published_at')
                ->get();

            if ($matches->isEmpty()) {
                continue;
            }

            $searchUrl = route('imoveis.index', $search->filters);

            // Um e-mail que falha (SMTP fora do ar, mal configurado) nunca pode
            // impedir as demais buscas salvas de serem processadas no mesmo lote.
            try {
                $search->user->notify(new NewMatchingProperties($matches, $search->name ?? 'Minha busca', $searchUrl));
            } catch (\Throwable $e) {
                Log::warning('Falha ao notificar busca salva #'.$search->id.': '.$e->getMessage());

                continue;
            }

            $search->update(['last_notified_at' => now()]);

            $notified++;
        }

        $this->info("Notificações enviadas para {$notified} busca(s) salva(s).");

        return self::SUCCESS;
    }
}
