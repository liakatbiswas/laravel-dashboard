<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteSetting extends Model
{
    protected $fillable = [
        'site_title',
        'site_subtitle',
        'site_tagline',
        'site_description',
        'favicon_path',
        'light_logo_path',
        'dark_logo_path',
        'phone',
        'email',
        'address',
        'website',
        'updated_by',
    ];

    public static function current(): self
    {
        $setting = static::query()->find(1);

        if ($setting) {
            return $setting;
        }

        // The settings row is missing (fresh install or accidental deletion).
        // Fall back to sane defaults instead of taking every page down.
        $fallback = new static;
        $fallback->setRawAttributes(['site_title' => config('app.name', 'EFA Bangladesh')], true);
        $fallback->exists = false;

        return $fallback;
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
