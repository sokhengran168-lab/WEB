<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Game extends Model
{
    //
    protected $fillable = [
        'name',
        'slug',
        'category',
        'platform',
        'banner_image',
        'icon_image',
        'rank_options',
        'server_options',
        'is_active',
    ];

    protected $casts = [
        'rank_options' => 'array',
        'server_options' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($game) {
            $slug = Str::slug($game->name);

            $count = Game::where('slug', 'LIKE', "$slug%")->count();

            $game->slug = $count ? "{$slug}-{$count}" : $slug;
        });
    }

    // One game has many listings
    public function listings()
    {
        return $this->hasMany(Listing::class);
    }
}
