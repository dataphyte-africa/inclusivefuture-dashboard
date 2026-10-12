<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class InterventionRecord extends Model
{
    protected $guarded = ['id'];
    protected function casts(): array { return ['published_at' => 'datetime']; }
    protected static function booted(): void { static::saved(fn () => Cache::forget('eim:public-records:v2')); static::deleted(fn () => Cache::forget('eim:public-records:v2')); }
}
