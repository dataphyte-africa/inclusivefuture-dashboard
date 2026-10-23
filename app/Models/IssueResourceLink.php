<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class IssueResourceLink extends Model
{
    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('eim:public-records:v3'));
        static::deleted(fn () => Cache::forget('eim:public-records:v3'));
    }

    public function issueRecord(): BelongsTo
    {
        return $this->belongsTo(IssueRecord::class);
    }

    public function resourceRecord(): BelongsTo
    {
        return $this->belongsTo(ResourceRecord::class);
    }
}
