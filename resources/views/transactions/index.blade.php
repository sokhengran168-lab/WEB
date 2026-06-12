@extends('layouts.app')
@section('title', 'My Orders')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="font-game text-xl font-bold text-white tracking-wider">
                MY ORDERS
            </h1>
            <p class="text-gray-500 text-sm mt-0.5">
                Manage your purchases and sales
            </p>
        </div>
        <a href="{{ route('home') }}"
           class="text-xs text-indigo-400 hover:text-indigo-300 transition">
            ← Browse More
        </a>
    </div>

    {{-- Won Auction Alert --}}
    @php
        $wonAuctions = \App\Models\Transaction::with('listing.game')
            ->where('buyer_id', auth()->id())
            ->where('status', 'pending')
            ->whereHas('listing', fn($q) => $q->where('type', 'auction'))
            ->get();
    @endphp
    @if($wonAuctions->count() > 0)
    <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-2xl p-4 mb-5">
        <div class="flex items-center gap-2 mb-3">
            <span class="text-xl">🏆</span>
            <span class="font-game font-bold text-yellow-400 tracking-wider text-sm">
                YOU WON {{ $wonAuctions->count() }} AUCTION{{ $wonAuctions->count() > 1 ? 'S' : '' }}!
            </span>
            <span class="text-xs text-gray-500">— Complete payment to claim</span>
        </div>
        <div class="flex flex-col gap-2">
            @foreach($wonAuctions as $txn)
            <div class="flex items-center justify-between bg-gray-900/80
                        border border-yellow-500/10 rounded-xl px-4 py-3">
                <div>
                    <div class="font-semibold text-sm text-white">
                        {{ Str::limit($txn->listing->title ?? '—', 40) }}
                    </div>
                    <div class="text-xs text-gray-400">
                        Winning bid:
                        <strong class="text-yellow-400">
                            ${{ number_format($txn->amount, 2) }}
                        </strong>
                    </div>
                </div>
                <a href="{{ route('transactions.payment', $txn) }}"
                   class="bg-yellow-500 hover:bg-yellow-400 text-black font-bold
                          text-xs px-4 py-2 rounded-xl transition">
                    Pay Now →
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Tabs --}}
    <div x-data="{ tab: '{{ request('tab', 'purchases') }}' }">

        <div class="flex gap-1 bg-gray-900 border border-gray-800 rounded-2xl p-1 mb-5 w-fit">
            <button @click="tab = 'purchases'"
                    :class="tab === 'purchases'
                        ? 'bg-indigo-600 text-white shadow-lg'
                        : 'text-gray-400 hover:text-white'"
                    class="px-5 py-2 rounded-xl text-sm font-semibold transition">
                🛒 Purchases
                <span class="ml-1 text-xs opacity-60">({{ $purchases->total() }})</span>
            </button>
            <button @click="tab = 'sales'"
                    :class="tab === 'sales'
                        ? 'bg-indigo-600 text-white shadow-lg'
                        : 'text-gray-400 hover:text-white'"
                    class="px-5 py-2 rounded-xl text-sm font-semibold transition">
                💰 Sales
                <span class="ml-1 text-xs opacity-60">({{ $sales->total() }})</span>
            </button>
        </div>

        {{-- PURCHASES --}}
        <div x-show="tab === 'purchases'" x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0">

            @forelse($purchases as $txn)
            @php
                $statusMap = [
                    'pending'   => ['⏳', 'Awaiting Payment', 'text-orange-400 bg-orange-500/10 border-orange-500/20'],
                    'paid'      => ['🕐', 'Verifying',        'text-blue-400 bg-blue-500/10 border-blue-500/20'],
                    'escrow'    => ['🔒', 'In Escrow',        'text-cyan-400 bg-cyan-500/10 border-cyan-500/20'],
                    'completed' => ['✅', 'Completed',        'text-green-400 bg-green-500/10 border-green-500/20'],
                    'disputed'  => ['⚠️', 'Disputed',         'text-red-400 bg-red-500/10 border-red-500/20'],
                    'refunded'  => ['↩️', 'Refunded',         'text-yellow-400 bg-yellow-500/10 border-yellow-500/20'],
                    'cancelled' => ['✕', 'Cancelled',         'text-gray-500 bg-gray-500/10 border-gray-500/20'],
                ];
                [$sIcon, $sLabel, $sClass] = $statusMap[$txn->status]
                    ?? ['•', ucfirst($txn->status), 'text-gray-400 bg-gray-500/10 border-gray-500/20'];
            @endphp
            <div class="bg-gray-900 border border-gray-800 rounded-2xl p-4 mb-3
                        hover:border-gray-700 transition group">
                <div class="flex items-center gap-4">

                    {{-- Game image --}}
                    <div class="w-14 h-12 bg-gray-800 rounded-xl overflow-hidden flex-shrink-0">
                        @if(($txn->listing->firstImage ?? false))
                        <img src="{{ $txn->listing->firstImage->url }}"
                             class="w-full h-full object-cover">
                        @else
                        <div class="w-full h-full flex items-center justify-center text-xl">🎮</div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-white text-sm truncate">
                            {{ $txn->listing->title ?? 'Deleted listing' }}
                        </div>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-xs text-gray-500">
                                {{ $txn->listing->game->name ?? '—' }}
                            </span>
                            <span class="text-gray-700">·</span>
                            <span class="text-xs text-gray-500">
                                {{ $txn->created_at->format('M d, Y') }}
                            </span>
                            <span class="text-gray-700">·</span>
                            <span class="font-mono text-xs text-gray-600">
                                {{ $txn->transaction_code }}
                            </span>
                        </div>
                    </div>

                    {{-- Amount --}}
                    <div class="text-right flex-shrink-0">
                        <div class="font-game font-bold text-green-400">
                            ${{ number_format($txn->amount, 2) }}
                        </div>
                        <div class="text-xs text-gray-600">
                            {{ $txn->listing->type === 'auction' ? '🏆 Auction' : '🛒 Fixed' }}
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="flex-shrink-0">
                        <span class="text-xs px-2.5 py-1 rounded-full border font-semibold {{ $sClass }}">
                            {{ $sIcon }} {{ $sLabel }}
                        </span>
                    </div>

                    {{-- Action --}}
                    <div class="flex-shrink-0">
                        @if($txn->status === 'pending')
                        <a href="{{ route('transactions.payment', $txn) }}"
                           class="text-xs bg-orange-600 hover:bg-orange-500 text-white
                                  font-bold px-3 py-2 rounded-xl transition whitespace-nowrap">
                            Pay Now →
                        </a>
                        @elseif($txn->status === 'escrow')
                        <a href="{{ route('transactions.show', $txn) }}"
                           class="text-xs bg-cyan-600/20 hover:bg-cyan-600/40 text-cyan-400
                                  border border-cyan-500/30 font-bold px-3 py-2
                                  rounded-xl transition whitespace-nowrap">
                            Confirm →
                        </a>
                        @else
                        <a href="{{ route('transactions.show', $txn) }}"
                           class="text-xs bg-gray-800 hover:bg-gray-700 text-gray-400
                                  hover:text-white px-3 py-2 rounded-xl transition
                                  group-hover:text-white whitespace-nowrap">
                            View →
                        </a>
                        @endif
                    </div>

                </div>
            </div>
            @empty
            <div class="bg-gray-900 border border-gray-800 rounded-2xl py-16 text-center">
                <div class="text-5xl mb-3 opacity-30">🛒</div>
                <div class="font-bold text-gray-500 mb-1">No purchases yet</div>
                <p class="text-xs text-gray-600 mb-4">
                    Find an account you like and make your first purchase
                </p>
                <a href="{{ route('home') }}"
                   class="inline-block bg-indigo-600 hover:bg-indigo-500 text-white
                          text-sm font-bold px-5 py-2.5 rounded-xl transition">
                    Browse Accounts
                </a>
            </div>
            @endforelse

            @if($purchases->hasPages())
            <div class="mt-4">{{ $purchases->links() }}</div>
            @endif
        </div>

        {{-- SALES --}}
        <div x-show="tab === 'sales'" x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0">

            @forelse($sales as $txn)
            @php
                $statusMap = [
                    'pending'   => ['⏳', 'Awaiting Payment', 'text-orange-400 bg-orange-500/10 border-orange-500/20'],
                    'paid'      => ['🕐', 'Verifying',        'text-blue-400 bg-blue-500/10 border-blue-500/20'],
                    'escrow'    => ['🔒', 'Deliver Account',  'text-cyan-400 bg-cyan-500/10 border-cyan-500/20'],
                    'completed' => ['✅', 'Paid Out',         'text-green-400 bg-green-500/10 border-green-500/20'],
                    'disputed'  => ['⚠️', 'Disputed',         'text-red-400 bg-red-500/10 border-red-500/20'],
                    'refunded'  => ['↩️', 'Refunded',         'text-yellow-400 bg-yellow-500/10 border-yellow-500/20'],
                    'cancelled' => ['✕', 'Cancelled',         'text-gray-500 bg-gray-500/10 border-gray-500/20'],
                ];
                [$sIcon, $sLabel, $sClass] = $statusMap[$txn->status]
                    ?? ['•', ucfirst($txn->status), 'text-gray-400 bg-gray-500/10 border-gray-500/20'];
            @endphp
            <div class="bg-gray-900 border border-gray-800 rounded-2xl p-4 mb-3
                        hover:border-gray-700 transition group">
                <div class="flex items-center gap-4">

                    {{-- Image --}}
                    <div class="w-14 h-12 bg-gray-800 rounded-xl overflow-hidden flex-shrink-0">
                        @if(($txn->listing->firstImage ?? false))
                        <img src="{{ $txn->listing->firstImage->url }}"
                             class="w-full h-full object-cover">
                        @else
                        <div class="w-full h-full flex items-center justify-center text-xl">🎮</div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-white text-sm truncate">
                            {{ $txn->listing->title ?? 'Deleted listing' }}
                        </div>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-xs text-gray-500">
                                Buyer: {{ $txn->buyer->name ?? '—' }}
                            </span>
                            <span class="text-gray-700">·</span>
                            <span class="text-xs text-gray-500">
                                {{ $txn->created_at->format('M d, Y') }}
                            </span>
                        </div>
                    </div>

                    {{-- Payout --}}
                    <div class="text-right flex-shrink-0">
                        <div class="font-game font-bold text-green-400">
                            ${{ number_format($txn->seller_payout, 2) }}
                        </div>
                        <div class="text-xs text-gray-600">your payout</div>
                    </div>

                    {{-- Status --}}
                    <div class="flex-shrink-0">
                        <span class="text-xs px-2.5 py-1 rounded-full border font-semibold {{ $sClass }}">
                            {{ $sIcon }} {{ $sLabel }}
                        </span>
                    </div>

                    {{-- Action --}}
                    <div class="flex-shrink-0">
                        @if($txn->status === 'escrow')
                        <a href="{{ route('transactions.show', $txn) }}"
                           class="text-xs bg-cyan-600/20 hover:bg-cyan-600/40 text-cyan-400
                                  border border-cyan-500/30 font-bold px-3 py-2
                                  rounded-xl transition whitespace-nowrap animate-pulse">
                            Send Account →
                        </a>
                        @else
                        <a href="{{ route('transactions.show', $txn) }}"
                           class="text-xs bg-gray-800 hover:bg-gray-700 text-gray-400
                                  hover:text-white px-3 py-2 rounded-xl transition
                                  whitespace-nowrap">
                            View →
                        </a>
                        @endif
                    </div>

                </div>

                {{-- Escrow reminder for seller --}}
                @if($txn->status === 'escrow')
                <div class="mt-3 pt-3 border-t border-gray-800 flex items-center
                            justify-between text-xs">
                    <span class="text-cyan-400">
                        💡 Payment confirmed — send account credentials to buyer now
                    </span>
                    @if($txn->review_deadline)
                    <span class="text-gray-500">
                        Deadline: {{ $txn->review_deadline->format('M d · H:i') }}
                    </span>
                    @endif
                </div>
                @endif

            </div>
            @empty
            <div class="bg-gray-900 border border-gray-800 rounded-2xl py-16 text-center">
                <div class="text-5xl mb-3 opacity-30">💰</div>
                <div class="font-bold text-gray-500 mb-1">No sales yet</div>
                <p class="text-xs text-gray-600 mb-4">
                    List your first account and start earning
                </p>
                <a href="{{ route('listings.create') }}"
                   class="inline-block bg-indigo-600 hover:bg-indigo-500 text-white
                          text-sm font-bold px-5 py-2.5 rounded-xl transition">
                    + Sell Account
                </a>
            </div>
            @endforelse

            @if($sales->hasPages())
            <div class="mt-4">{{ $sales->links() }}</div>
            @endif
        </div>

    </div>
</div>
@endsection
