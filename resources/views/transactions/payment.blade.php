@extends('layouts.app')
@section('title', 'Complete Payment')

@section('content')
<div class="max-w-lg mx-auto px-4 py-8">

    {{-- Header --}}
    <div class="text-center mb-6">
        <div class="text-4xl mb-2">🏦</div>
        <h1 class="font-game text-xl font-bold text-white tracking-wider mb-1">
            COMPLETE PAYMENT
        </h1>
        <p class="text-xs text-gray-500">
            Transfer the exact amount to secure your order
        </p>
    </div>

    {{-- Order Summary --}}
    <div class="bg-gray-900 border border-gray-800 rounded-2xl p-4 mb-4">
        <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">
            Order Summary
        </div>
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-10 bg-gray-800 rounded-xl flex items-center
                        justify-center text-xl flex-shrink-0">🎮</div>
            <div class="flex-1 min-w-0">
                <div class="font-semibold text-sm text-white truncate">
                    {{ $transaction->listing->title }}
                </div>
                <div class="text-xs text-gray-400">
                    {{ $transaction->listing->game->name }}
                    · Seller: {{ $transaction->seller->name }}
                </div>
            </div>
        </div>
        <div class="bg-gray-800 rounded-xl p-3 flex justify-between items-center">
            <span class="text-sm text-gray-400">Total to pay</span>
            <span class="font-game font-bold text-2xl text-green-400">
                ${{ number_format($transaction->amount, 2) }}
            </span>
        </div>
    </div>

    {{-- Bank Details --}}
    <div class="bg-gray-900 border border-indigo-500/30 rounded-2xl p-4 mb-4"
         style="background: linear-gradient(135deg, rgba(99,102,241,0.05), rgba(10,10,20,0.9))">

        <div class="text-xs font-bold text-indigo-400 uppercase tracking-wider mb-4">
            💳 Bank Transfer Details
        </div>

        <div class="flex flex-col gap-3">
            @foreach([
                ['Bank Name',       $bank['bank_name']],
                ['Account Name',    $bank['bank_account_name']],
                ['Account Number',  $bank['bank_account_number']],
                ['Swift/BIC Code',  $bank['bank_swift']],
            ] as [$label, $value])
            <div class="flex items-center justify-between bg-gray-800/50
                        rounded-xl px-3 py-2.5">
                <span class="text-xs text-gray-500">{{ $label }}</span>
                <div class="flex items-center gap-2">
                    <span class="font-bold text-sm text-white">{{ $value }}</span>
                    <button onclick="copy('{{ $value }}')"
                            class="text-xs text-indigo-400 hover:text-indigo-300 transition">
                        📋
                    </button>
                </div>
            </div>
            @endforeach

            {{-- Reference Code --}}
            <div class="bg-yellow-500/10 border border-yellow-500/30
                        rounded-xl px-3 py-2.5">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs text-yellow-400/70 mb-0.5">
                            ⚠️ Payment Reference (REQUIRED)
                        </div>
                        <div class="font-game font-bold text-yellow-400 text-lg tracking-wider">
                            {{ $transaction->transaction_code }}
                        </div>
                    </div>
                    <button onclick="copy('{{ $transaction->transaction_code }}')"
                            class="text-xs text-yellow-400 hover:text-yellow-300 transition">
                        📋 Copy
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    You MUST include this reference when transferring.
                    Without it we cannot match your payment.
                </p>
            </div>
        </div>
    </div>

    {{-- Steps --}}
    <div class="bg-gray-900 border border-gray-800 rounded-2xl p-4 mb-4">
        <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">
            How to Complete
        </div>
        <div class="flex flex-col gap-3">
            @foreach([
                ['1', 'Open your banking app or go to your bank'],
                ['2', 'Transfer <strong class="text-green-400">${{ number_format($transaction->amount, 2) }}</strong> to the account above'],
                ['3', 'Use <strong class="text-yellow-400">{{ $transaction->transaction_code }}</strong> as payment reference'],
                ['4', 'Click "I Have Paid" below'],
                ['5', 'Admin verifies within 1-3 hours → account details sent'],
            ] as [$num, $text])
            <div class="flex items-start gap-3">
                <span class="w-5 h-5 bg-indigo-600 rounded-full flex items-center
                             justify-center text-white text-xs font-black flex-shrink-0 mt-0.5">
                    {{ $num }}
                </span>
                <span class="text-sm text-gray-300">{!! $text !!}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- I Have Paid Form --}}
    <form method="POST" action="{{ route('transactions.paid', $transaction) }}"
          class="mb-3">
        @csrf

        <div class="mb-3">
            <label class="block text-xs font-semibold text-gray-400 mb-1.5">
                Payment Note (Optional)
            </label>
            <input type="text" name="payment_note"
                   placeholder="e.g. Paid via Maybank2u at 3:00 PM"
                   class="w-full bg-gray-800 border border-gray-700 rounded-xl
                          px-3 py-2.5 text-sm text-white placeholder-gray-600
                          focus:outline-none focus:border-indigo-500">
        </div>

        <button type="submit"
                class="w-full font-game font-bold text-xs tracking-wider
                       bg-green-600 hover:bg-green-500 text-white
                       py-3.5 rounded-xl transition"
                style="box-shadow: 0 0 20px rgba(34,197,94,0.3)"
                onclick="return confirm('Confirm that you have transferred ${{ number_format($transaction->amount, 2) }}?')">
            ✅ I HAVE PAID — NOTIFY ADMIN
        </button>

    </form>

    {{-- Cancel --}}
    <form method="POST" action="{{ route('transactions.cancel', $transaction) }}"
          class="text-center">
        @csrf
        <button type="submit"
                class="text-xs text-gray-600 hover:text-red-400 transition"
                onclick="return confirm('Cancel this order? The listing will become available again.')">
            ✕ Cancel Order
        </button>
    </form>

    {{-- Timer --}}
    <div class="text-center mt-4">
        <p class="text-xs text-gray-600">
            ⏰ This order is reserved for
            <span class="text-yellow-400 font-bold" id="timer">24:00:00</span>
            — please complete payment before it expires.
        </p>
    </div>

</div>
@endsection

@push('scripts')
<script>
function copy(text) {
    navigator.clipboard.writeText(text).then(() => {
        const btn = event.target;
        const orig = btn.textContent;
        btn.textContent = '✅';
        setTimeout(() => btn.textContent = orig, 1500);
    });
}

// 24 hour countdown from transaction creation
const created = new Date('{{ $transaction->created_at->toISOString() }}');
const expires = new Date(created.getTime() + 24 * 60 * 60 * 1000);

function updateTimer() {
    const now  = new Date();
    const diff = expires - now;
    if (diff <= 0) {
        document.getElementById('timer').textContent = 'EXPIRED';
        return;
    }
    const h = Math.floor(diff / 3600000);
    const m = Math.floor((diff % 3600000) / 60000);
    const s = Math.floor((diff % 60000) / 1000);
    document.getElementById('timer').textContent =
        `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
}
updateTimer();
setInterval(updateTimer, 1000);
</script>
@endpush
