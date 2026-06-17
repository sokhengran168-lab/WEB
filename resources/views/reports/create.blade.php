@extends('layouts.app')
@section('title', 'Report Listing')

@section('content')
<div class="max-w-lg mx-auto px-4 py-8">

    <h1 class="text-xl font-bold mb-1">🚩 Report Listing</h1>
    <p class="text-gray-400 text-sm mb-6">
        Help keep GameTradeHub safe. Reports are reviewed within 24 hours.
    </p>

    {{-- Listing Preview --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 mb-5 flex gap-3">

        {{-- Image --}}
        <div class="w-12 h-10 bg-gray-800 rounded-lg overflow-hidden flex-shrink-0">
            @if($listing->firstImage)
                <img src="{{ $listing->firstImage->url }}"
                    alt="{{ $listing->title }}"
                    class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="2"
                            d="M3 7a2 2 0 012-2h3l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/>
                    </svg>
                </div>
            @endif
        </div>

        {{-- Info --}}
        <div>
            <div class="font-semibold text-sm">{{ $listing->title }}</div>
            <div class="text-xs text-gray-400">
                {{ $listing->game->name }} · ${{ number_format($listing->price, 2) }}
            </div>
        </div>

    </div>

    <form method="POST" action="{{ route('listings.report.store', $listing) }}">
        @csrf

        {{-- Reason --}}
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-5 mb-4">
            <label class="block text-xs font-bold text-gray-500
                          uppercase tracking-wider mb-3">
                Reason for Report *
            </label>
            <div class="flex flex-col gap-2">
            @foreach([
                'scam' => [
                    'icon' => 'exclamation-triangle',
                    'label' => 'Scam / Fraud',
                    'desc' => 'Seller is trying to scam buyers'
                ],
                'fake_screenshots' => [
                    'icon' => 'photo',
                    'label' => 'Fake Screenshots',
                    'desc' => 'Screenshots are edited or fake'
                ],
                'wrong_info' => [
                    'icon' => 'x-circle',
                    'label' => 'Wrong Information',
                    'desc' => 'Account details are incorrect'
                ],
                'duplicate' => [
                    'icon' => 'duplicate',
                    'label' => 'Duplicate Listing',
                    'desc' => 'Same account listed multiple times'
                ],
                'inappropriate' => [
                    'icon' => 'shield-exclamation',
                    'label' => 'Inappropriate Content',
                    'desc' => 'Content violates our rules'
                ],
                'other' => [
                    'icon' => 'chat-bubble-left',
                    'label' => 'Other',
                    'desc' => 'Something else not listed above'
                ],
            ] as $value => $item)
            <label class="cursor-pointer">
                <input type="radio" name="reason" value="{{ $value }}"
                    class="sr-only peer"
                    {{ old('reason') === $value ? 'checked' : '' }}>

                <div class="flex items-center gap-3 bg-gray-800 border-2 border-gray-700
                            hover:border-gray-600 rounded-xl p-3 transition
                            peer-checked:border-red-500 peer-checked:bg-red-500/5">

                    {{-- SVG Icon --}}
                    <div class="w-6 h-6 text-red-400 flex-shrink-0">
                        @switch($item['icon'])
                            @case('exclamation-triangle')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-width="2" d="M12 9v4m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4a2 2 0 00-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/>
                                </svg>
                            @break

                            @case('photo')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-width="2" d="M3 7a2 2 0 012-2h3l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/>
                                </svg>
                            @break

                            @case('x-circle')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            @break

                            @case('duplicate')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-width="2" d="M8 16h8M8 12h8M8 8h8M4 4h16v16H4z"/>
                                </svg>
                            @break

                            @case('shield-exclamation')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-width="2" d="M12 3l8 4v5c0 5-3.5 8.5-8 9-4.5-.5-8-4-8-9V7l8-4z"/>
                                </svg>
                            @break

                            @case('chat-bubble-left')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-width="2" d="M8 10h8M8 14h6M21 12c0 4-4 8-9 8a9.77 9.77 0 01-4-.8L3 21l1.8-3.6A8 8 0 113 12c0-4 4-8 9-8s9 4 9 8z"/>
                                </svg>
                            @break
                        @endswitch
                    </div>

                    <div>
                        <div class="text-sm font-semibold">{{ $item['label'] }}</div>
                        <div class="text-xs text-gray-400">{{ $item['desc'] }}</div>
                    </div>

                </div>
            </label>
            @endforeach
            </div>
            @error('reason')
            <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
            @enderror
        </div>

        {{-- Details --}}
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-5 mb-5">
            <label class="block text-xs font-bold text-gray-500
                          uppercase tracking-wider mb-3">
                Additional Details (Optional)
            </label>
            <textarea name="details" rows="3"
                      placeholder="Describe the issue in more detail..."
                      class="w-full bg-gray-800 border border-gray-700 rounded-xl
                             px-3 py-2.5 text-sm text-white
                             focus:outline-none focus:border-red-500 resize-none">{{ old('details') }}</textarea>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('listings.show', $listing) }}"
               class="text-gray-400 hover:text-white text-sm transition">
                ← Cancel
            </a>
            <button type="submit"
                    class="bg-red-600 hover:bg-red-500 text-white
                           px-6 py-2.5 rounded-xl font-bold text-sm transition">
                Submit Report
            </button>
        </div>

    </form>
</div>
@endsection
