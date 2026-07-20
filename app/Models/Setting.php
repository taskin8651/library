<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'site_name', 'logo', 'favicon',
        'meta_title', 'meta_description', 'meta_keywords', 'og_image',
        'contact_email', 'contact_phone',
        'facebook_url', 'twitter_url', 'instagram_url', 'linkedin_url', 'youtube_url',
    ];

    // Single-row settings table — always id=1. Auto-creates the row with
    // column defaults the first time anything asks for it, so callers never
    // have to null-check "has this been configured yet."
    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    public function getFaviconUrlAttribute(): ?string
    {
        return $this->favicon ? asset('storage/' . $this->favicon) : null;
    }

    public function getOgImageUrlAttribute(): ?string
    {
        return $this->og_image ? asset('storage/' . $this->og_image) : null;
    }
}
