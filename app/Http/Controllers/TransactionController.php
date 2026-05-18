<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Transaction;
use App\Services\EscrowService;
use App\Services\WalletService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(
        private WalletService $walletService,
        private EscrowService $escrowService,
    ) {}

    // Show all purchases and sales
    public function index()
    {
        $purchases = Transaction::with(['listing', 'seller'])
            ->where('buyer_id', auth()->id())
            ->latest()
            ->paginate(10, ['*'], 'purchases_page');

        $sales = Transaction::with(['listing', 'buyer'])
            ->where('seller_id', auth()->id())
            ->latest()
            ->paginate(10, ['*'], 'sales_page');

        return view('transactions.index', compact('purchases', 'sales'));
    }

    // Show single transaction detail
    public function show(Transaction $transaction)
    {
        if (
            $transaction->buyer_id  !== auth()->id() &&
            $transaction->seller_id !== auth()->id()
        ) {
            abort(403);
        }

        $transaction->load(['listing', 'buyer', 'seller', 'escrowLog']);
        return view('transactions.show', compact('transaction'));
    }

    // Buyer clicks "Buy Now" — now redirects to checkout
    // kept for wallet payment method only
    public function store(Request $request)
    {
        $request->validate([
            'listing_id' => 'required|exists:listings,id',
        ]);

        $listing = Listing::findOrFail($request->listing_id);

        if ($listing->status !== 'active') {
            return back()->with('error', 'This listing is no longer available.');
        }

        if ($listing->user_id === auth()->id()) {
            return back()->with('error', 'You cannot buy your own listing.');
        }

        // Check for existing transaction
        $existing = Transaction::where('listing_id', $listing->id)
            ->where('buyer_id', auth()->id())
            ->whereIn('status', ['pending', 'escrow'])
            ->first();

        if ($existing) {
            return redirect()->route('transactions.show', $existing)
                ->with('error', 'You already have an active transaction for this listing.');
        }

        $fee    = round($listing->price * 0.05, 2);
        $payout = round($listing->price - $fee, 2);

        // Credit wallet first (wallet pay method), then hold in escrow
        $this->walletService->credit(
            userId:      auth()->id(),
            amount:      $listing->price,
            type:        'card_payment',
            description: 'Payment for: ' . $listing->title
        );

        $transaction = Transaction::create([
            'transaction_code' => Transaction::generateCode(),
            'listing_id'       => $listing->id,
            'buyer_id'         => auth()->id(),
            'seller_id'        => $listing->user_id,
            'amount'           => $listing->price,
            'platform_fee'     => $fee,
            'seller_payout'    => $payout,
            'payment_method'   => 'wallet',
        ]);

        $this->escrowService->hold($transaction);

        return redirect()
            ->route('transactions.show', $transaction)
            ->with('success', 'Purchase successful! Funds are held in escrow.');
    }

    // Buyer confirms receipt
    public function confirm(Transaction $transaction)
    {
        if ($transaction->buyer_id !== auth()->id()) {
            abort(403);
        }

        if ($transaction->status !== 'escrow') {
            return back()->with('error', 'This transaction cannot be confirmed.');
        }

        $this->escrowService->release($transaction, 'buyer');

        return redirect()
            ->route('transactions.show', $transaction)
            ->with('success', 'Payment released to seller. Thank you!');
    }

    // Buyer raises dispute
    public function dispute(Request $request, Transaction $transaction)
    {
        if ($transaction->buyer_id !== auth()->id()) {
            abort(403);
        }

        if ($transaction->status !== 'escrow') {
            return back()->with('error', 'Cannot raise dispute on this transaction.');
        }

        $transaction->update(['status' => 'disputed']);

        return redirect()
            ->route('transactions.show', $transaction)
            ->with('success', 'Dispute raised. Admin will review within 24-48 hours.');
    }
}