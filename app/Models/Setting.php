<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $guarded = [];

    public static function current(): self
    {
        return Cache::rememberForever('app_settings', function () {
            return static::query()->firstOrCreate(['id' => 1], [
                'site_name' => config('app.name'),
            ]);
        });
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('app_settings'));
    }

    public function whatsappLink(?string $message = null): ?string
    {
        if (! $this->whatsapp_number) {
            return null;
        }

        $number = preg_replace('/\D/', '', $this->whatsapp_number);
        $text = $message ? '?text='.urlencode($message) : '';

        return "https://wa.me/{$number}{$text}";
    }
}
