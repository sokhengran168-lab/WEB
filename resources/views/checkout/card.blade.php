@extends('layouts.app')
@section('title', 'Enter Card Details')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-10">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('checkout.show', $listing) }}"
           class="w-9 h-9 bg-gray-800 hover:bg-gray-700 rounded-full flex items-center
                  justify-center text-gray-400 transition">
            ←
        </a>
        <h1 class="text-xl font-bold">Enter Card Details</h1>
        <span class="ml-auto flex items-center gap-1.5 text-xs text-green-400">
            <span class="w-2 h-2 bg-green-400 rounded-full"></span>
            SSL Secured
        </span>
    </div>

    <div class="grid grid-cols-3 gap-6">

        {{-- LEFT — card form --}}
        <div class="col-span-2">
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">

                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-5">
                    Credit Card
                </div>

                @if ($errors->any())
                <div class="bg-red-500/10 border border-red-500/25 text-red-400 rounded-xl p-3 mb-5 text-sm">
                    {{ $errors->first() }}
                </div>
                @endif

                <form method="POST" action="{{ route('checkout.pay', $listing) }}" id="card-form">
                    @csrf

                    {{-- Card number --}}
                    <div class="mb-4">
                        <input type="text" name="card_number" placeholder="Card number"
                               maxlength="19" id="card-number"
                               class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3
                                      text-sm text-white placeholder-gray-600 focus:outline-none
                                      focus:border-indigo-500 transition"
                               value="{{ old('card_number') }}" required>
                    </div>

                    {{-- Expiry + CVV --}}
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <input type="text" name="expiry" placeholder="MM/YY"
                               maxlength="5" id="expiry"
                               class="bg-gray-800 border border-gray-700 rounded-xl px-4 py-3
                                      text-sm text-white placeholder-gray-600 focus:outline-none
                                      focus:border-indigo-500 transition"
                               value="{{ old('expiry') }}" required>
                        <input type="text" name="cvv" placeholder="CVV"
                               maxlength="4"
                               class="bg-gray-800 border border-gray-700 rounded-xl px-4 py-3
                                      text-sm text-white placeholder-gray-600 focus:outline-none
                                      focus:border-indigo-500 transition"
                               required>
                    </div>

                    {{-- Billing details --}}
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4 mt-6">
                        Billing Details
                    </div>

                    <div class="mb-3">
                        <input type="text" name="card_name" placeholder="Name on card"
                               class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3
                                      text-sm text-white placeholder-gray-600 focus:outline-none
                                      focus:border-indigo-500 transition"
                               value="{{ old('card_name', auth()->user()->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <input type="text" name="address" placeholder="Address"
                               class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3
                                      text-sm text-white placeholder-gray-600 focus:outline-none
                                      focus:border-indigo-500 transition"
                               value="{{ old('address') }}" required>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <input type="text" name="city" placeholder="City"
                               class="bg-gray-800 border border-gray-700 rounded-xl px-4 py-3
                                      text-sm text-white placeholder-gray-600 focus:outline-none
                                      focus:border-indigo-500 transition"
                               value="{{ old('city') }}" required>
                        <input type="text" name="zip" placeholder="Zip Code"
                               class="bg-gray-800 border border-gray-700 rounded-xl px-4 py-3
                                      text-sm text-white placeholder-gray-600 focus:outline-none
                                      focus:border-indigo-500 transition"
                               value="{{ old('zip') }}" required>
                    </div>

                    <div class="mb-5">
                        <select name="country"
                                class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3
                                       text-sm text-white focus:outline-none focus:border-indigo-500 transition">
                            <option value="">Country</option>
                            <option value="KH" {{ old('country') == 'KH' ? 'selected' : '' }}>Cambodia</option>
                            <option value="TH" {{ old('country') == 'TH' ? 'selected' : '' }}>Thailand</option>
                            <option value="VN" {{ old('country') == 'VN' ? 'selected' : '' }}>Vietnam</option>
                            <option value="SG" {{ old('country') == 'SG' ? 'selected' : '' }}>Singapore</option>
                            <option value="MY" {{ old('country') == 'MY' ? 'selected' : '' }}>Malaysia</option>
                            <option value="PH" {{ old('country') == 'PH' ? 'selected' : '' }}>Philippines</option>
                            <option value="ID" {{ old('country') == 'ID' ? 'selected' : '' }}>Indonesia</option>
                            <option value="US" {{ old('country') == 'US' ? 'selected' : '' }}>United States</option>
                            <option value="GB" {{ old('country') == 'GB' ? 'selected' : '' }}>United Kingdom</option>
                            <option value="other" {{ old('country') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    {{-- Save card checkbox --}}
                    <label class="flex items-center gap-3 mb-6 cursor-pointer">
                        <div class="w-5 h-5 bg-gray-800 border border-gray-600 rounded flex items-center
                                    justify-center flex-shrink-0" id="save-box">
                        </div>
                        <input type="checkbox" name="save_card" class="hidden" id="save-check"
                               onchange="toggleSave()">
                        <div>
                            <div class="text-sm font-semibold">Save this card</div>
                            <div class="text-xs text-gray-500">Securely stored · Remove anytime</div>
                        </div>
                        <span class="ml-auto text-xs text-indigo-400">Pay in one click next time!</span>
                    </label>

                    {{-- Hidden listing id --}}
                    <input type="hidden" name="listing_id" value="{{ $listing->id }}">

                </form>

                {{-- Security badges --}}
                <div class="flex items-center gap-4 pt-5 border-t border-gray-800 flex-wrap">
                    <span class="text-xs text-gray-600 bg-gray-800 px-2 py-1 rounded">PCI DSS</span>
                    <span class="text-xs text-gray-600 bg-gray-800 px-2 py-1 rounded">ID Check</span>
                    <span class="text-xs text-gray-600 bg-gray-800 px-2 py-1 rounded">SafeKey</span>
                    <span class="text-xs text-gray-600 bg-gray-800 px-2 py-1 rounded">🔒 Verified</span>
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

                <button onclick="document.getElementById('card-form').submit()"
                        class="w-full bg-green-600 hover:bg-green-500 text-white py-3.5 rounded-xl
                               font-bold text-sm transition flex items-center justify-center gap-2">
                    Pay ${{ number_format($listing->price, 2) }} →
                </button>

                <p class="text-xs text-gray-600 text-center mt-3">
                    🔒 Secured by escrow protection
                </p>
            </div>
        </div>

    </div>
</div>

<script>
// Format card number with spaces
document.getElementById('card-number').addEventListener('input', function (e) {
    let val = e.target.value.replace(/\D/g, '').substring(0, 16);
    e.target.value = val.replace(/(.{4})/g, '$1 ').trim();
});

// Format expiry MM/YY
document.getElementById('expiry').addEventListener('input', function (e) {
    let val = e.target.value.replace(/\D/g, '').substring(0, 4);
    if (val.length >= 2) val = val.substring(0,2) + '/' + val.substring(2);
    e.target.value = val;
});

// Toggle save card checkbox
function toggleSave() {
    const box   = document.getElementById('save-box');
    const check = document.getElementById('save-check');
    check.checked = !check.checked;
    box.innerHTML = check.checked
        ? '<span class="text-indigo-400 text-xs font-bold">✓</span>'
        : '';
    box.classList.toggle('border-indigo-500', check.checked);
}
document.getElementById('save-box').addEventListener('click', toggleSave);
</script>
@endsection