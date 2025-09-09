<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Url extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_url',
        'short_code',
        'title',
        'clicks',
        'user_id',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'clicks' => 'integer'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function incrementClicks(): void
    {
        $this->increment('clicks');
    }

    public function getShortUrl(): string
    {
        return url('/') . '/' . $this->short_code;
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
