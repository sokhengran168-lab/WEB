<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ListingImage extends Model
{
    protected $fillable = [
        'listing_id',
        'image_path',
        'is_proof',
        'sort_order',
    ];

    protected $casts = [
        'is_proof' => 'boolean',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    // Smart URL — works for both Cloudinary and local storage
    public function getUrlAttribute(): string
    {
        // Already a full Cloudinary/external URL
        if (str_starts_with($this->image_path, 'http')) {
            return $this->image_path;
        }

        // Fallback to local storage
        return Storage::disk('public')->url($this->image_path);
    }
}
