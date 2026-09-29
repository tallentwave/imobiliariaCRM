<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Document extends Model
{
    protected $guarded = [];

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function downloadUrl(): string
    {
        return route('admin.documents.download', $this);
    }

    public function sizeForHumans(): string
    {
        $bytes = \Illuminate\Support\Facades\Storage::disk('local')->exists($this->path)
            ? \Illuminate\Support\Facades\Storage::disk('local')->size($this->path)
            : 0;

        return match (true) {
            $bytes >= 1048576 => round($bytes / 1048576, 1).' MB',
            $bytes >= 1024 => round($bytes / 1024, 1).' KB',
            default => $bytes.' B',
        };
    }
}
