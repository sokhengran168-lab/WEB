<?php

namespace App\Providers;

use App\Models\Listing;
use App\Policies\ListingPolicy;
use App\Services\AuctionService;
use App\Services\EscrowService;
use App\Services\WalletService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Services\ImageUploadService;

use Cloudinary\Configuration\Configuration;


class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ImageUploadService::class);

        $this->app->singleton(WalletService::class);

        $this->app->singleton(EscrowService::class, function ($app) {
            return new EscrowService($app->make(WalletService::class));
        });

        $this->app->singleton(AuctionService::class, function ($app) {
            return new AuctionService(
                $app->make(EscrowService::class),
            );
        });
    }

    public function boot(): void
    {
        if (config('app.env') === 'production') {
            \URL::forceScheme('https');
        }


        // ✅ FIX: Initialize Cloudinary globally
        Configuration::instance([
            'cloud' => [
                'cloud_name' => config('cloudinary.cloud_name'),
                'api_key'    => config('cloudinary.api_key'),
                'api_secret' => config('cloudinary.api_secret'),
            ],
            'url' => ['secure' => true]
        ]);


        Gate::policy(Listing::class, ListingPolicy::class);
        Paginator::useTailwind();
    }
}
