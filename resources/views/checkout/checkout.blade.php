@extends('layouts.app')
@section('title', 'Secure Checkout')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-10">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ url()->previous() }}"
           class="w-9 h-9 bg-gray-800 hover:bg-gray-700 rounded-full flex items-center
                  justify-center text-gray-400 transition">
            ←
        </a>
        <h1 class="text-xl font-bold">Secure Checkout</h1>
        <span class="ml-auto flex items-center gap-1.5 text-xs text-green-400">
            <span class="w-2 h-2 bg-green-400 rounded-full"></span>
            SSL Secured
        </span>
    </div>

    <div class="grid grid-cols-3 gap-6">

        {{-- LEFT — item + payment method --}}
        <div class="col-span-2 flex flex-col gap-4">

            {{-- Item card --}}
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">
                    Item
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-gray-800 rounded-xl flex items-center
                                justify-center text-3xl flex-shrink-0">🎮</div>
                    <div class="flex-1">
                        <div class="font-bold text-base">{{ $listing->title }}</div>
                        <div class="text-sm text-gray-400 mt-0.5">
                            {{ $listing->game->name }}
                            @if($listing->rank) · {{ $listing->rank }} @endif
                            @if($listing->server) · {{ $listing->server }} @endif
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ $listing->platform }}
                        </div>
                    </div>
                    <div class="text-xl font-bold text-green-400">
                        ${{ number_format($listing->price, 2) }}
                    </div>
                </div>

                {{-- Seller info --}}
                <div class="mt-4 pt-4 border-t border-gray-800 flex items-center gap-3">
                    <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center
                                justify-center text-sm font-bold flex-shrink-0">
                        {{ strtoupper(substr($listing->seller->name, 0, 1)) }}
                    </div>
                    <div>
                        <span class="text-sm font-semibold">{{ $listing->seller->name }}</span>
                        @if($listing->seller->is_verified)
                        <span class="ml-2 text-xs text-sky-400 bg-sky-500/10 border border-sky-500/20
                                     px-2 py-0.5 rounded-full">✓ Verified</span>
                        @endif
                        @if($listing->seller->rating_avg > 0)
                        <span class="ml-2 text-xs text-yellow-400">
                            ⭐ {{ number_format($listing->seller->rating_avg, 1) }}
                            ({{ $listing->seller->total_sales }} sales)
                        </span>
                        @endif
                    </div>
                    <div class="ml-auto">
                        @if($listing->seller->total_sales > 0)
                        <span class="text-xs text-green-400 bg-green-500/10 border border-green-500/20
                                     px-2 py-1 rounded-full">
                            👍 {{ number_format(($listing->seller->rating_avg / 5) * 100, 0) }}% positive
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Payment method --}}
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">
                    Payment Method
                </div>

                <div id="payment-methods" class="flex flex-col gap-3">

                    {{-- Credit Card --}}
                    <label class="flex items-center gap-4 border-2 border-indigo-500 bg-indigo-500/5
                                  rounded-xl p-4 cursor-pointer transition" id="method-card">
                        <input type="radio" name="payment_method" value="card"
                               class="hidden" checked onchange="selectMethod('card')">
                        <div class="w-5 h-5 rounded-full border-2 border-indigo-500 flex items-center
                                    justify-center flex-shrink-0" id="dot-card">
                            <div class="w-2.5 h-2.5 bg-indigo-500 rounded-full"></div>
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold text-sm">Credit / Debit Card</div>
                            <div class="text-xs text-gray-500 mt-0.5">Pay in one click next time!</div>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="bg-blue-600 text-white text-xs px-2 py-0.5 rounded font-bold">VISA</span>
                            <span class="bg-red-600 text-white text-xs px-2 py-0.5 rounded font-bold">MC</span>
                            <span class="bg-blue-500 text-white text-xs px-2 py-0.5 rounded font-bold">AMEX</span>
                        </div>
                    </label>

                    {{-- Google Pay --}}
                    <label class="flex items-center gap-4 border-2 border-gray-700 bg-gray-800/50
                                  rounded-xl p-4 cursor-pointer hover:border-gray-600 transition"
                           id="method-google">
                        <input type="radio" name="payment_method" value="google_pay"
                               class="hidden" onchange="selectMethod('google')">
                        <div class="w-5 h-5 rounded-full border-2 border-gray-600 flex items-center
                                    justify-center flex-shrink-0" id="dot-google"></div>
                        <div class="flex-1">
                            <div class="font-semibold text-sm">Google Pay</div>
                        </div>
                        <span class="text-xs bg-gray-700 text-gray-300 px-2 py-1 rounded font-bold">G Pay</span>
                    </label>

                    {{-- Wallet --}}
                    <label class="flex items-center gap-4 border-2 border-gray-700 bg-gray-800/50
                                  rounded-xl p-4 cursor-pointer hover:border-gray-600 transition"
                           id="method-wallet">
                        <input type="radio" name="payment_method" value="wallet"
                               class="hidden" onchange="selectMethod('wallet')">
                        <div class="w-5 h-5 rounded-full border-2 border-gray-600 flex items-center
                                    justify-center flex-shrink-0" id="dot-wallet"></div>
                        <div class="flex-1">
                            <div class="font-semibold text-sm">💰 Wallet Balance</div>
                            <div class="text-xs text-gray-500 mt-0.5">
                                Available: ${{ number_format(auth()->user()->wallet_balance, 2) }}
                                @if(auth()->user()->wallet_balance < $listing->price)
                                <span class="text-red-400 ml-1">(Insufficient)</span>
                                @endif
                            </div>
                        </div>
                    </label>

                </div>
            </div>

            {{-- Trust badges --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 flex items-center gap-3">
                    <span class="text-2xl">🔒</span>
                    <div>
                        <div class="text-sm font-semibold">Safe & Secure Payment</div>
                        <div class="text-xs text-gray-500">100% protected by escrow</div>
                    </div>
                </div>
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 flex items-center gap-3">
                    <span class="text-2xl">🛡️</span>
                    <div>
                        <div class="text-sm font-semibold">Account Warranty</div>
                        <div class="text-xs text-gray-500">5-day buyer protection</div>
                    </div>
                </div>
            </div>

        </div>

        {{-- RIGHT — order summary --}}
        <div>
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-5 sticky top-24">
                <div class="text-sm font-bold mb-4">Order Summary</div>

                <div class="flex justify-between text-sm mb-2">
                    <span class="text-gray-400">Order Price</span>
                    <span class="font-semibold">${{ number_format($listing->price, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm mb-4">
                    <span class="text-gray-400">Payment Fees</span>
                    <span class="text-red-400">-${{ number_format($fee, 2) }}</span>
                </div>

                <div class="border-t border-gray-800 pt-4 mb-5">
                    <div class="flex justify-between font-bold text-base">
                        <span>Total to pay:</span>
                        <span class="text-green-400">${{ number_format($listing->price, 2) }}</span>
                    </div>
                </div>

                {{-- CTA button --}}
                <div id="cta-card">
                    <a href="{{ route('checkout.card', $listing) }}"
                       class="block w-full bg-gray-900 hover:bg-gray-800 text-white text-center
                              py-3.5 rounded-xl font-bold text-sm transition border border-gray-700
                              flex items-center justify-center gap-2">
                        Enter card details →
                    </a>
                </div>

                <div id="cta-wallet" class="hidden">
                    <form method="POST" action="{{ route('transactions.store') }}">
                        @csrf
                        <input type="hidden" name="listing_id" value="{{ $listing->id }}">
                        <button type="submit"
                                class="w-full bg-indigo-600 hover:bg-indigo-500 text-white
                                       py-3.5 rounded-xl font-bold text-sm transition">
                            Pay ${{ number_format($listing->price, 2) }} with Wallet
                        </button>
                    </form>
                </div>

                <div id="cta-google" class="hidden">
                    <button class="w-full bg-white text-gray-900 py-3.5 rounded-xl font-bold
                                   text-sm transition hover:bg-gray-100">
                        Pay with G Pay
                    </button>
                </div>

                <p class="text-xs text-gray-600 text-center mt-3">
                    🔒 Secured by escrow protection
                </p>
            </div>
        </div>

    </div>
</div>

<script>
function selectMethod(method) {
    const methods = ['card', 'google', 'wallet'];
    methods.forEach(m => {
        const label = document.getElementById('method-' + m);
        const dot   = document.getElementById('dot-' + m);
        const cta   = document.getElementById('cta-' + m);
        if (m === method) {
            label.classList.add('border-indigo-500', 'bg-indigo-500/5');
            label.classList.remove('border-gray-700', 'bg-gray-800/50');
            dot.innerHTML = '<div class="w-2.5 h-2.5 bg-indigo-500 rounded-full"></div>';
            dot.classList.add('border-indigo-500');
            dot.classList.remove('border-gray-600');
            if (cta) cta.classList.remove('hidden');
        } else {
            label.classList.remove('border-indigo-500', 'bg-indigo-500/5');
            label.classList.add('border-gray-700', 'bg-gray-800/50');
            dot.innerHTML = '';
            dot.classList.remove('border-indigo-500');
            dot.classList.add('border-gray-600');
            if (cta) cta.classList.add('hidden');
        }
    });
}
</script>
@endsection