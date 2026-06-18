<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\EscrowService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(
        private EscrowService $escrowService,
    ) {}

    public function index(Request $request)
    {
        $transactions = Transaction::with(['listing', 'buyer', 'seller'])
            ->when(
                $request->status,
                fn ($q) => $q->where('status', $request->status),
                fn ($q) => $q->where('status', Transaction::STATUS_PAID)
            )
            ->latest()
            ->paginate(15);

        return view('admin.transactions.index', compact('transactions'));
    }

    public function confirmPayment(Transaction $transaction)
    {
        if ($transaction->status !== Transaction::STATUS_PAID) {
            return back()->with('error', 'This transaction is not awaiting payment confirmation.');
        }

        $this->escrowService->hold($transaction);

        $transaction->update([
            'admin_confirmed_at' => now(),
        ]);

        return back()->with('success', 'Payment confirmed. Escrow activated.');
    }

    public function releaseEscrow(Transaction $transaction)
    {
        if ($transaction->status === Transaction::STATUS_COMPLETED) {
                return back()->with('info', 'This transaction is already completed.');
        }

        if (!in_array($transaction->status, [
            Transaction::STATUS_ESCROW,
            Transaction::STATUS_DISPUTED
        ])) {
            return back()->with('error', 'Cannot release this transaction.');
        }

        $this->escrowService->release($transaction, 'admin');

        return back()->with('success', 'Escrow released. Seller paid.');
    }

    public function refund(Transaction $transaction)
    {

        if ($transaction->status === Transaction::STATUS_REFUNDED) {
            return back()->with('info', 'This transaction has already been refunded.');
        }

        if ($transaction->status !== Transaction::STATUS_DISPUTED) {
            return back()->with('error', 'Can only refund disputed transactions.');
        }

        $this->escrowService->refund($transaction);

        $transaction->listing->update([
            'status' => 'active'
        ]);

        return back()->with('success','Buyer refunded successfully.');
    }
}

