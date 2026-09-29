<?php

namespace App\Console\Commands;

use App\Models\Property;
use App\Models\SavedSearch;
use App\Notifications\NewMatchingProperties;
use Illuminate\Console\Command;

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

            $search->user->notify(new NewMatchingProperties($matches, $search->name ?? 'Minha busca', $searchUrl));

            $search->update(['last_notified_at' => now()]);

            $notified++;
        }

        $this->info("Notificações enviadas para {$notified} busca(s) salva(s).");

        return self::SUCCESS;
    }
}
