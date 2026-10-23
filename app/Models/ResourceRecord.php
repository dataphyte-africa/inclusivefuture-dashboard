<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class ResourceRecord extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'allocation_year' => 'integer', 'published_at' => 'datetime'];
    }

    public function issueLinks(): HasMany
    {
        return $this->hasMany(IssueResourceLink::class);
    }

    protected static function booted(): void
    {
        static::saved(fn () => self::clearPublicRecordCache());
        static::deleted(fn () => self::clearPublicRecordCache());
    }

    private static function clearPublicRecordCache(): void
    {
        Cache::forget('eim:public-records:v2');
        Cache::forget('eim:public-records:v3');
    }
}
