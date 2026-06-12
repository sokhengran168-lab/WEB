<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Store a new review
     */
    public function store(Request $request, Transaction $transaction)
    {
        // Only buyer can review
        if ($transaction->buyer_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Must be completed/escrow
        if (!in_array($transaction->status, ['escrow', 'completed'])) {
            return back()->with('error', 'You can only review after payment confirmation.');
        }

        // One review per transaction
        if ($transaction->hasReview()) {
            return back()->with('error', 'You have already reviewed this transaction.');
        }

        // Validate input
        $validated = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Require comment for low ratings (important UX)
        if ($validated['rating'] <= 2 && empty($validated['comment'])) {
            return back()->with('error', 'Please provide a comment for low ratings.');
        }

        //  Create review
        $review = Review::create([
            'transaction_id' => $transaction->id,
            'reviewer_id'    => auth()->id(),
            'seller_id'      => $transaction->seller_id,
            'listing_id'     => $transaction->listing_id,
            'rating'         => $validated['rating'],
            'comment'        => $validated['comment'],
        ]);

        // Recalculate seller rating
        $seller = $transaction->seller;
        $seller->recalculateRating();

        // ✅ 🔥 Auto-flag listing for low rating
        if ($validated['rating'] <= 2 && $transaction->listing) {

            $transaction->listing->update([
                'is_flagged'  => true,
                'flag_reason' => 'Low rating from buyer (' . $validated['rating'] . '⭐)',
                'flagged_at'  => now(),
            ]);
        }

        return back()->with('success', '⭐ Review submitted successfully!');
    }

    /**
     * Delete a review
     */
    public function destroy(Review $review)
    {
        //  Only owner can delete
        if ($review->reviewer_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $seller = $review->seller;

        //  Delete review
        $review->delete();

        //  Recalculate seller rating
        if ($seller) {
            $seller->recalculateRating();
        }

        return back()->with('success', 'Review deleted successfully.');
    }
}
