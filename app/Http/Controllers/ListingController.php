<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Listing;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ListingController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth')->only([
            'create', 'store', 'edit', 'update', 'destroy'
        ]);
    }

    // ── Browse ────────────────────────────────────────────────────────────
    public function index(Request $request)
    {

        // Add stats HERE
        $totalListings = Listing::where('status', 'active')->count();
        $totalSales    = Transaction::where('status', 'completed')->count();
        $totalSellers  = User::where('total_sales', '>', 0)->count();

        $games = Game::where('is_active', true)
            ->withCount(['listings' => fn($q) => $q->where('status', 'active')])
            ->get();

        $auctionCount = Listing::where('type', 'auction')->where('status', 'active')->count();

        $featured = Listing::with(['game', 'seller', 'firstImage'])
            ->where('status', 'active')
            ->where('type', 'fixed')
            ->where('is_featured', true)
            ->latest()
            ->take(6)
            ->get();

        $liveAuctions = Listing::with(['game', 'seller', 'firstImage', 'highestBidder'])
            ->where('status', 'active')
            ->where('type', 'auction')
            ->where('auction_ends_at', '>', now())
            ->latest()
            ->take(4)
            ->get();

        $listings = Listing::with(['game', 'seller', 'firstImage'])
            ->where('type', 'fixed')
            ->where(function ($query) {
                $query->where('status', 'active');
                if (Auth::check()) {
                    $query->orWhere(function ($query) {
                        $query->where('user_id', Auth::id())
                              ->whereIn('status', ['pending', 'rejected']);
                    });
                }
            })
            ->when($request->search,    fn($q) => $q->where('title', 'like', '%' . $request->search . '%'))
            ->when($request->game_id,   fn($q) => $q->where('game_id', $request->game_id))
            ->when($request->platform,  fn($q) => $q->where('platform', $request->platform))
            ->when($request->min_price, fn($q) => $q->where('price', '>=', $request->min_price))
            ->when($request->max_price, fn($q) => $q->where('price', '<=', $request->max_price))
            ->when($request->sort === 'price_asc',  fn($q) => $q->orderBy('price', 'asc'))
            ->when($request->sort === 'price_desc', fn($q) => $q->orderBy('price', 'desc'))
            ->when($request->sort === 'popular',    fn($q) => $q->orderBy('views_count', 'desc'))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        if (request()->ajax()) {
            return view('partials.listings-grid', compact('listings'));
        }

        return view('listings.index', compact(
            'listings', 'games', 'featured', 'liveAuctions', 'auctionCount',
            // ADD THESE
            'totalListings',
            'totalSales',
            'totalSellers'
        ));
    }

    // ── Show ──────────────────────────────────────────────────────────────
    public function show(Listing $listing)
    {
        if ($listing->status === 'rejected') {
            abort(404);
        }

        if ($listing->status === 'active') {
            $listing->incrementViews();
        }

        $listing->load(['game', 'seller', 'images']);

        $related = Listing::active()
            ->where('game_id', $listing->game_id)
            ->where('id', '!=', $listing->id)
            ->take(4)
            ->get();

        return view('listings.show', compact('listing', 'related'));
    }

    // ── Create ────────────────────────────────────────────────────────────
    public function create()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $games = Game::where('is_active', true)->get();

        $gamesData = $games->map(fn($game) => [
            'id'      => $game->id,
            'name'    => $game->name,
            'ranks'   => $game->rank_options ?? [],
            'servers' => $game->server_options ?? [],
        ])->values(); // ← add this

        if (!Auth::user()->hasCompletedOnboarding()) {
            session()->flash('warning', 'Complete seller onboarding to publish, or continue to create a draft.');
        }

        return view('listings.create', compact('games', 'gamesData'));
    }

    // ── Store ─────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        // Helpful debug logging when files aren't arriving on the server.
        // Common causes: php.ini `post_max_size` / `upload_max_filesize`, webserver limits
        // (nginx `client_max_body_size`), or missing multipart `enctype` on the form.
        try {
            $files = $request->allFiles();
            Log::info('Listing upload debug', [
                'content_length' => $request->server('CONTENT_LENGTH'),
                'files_count'    => count($files),
                'files_keys'     => array_keys($files),
            ]);

            // If the client sent a non-empty body but PHP provided no files, surface a clearer error.
            if (count($files) === 0 && (int) $request->server('CONTENT_LENGTH') > 0) {
                return back()->withErrors(['images' => 'No files were uploaded. Possible server limit (post_max_size / upload_max_filesize) or request body truncated. Check PHP and webserver settings.'])->withInput();
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to log upload debug: ' . $e->getMessage());
        }

        $validated = $request->validate([
            'game_id'           => 'required|exists:games,id',
            'description'       => 'required|string',
            'title'             => 'required|string|max:255',
            'price'             => 'required|numeric|min:1',
            'rank'              => 'nullable|string|max:100',
            'level'             => 'nullable|integer|min:1',
            'platform'          => 'required|in:Mobile,PC,Console',
            'contact_whatsapp'  => 'nullable|string|max:20',
            'contact_discord'   => 'nullable|string|max:100',
            'seller_phone'      => 'nullable|string|max:20',
            'seller_country'    => 'nullable|string|max:10',
            'stock_source'      => 'nullable|in:self_farmed,resell,gifted,other',
            'stock_source_note' => 'nullable|string|max:500',
            'images'            => 'required|array|min:1',
            'images.*'          => 'image|mimes:jpg,jpeg,png,webp|max:10240',
            'contact_telegram'  => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    if (preg_match('/(?:https?:\/\/)?(?:t\.me|telegram\.me)\/([^\s\/?#]+)/i', $value, $matches)) {
                        $username = $matches[1];
                    } else {
                        $username = ltrim($value, '@');
                    }
                    if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9_]{3,30}[a-zA-Z0-9]$/', $username)) {
                        $fail('Telegram username must be 5–32 characters, letters, numbers and underscores only.');
                    }
                },
            ],
        ]);

        // Normalise Telegram to plain username
        if (!empty($validated['contact_telegram'])) {
            $telegram = $validated['contact_telegram'];
            if (preg_match('/(?:https?:\/\/)?(?:www\.)?(?:t\.me|telegram\.me)\/([^\s\/?#]+)/i', $telegram, $matches)) {
                $telegram = $matches[1];
            }
            $telegram = preg_replace('/^[@#]/', '', $telegram);
            $telegram = preg_replace('/[^\w.-]/', '', $telegram);
            $validated['contact_telegram'] = $telegram ?: $validated['contact_telegram'];
        }

        // Auto-flag checks
        $isFlagged  = false;
        $flagReason = null;

        if (auth()->user()->created_at->diffInHours(now()) < 24) {
            $isFlagged  = true;
            $flagReason = 'New account — registered less than 24 hours ago';
        }
        if ($validated['price'] < 3) {
            $isFlagged  = true;
            $flagReason = 'Suspicious price — under $3';
        }
        foreach (['hack', 'cheat', 'stolen', 'illegal', 'fake'] as $word) {
            if (stripos($validated['title'], $word) !== false) {
                $isFlagged  = true;
                $flagReason = 'Suspicious keyword in title: ' . $word;
                break;
            }
        }

        $listing = Listing::create([
            ...$validated,
            'user_id'     => auth()->id(),
            'status'      => 'active',
            'type'        => 'fixed',
            'is_flagged'  => $isFlagged,
            'flag_reason' => $flagReason,
            'flagged_at'  => $isFlagged ? now() : null,
        ]);

        foreach ($request->file('images', []) as $index => $image) {
            $path = $this->uploadImage($image, $listing->id);
            $listing->images()->create([
                'image_path' => $path,
                'is_proof'   => true,
                'sort_order' => $index,
            ]);
        }

        $message = $isFlagged
            ? 'Listing is live! Note: it has been flagged for admin review.'
            : '🎉 Listing is live! Buyers can now find your account.';

        return redirect()->route('dashboard')->with('success', $message);
    }

    // ── Edit ──────────────────────────────────────────────────────────────
    public function edit(Listing $listing)
    {
        $this->authorize('update', $listing);

        $games = Game::where('is_active', true)->get();

        if ($listing->isAuction()) {
            return view('auctions.edit', compact('listing', 'games'));
        }

        $gamesData = $games->map(fn($game) => [
            'id'      => $game->id,
            'name'    => $game->name,
            'ranks'   => $game->rank_options   ?? [],
            'servers' => $game->server_options ?? [],
        ])->values();

        return view('listings.edit', compact('listing', 'games', 'gamesData'));
    }
    // ── Update ────────────────────────────────────────────────────────────
    public function update(Request $request, Listing $listing)
    {
        $this->authorize('update', $listing);

        if ($listing->isAuction()) {
            $validated = $request->validate([
                'game_id'          => 'required|exists:games,id',
                'title'            => 'required|string|max:255',
                'starting_price'   => 'required|numeric|min:1',
                'bid_increment'    => 'required|numeric|min:0.5',
                'auction_ends_at'  => 'required|date|after:now',
                'rank'             => 'nullable|string|max:100',
                'level'            => 'nullable|integer|min:1',
                'platform'         => 'required|in:Mobile,PC,Console',
            ]);

            $listing->update([
                ...$validated,
                'price'  => $validated['starting_price'],
                'status' => 'active',
            ]);
        } else {
            $validated = $request->validate([
                'game_id'          => 'required|exists:games,id',
                'title'            => 'required|string|max:255',
                'price'            => 'required|numeric|min:1',
                'rank'             => 'nullable|string|max:100',
                'level'            => 'nullable|integer|min:1',
                'platform'         => 'required|in:Mobile,PC,Console',
                'description'      => 'nullable|string',
                'contact_telegram' => 'nullable|string|max:100',
                'contact_whatsapp' => 'nullable|string|max:20',
                'contact_discord'  => 'nullable|string|max:100',
                'seller_phone'     => 'nullable|string|max:20',
                // image validation — nullable because keeping existing is valid
                'images'           => 'nullable|array|max:8',
                'images.*'         => 'image|mimes:jpg,jpeg,png,webp|max:10240',
                'delete_images'    => 'nullable|array',
                'delete_images.*'  => 'integer|exists:listing_images,id',
            ]);

            $listing->update([
                ...$validated,
                'status' => 'active',
            ]);
        }

        // ── Delete selected existing images ───────────────────────────────
        if ($request->filled('delete_images')) {
            foreach ($request->delete_images as $imageId) {
                $image = $listing->images()->find($imageId);
                if (!$image) continue;

                $this->deleteImage($image->image_path);
                $image->delete();
            }
        }

        // ── Upload new images (same logic as store) ───────────────────────
        if ($request->hasFile('images')) {
            // Next sort_order after existing images
            $nextOrder = $listing->images()->max('sort_order') + 1;

            foreach ($request->file('images') as $index => $file) {
                $path = $this->uploadImage($file, $listing->id);
                $listing->images()->create([
                    'image_path' => $path,
                    'is_proof'   => true,
                    'sort_order' => $nextOrder + $index,
                ]);
            }
        }

        return redirect()->route('dashboard')->with('success', 'Listing updated successfully!');
    }

    // ── Destroy ───────────────────────────────────────────────────────────
    public function destroy(Listing $listing)
    {
        $this->authorize('delete', $listing);

        DB::transaction(function () use ($listing) {
            foreach ($listing->images as $image) {
                $this->deleteImage($image->image_path);
                $image->delete();
            }
            $listing->delete();
        });

        return redirect()->route('dashboard')->with('success', 'Listing deleted.');
    }

    // ── Private helpers ───────────────────────────────────────────────────

    /**
     * Upload one image to Cloudinary (if configured) or local disk.
     * Returns the stored path/URL saved to image_path.
     */
    private function uploadImage($file, int $listingId): string
    {
        if (config('filesystems.disks.cloudinary.url')) {
            $result = cloudinary()->uploadApi()->upload(
                $file->getRealPath(),
                [
                    'folder'        => 'gametradehub/listings/' . $listingId,
                    'resource_type' => 'image',
                ]
            );
            return $result['secure_url'];
        }

        // Fallback: local public disk
        return $file->store('listings/' . $listingId, 'public');
    }

    /**
     * Delete one image from Cloudinary (if configured) or local disk.
     * Accepts the value stored in image_path.
     */

    private function deleteImage(string $imagePath): void
    {
        if (str_starts_with($imagePath, 'http')) {

            if (preg_match('/upload\/(?:v\d+\/)?(.+)\.\w+$/', $imagePath, $matches)) {
                try {
                    cloudinary()->uploadApi()->destroy($matches[1]);
                } catch (\Exception $e) {
                    \Log::error('Cloudinary delete failed: ' . $e->getMessage());
                }
            } else {
                \Log::warning('Cloudinary public_id not extracted: ' . $imagePath);
            }

        } else {
            Storage::disk('public')->delete($imagePath);
        }
    }

    public function liveSearch(Request $request)
    {
        $search = trim($request->search);

        //  Fix: check AFTER assigning
        // if (!$search) {
        //     return response()->json([]);
        // }

        if (!$search || strlen($search) < 2) {
            return response()->json([]);
        }
        $listings = Listing::with('firstImage')
            ->where('status', 'active')
            ->where(function ($q) use ($search) {

                $q->where('title', 'like', "%{$search}%")
                ->orWhereHas('game', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })

                // Optional extra filters
                ->orWhere('rank', 'like', "%{$search}%")
                ->orWhere('platform', 'like', "%{$search}%");
            })
            ->limit(6)
            ->get();

        return response()->json($listings);
    }
}
