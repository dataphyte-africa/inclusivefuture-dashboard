<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class IssueRecord extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['value' => 'decimal:4', 'published_at' => 'datetime'];
    }

    public function interventionLinks(): HasMany
    {
        return $this->hasMany(IssueInterventionLink::class);
    }

    public function resourceLinks(): HasMany
    {
        return $this->hasMany(IssueResourceLink::class);
    }

    public function lawPolicyLinks(): HasMany
    {
        return $this->hasMany(IssueLawPolicyLink::class);
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
