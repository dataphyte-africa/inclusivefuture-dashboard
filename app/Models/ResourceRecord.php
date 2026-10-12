<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ResourceRecord extends Model
{
    protected $guarded = ['id'];
    protected function casts(): array { return ['amount' => 'decimal:2', 'allocation_year' => 'integer', 'published_at' => 'datetime']; }
    protected static function booted(): void { static::saved(fn () => Cache::forget('eim:public-records:v2')); static::deleted(fn () => Cache::forget('eim:public-records:v2')); }
}
