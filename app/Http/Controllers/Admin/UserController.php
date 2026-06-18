<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::withCount(['listings', 'purchases', 'sales'])
            ->withSum(['sales as total_earned' => function ($q) {
                $q->where('status', 'completed');
            }], 'seller_payout')

            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%');
                });
            })

            ->when($request->filter === 'banned',
                fn($q) => $q->where('is_banned', true)
            )

            ->when($request->filter === 'verified',
                fn($q) => $q->where('is_verified', true)
            )

            // Sorting
            ->when($request->sort === 'wallet',
                fn($q) => $q->orderByDesc('wallet_balance')
            )

            ->when($request->sort === 'earned',
                fn($q) => $q->orderByDesc('total_earned')
            )

            // Default order (only if no sort)
            ->when(!$request->sort, fn($q) => $q->latest())

            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function ban(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot ban yourself.');
        }

        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot ban an admin.');
        }

        $user->update(['is_banned' => true]);

        return back()->with('success', $user->name . ' has been banned.');
    }

    public function unban(User $user)
    {
        $user->update(['is_banned' => false]);
        return back()->with('success', $user->name . ' has been unbanned.');
    }

    public function verify(User $user)
    {
        if ($user->is_verified) {
            return back()->with('info', $user->name . ' is already verified.');
        }

        $user->update(['is_verified' => true]);

        return back()->with('success', $user->name . ' is now verified.');
    }

    public function unverify(User $user)
    {
        if (!$user->is_verified) {
            return back()->with('info', $user->name . ' is already unverified.');
        }

        $user->update(['is_verified' => false]);

        return back()->with('success', $user->name . ' has been unverified.');
    }

}
