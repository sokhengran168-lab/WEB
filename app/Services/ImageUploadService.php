<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class ImageUploadService
{
    public function upload(UploadedFile $file, string $folder = 'listings'): string
    {
        // Use Cloudinary if configured
        if (config('cloudinary.cloud_url')) {

            $cloud = app(\App\Services\CloudinaryService::class);

            $result = $cloud->upload($file->getRealPath(), $folder);

            return $result['secure_url'];
        }

        // Fallback to local storage
        return $file->store($folder, 'public');
    }

    public function delete(string $path): void
    {
        if (str_starts_with($path, 'http') && config('cloudinary.cloud_url')) {
            // Extract public_id from Cloudinary URL
            $publicId = $this->extractPublicId($path);
            if ($publicId) {
                cloudinary()->destroy($publicId);
            }
            return;
        }

        // Local storage delete
        \Storage::disk('public')->delete($path);
    }

    private function extractPublicId(string $url): ?string
    {
        // Extract public_id from Cloudinary URL
        // e.g. https://res.cloudinary.com/cloud/image/upload/v123/gametradehub/listings/abc.jpg
        preg_match('/upload\/(?:v\d+\/)?(.+?)(?:\.\w+)?$/', $url, $matches);
        return $matches[1] ?? null;
    }
}
