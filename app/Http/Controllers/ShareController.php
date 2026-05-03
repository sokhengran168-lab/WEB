<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;

class ShareController extends Controller
{
    public function share(Request $request, Listing $listing){
        $request->validate([
            'platform'=> 'required|in:whatsapp,telegram,twitter,copy',
        ]);

        // Increment share count
        $listing->incrementShares();

        $url = route($listing->isAuction() ? 'auctions.show' : 'listings.show', $listing);
        $title = $listing->title;
        $message = "Check out this game account: {title} - \${$listing->price}\n{$url}";

        $shareUrl = match($request->platform) {
            'whatsapp' => 'https://wa.me/?text=' . urlencode($message),
            'telegram'  => 'https://t.me/share/url?url=' . urlencode($url) . '&text=' . urlencode($title),
            'facebook'  => 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($url),
            'twitter'   => 'https://twitter.com/intent/tweet?text=' . urlencode($title) . '&url=' . urlencode($url),
            'copy'      => null,
        };

        if ($request->platform === 'copy'){
            return response()->json([
                'url' => $url,
                'message' => 'Link copied'
            ]);
        }
        return response()->json(['url' => $shareUrl]);
    }
}
