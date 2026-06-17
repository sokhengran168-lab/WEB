@extends('layouts.app')
@section('title', $listing->title . ' — GameTradeHub')
@section('og_type', 'product')
@section('og_title', $listing->title . ' — GameTradeHub')
@section('og_description', 'Buy: ' . $listing->title . ' | ' . $listing->game->name . ' | $' . number_format($listing->price, 2) . ' | Escrow Protected')
@section('og_image', $listing->firstImage ? $listing->firstImage->url : asset('images/og-default.jpg'))

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-5">
        <a href="{{ route('listings.index') }}" class="hover:text-white transition">Browse</a>
        <span>›</span>
        <a href="{{ route('listings.index', ['game' => $listing->game->slug]) }}"
           class="hover:text-white transition">{{ $listing->game->name }}</a>
        <span>›</span>
        <span class="text-gray-300 truncate">{{ $listing->title }}</span>
    </div>

    @if(Auth::check() && Auth::id() === $listing->user_id && $listing->status !== 'active')
    <div class="bg-yellow-500/10 border border-yellow-500/25 rounded-xl p-4 mb-5 text-sm text-yellow-200">
        Your listing is pending admin review and is hidden from public search, but you can still edit it from your dashboard.
    </div>
    @endif

    {{-- ── Main 2-col grid ── --}}
    <div class="grid grid-cols-3 gap-6 mb-10">

        {{-- Left column --}}
        <div class="col-span-2 flex flex-col gap-5">

            {{-- Images --}}
            <div>
                <div class="bg-gray-900 border border-gray-800 rounded-xl h-72
                            flex items-center justify-center overflow-hidden mb-3">
                    @if($listing->images->count() > 0)
                        <img id="mainImg" src="{{ $listing->images->first()->url }}"
                             class="w-full h-full object-cover">
                    @else
                        <span class="text-6xl">🎮</span>
                    @endif
                </div>

                @if($listing->images->count() > 1)
                <div class="flex gap-2">
                    @foreach($listing->images as $image)
                    <img src="{{ $image->url }}" data-src="{{ $image->url }}"
                         class="listing-thumb w-16 h-12 object-cover rounded-lg
                                border-2 border-gray-700 hover:border-indigo-500 cursor-pointer transition">
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Specs --}}
            <div class="grid grid-cols-4 gap-3">
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-3">
                    <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Game</div>
                    <div class="font-semibold text-sm">{{ $listing->game->name }}</div>
                </div>
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-3">
                    <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Rank</div>
                    <div class="font-semibold text-sm">{{ $listing->rank ?? '—' }}</div>
                </div>
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-3">
                    <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Level</div>
                    <div class="font-semibold text-sm">{{ $listing->level ?? '—' }}</div>
                </div>
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-3">
                    <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Platform</div>
                    <div class="font-semibold text-sm">{{ $listing->platform }}</div>
                </div>
            </div>

            {{-- Description --}}
            @if($listing->description)
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Description</h3>
                <p class="text-gray-300 text-sm leading-relaxed whitespace-pre-line">
                    {{ $listing->description }}
                </p>
            </div>
            @endif

            {{-- Contact Seller --}}
            @php
                $hasTelegram = !empty($listing->contact_telegram);
                $hasWhatsapp = !empty($listing->contact_whatsapp);
                $hasDiscord  = !empty($listing->contact_discord);
                $hasPhone    = !empty($listing->seller_phone);
                $hasContact  = $hasTelegram || $hasWhatsapp || $hasDiscord || $hasPhone;
            @endphp

            @if($hasContact)
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Contact Seller</h3>
                <div class="flex flex-col gap-2">

                    @if($hasTelegram)
                    <a href="https://t.me/{{ $listing->contact_telegram }}" target="_blank"
                       class="flex items-center gap-3 bg-gray-800 hover:bg-gray-750
                              border border-gray-700 hover:border-sky-500/40 rounded-xl p-3 transition group">
                        <div class="w-9 h-9 bg-sky-500/15 border border-sky-500/20 rounded-xl
                                    flex items-center justify-center flex-shrink-0">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="#38bdf8">
                                <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.248-1.97 9.289c-.145.658-.537.818-1.084.508l-3-2.21-1.447 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12L8.48 14.617l-2.95-.924c-.642-.204-.657-.642.136-.953l11.57-4.461c.537-.194 1.006.131.326.969z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-xs text-gray-500 mb-0.5">Telegram</div>
                            <div class="text-sm font-semibold text-sky-400 group-hover:text-sky-300">
                                {{ $listing->contact_telegram }}
                            </div>
                        </div>
                        <span class="text-gray-600 group-hover:text-gray-400 text-xs">→</span>
                    </a>
                    @endif

                    @if($hasWhatsapp)
                    <a href="https://api.whatsapp.com/send?phone={{ preg_replace('/\D/', '', $listing->contact_whatsapp) }}"
                       target="_blank"
                       class="flex items-center gap-3 bg-gray-800 hover:bg-gray-750
                              border border-gray-700 hover:border-green-500/40 rounded-xl p-3 transition group">
                        <div class="w-9 h-9 bg-green-500/15 border border-green-500/20 rounded-xl
                                    flex items-center justify-center flex-shrink-0">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="#22c55e">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                <path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.122 1.532 5.856L0 24l6.335-1.54A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.885 0-3.65-.51-5.17-1.399l-.37-.22-3.76.914.949-3.671-.242-.378A9.96 9.96 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-xs text-gray-500 mb-0.5">WhatsApp</div>
                            <div class="text-sm font-semibold text-green-400 group-hover:text-green-300">
                                {{ $listing->contact_whatsapp }}
                            </div>
                        </div>
                        <span class="text-gray-600 group-hover:text-gray-400 text-xs">→</span>
                    </a>
                    @endif

                    @if($hasDiscord)
                    <div class="flex items-center gap-3 bg-gray-800 border border-gray-700 rounded-xl p-3">
                        <div class="w-9 h-9 bg-indigo-500/15 border border-indigo-500/20 rounded-xl
                                    flex items-center justify-center flex-shrink-0">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="#818cf8">
                                <path d="M20.317 4.37a19.791 19.791 0 00-4.885-1.515.074.074 0 00-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 00-5.487 0 12.64 12.64 0 00-.617-1.25.077.077 0 00-.079-.037A19.736 19.736 0 003.677 4.37a.07.07 0 00-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 00.031.057 19.9 19.9 0 005.993 3.03.078.078 0 00.084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 00-.041-.106 13.107 13.107 0 01-1.872-.892.077.077 0 01-.008-.128 10.2 10.2 0 00.372-.292.074.074 0 01.077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 01.078.01c.12.098.246.198.373.292a.077.077 0 01-.006.127 12.299 12.299 0 01-1.873.892.077.077 0 00-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 00.084.028 19.839 19.839 0 006.002-3.03.077.077 0 00.032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 00-.031-.03z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-xs text-gray-500 mb-0.5">Discord</div>
                            <div class="text-sm font-semibold text-indigo-400">
                                {{ $listing->contact_discord }}
                            </div>
                        </div>
                        <button class="copy-btn text-xs text-gray-500 hover:text-white
                                       bg-gray-700 hover:bg-gray-600 px-2 py-1 rounded-lg transition"
                                data-copy="{{ $listing->contact_discord }}">
                            Copy
                        </button>
                    </div>
                    @endif

                    @if($hasPhone)
                    <div class="flex items-center gap-3 bg-gray-800 border border-gray-700 rounded-xl p-3">
                        <div class="w-9 h-9 bg-purple-500/15 border border-purple-500/20 rounded-xl
                                    flex items-center justify-center flex-shrink-0">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                 stroke="#a78bfa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.89 9.11 19.79 19.79 0 01.82.45 2 2 0 012.82 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L7.09 9.91a16 16 0 006 6l.91-.91a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 17.92v-.01-.99z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-xs text-gray-500 mb-0.5">Phone</div>
                            <div class="text-sm font-semibold text-purple-400">
                                {{ $listing->seller_phone }}
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
            @endif

            {{-- Seller Reviews --}}
            @php
                $sellerReviews = $listing->seller->reviews()->with('reviewer')->take(3)->get();
                $totalReviews  = $listing->seller->reviews()->count();
            @endphp
            @if($sellerReviews->count() > 0)
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Seller Reviews</h3>
                    <a href="{{ route('sellers.show', $listing->seller) }}"
                       class="text-xs text-indigo-400 hover:underline">
                        View all {{ $totalReviews }} →
                    </a>
                </div>

                <div class="flex items-center gap-3 bg-gray-800 rounded-xl p-3 mb-4">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-yellow-400">
                            {{ number_format($listing->seller->rating_avg, 1) }}
                        </div>
                        <div class="text-xs text-gray-500">out of 5</div>
                    </div>
                    <div class="flex-1">
                        <div class="flex text-yellow-400 mb-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($listing->seller->rating_avg))
                                    <x-heroicon-s-star class="w-5 h-5" />
                                @else
                                    <x-heroicon-o-star class="w-5 h-5" />
                                @endif
                            @endfor
                        </div>
                        <div class="text-xs text-gray-400">
                            {{ $totalReviews }} reviews
                        </div>
                    </div>
                </div>

                @foreach($sellerReviews as $review)
                <div class="border-b border-gray-800 last:border-0 py-3">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 bg-indigo-600 rounded-full flex items-center
                                        justify-center text-xs font-bold">
                                {{ strtoupper(substr($review->reviewer->name, 0, 1)) }}
                            </div>
                            <span class="text-sm font-semibold">{{ $review->reviewer->name }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="flex">{!! $review->stars() !!}</span>
                            <span class="text-xs text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    @if($review->comment)
                    <p class="text-xs text-gray-300 mt-1 leading-relaxed">{{ $review->comment }}</p>
                    @endif
                </div>
                @endforeach
            </div>
            @endif

        </div>

        {{-- ── Right column — Buy Box ── --}}
        <div>
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-5 sticky top-20">

                <div class="text-3xl font-bold text-green-400 mb-1">
                    ${{ number_format($listing->price, 2) }}
                </div>
                <div class="font-bold text-lg mb-4 leading-tight">{{ $listing->title }}</div>

                {{-- Seller --}}
                <div class="flex items-center gap-3 bg-gray-800 rounded-xl p-3 mb-4">
                    <div class="w-9 h-9 bg-indigo-600 rounded-full flex items-center
                                justify-center font-bold text-sm flex-shrink-0">
                        {{ strtoupper(substr($listing->seller->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('sellers.show', $listing->seller) }}"
                           class="font-semibold text-sm hover:text-indigo-400 transition">
                            {{ $listing->seller->name }}
                        </a>
                        <div class="flex items-center gap-2 text-xs text-gray-400 flex-wrap mt-0.5">
                            <span>🛒 {{ $listing->seller->total_sales }} sales</span>
                            @if($listing->seller->rating_avg > 0)
                            <span>·</span>
                            <span class="text-yellow-400">⭐ {{ number_format($listing->seller->rating_avg, 1) }}</span>
                            @endif
                            @if($listing->seller->is_verified)
                            <span>·</span>
                            <span class="text-sky-400">✓ Verified</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Escrow --}}
                <div class="bg-cyan-500/5 border border-cyan-500/15 rounded-xl p-3 mb-4
                            text-xs text-gray-400 flex gap-2">
                    <span class="text-base flex-shrink-0">🔒</span>
                    <span>Payment held in <strong class="text-cyan-400">escrow</strong>
                          until you confirm the account. 48-hour review window.</span>
                </div>

                @auth
                    @if($listing->user_id === auth()->id())
                    <div class="flex gap-2 mb-4">
                        <a href="{{ route('listings.edit', $listing) }}"
                        class="flex-1 flex items-center justify-center gap-2
                                bg-gray-700 hover:bg-gray-600 text-white
                                py-2.5 rounded-xl text-sm font-semibold transition">
                            <x-heroicon-o-pencil-square class="w-5 h-5" />
                            <span>Edit</span>
                        </a>

                        <form method="POST" action="{{ route('listings.destroy', $listing) }}" id="deleteForm">
                            @csrf
                            @method('DELETE')

                            <button type="button" id="deleteBtn"
                                    class="flex items-center justify-center
                                        bg-red-600/20 hover:bg-red-600/40 text-red-400
                                        py-2.5 px-4 rounded-xl transition">
                                <x-heroicon-o-trash class="w-5 h-5" />
                            </button>
                        </form>
                    </div>
                    @else
                    <form action="{{ route('transactions.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="listing_id" value="{{ $listing->id }}">

                        <button type="submit"
                            class="block w-full text-center bg-green-600 hover:bg-green-500
                                text-white py-3 rounded-xl font-bold text-sm transition mb-2">
                            🛒 Buy Now — ${{ number_format($listing->price, 2) }}
                        </button>
                    </form>
                    <div class="text-center text-xs text-gray-500 mb-4">
                        🏦 Pay via bank transfer · Escrow protected
                    </div>
                    @endif
                @else
                <a href="{{ route('login') }}"
                   class="block w-full bg-indigo-600 hover:bg-indigo-500 text-white text-center
                          py-3 rounded-xl font-bold text-sm transition mb-4">
                    Login to Buy
                </a>
                @endauth

                {{-- Share --}}
                <div class="border-t border-gray-800 pt-4 mb-4">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                        Share
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <a href="https://api.whatsapp.com/send?text={{ urlencode($listing->title . ' — $' . number_format($listing->price, 2) . ' | ' . url()->current()) }}"
                           target="_blank"
                           class="flex items-center justify-center gap-1.5 py-2 rounded-xl border
                                  border-green-500/20 bg-green-500/5 hover:bg-green-500/15
                                  text-green-400 text-xs font-semibold transition">
                            WhatsApp
                        </a>
                        <a href="https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode($listing->title) }}"
                           target="_blank"
                           class="flex items-center justify-center gap-1.5 py-2 rounded-xl border
                                  border-sky-500/20 bg-sky-500/5 hover:bg-sky-500/15
                                  text-sky-400 text-xs font-semibold transition">
                            Telegram
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                           target="_blank"
                           class="flex items-center justify-center gap-1.5 py-2 rounded-xl border
                                  border-blue-500/20 bg-blue-500/5 hover:bg-blue-500/15
                                  text-blue-400 text-xs font-semibold transition">
                            Facebook
                        </a>
                        <button id="copyLinkBtn" data-url="{{ url()->current() }}"
                                class="flex items-center justify-center gap-1.5 py-2 rounded-xl border
                                       border-indigo-500/20 bg-indigo-500/5 hover:bg-indigo-500/15
                                       text-indigo-400 text-xs font-semibold transition w-full">
                            Copy Link
                        </button>
                    </div>
                </div>

                {{-- Trust --}}
                <div class="border-t border-gray-800 pt-4 flex flex-col gap-2 text-xs text-gray-500">
                    <span>✅ Verified listing — proof screenshots checked</span>
                    <span>🔒 Escrow protected — funds safe until confirmed</span>
                    <span>⚖️ Dispute resolution available</span>
                </div>

                <div class="mt-3 pt-3 border-t border-gray-800 text-center">
                    <a href="{{ route('listings.report', $listing) }}"
                       class="text-xs text-gray-600 hover:text-red-400 transition">
                        🚩 Report this listing
                    </a>
                </div>

            </div>
        </div>

    </div>
    {{-- ── Below-the-fold: full-width explore sections ── --}}

    {{-- More from this seller --}}
    @php
        $moreSeller = \App\Models\Listing::where('user_id', $listing->user_id)
            ->where('id', '!=', $listing->id)
            ->where('status', 'active')
            ->latest()
            ->take(4)
            ->get();
    @endphp
    @if($moreSeller->count() > 0)
    <section class="mb-10">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold text-lg">More from {{ $listing->seller->name }}</h2>
            <a href="{{ route('sellers.show', $listing->seller) }}"
               class="text-sm text-indigo-400 hover:underline">View all →</a>
        </div>
        <div class="grid grid-cols-4 gap-4">
            @foreach($moreSeller as $item)
            @include('listings._card', ['listing' => $item])
            @endforeach
        </div>
    </section>
    @endif

    {{-- Similar listings --}}
    @php
        $similar = \App\Models\Listing::where('game_id', $listing->game_id)
            ->where('id', '!=', $listing->id)
            ->where('status', 'active')
            ->latest()
            ->take(4)
            ->get();
    @endphp
    @if($similar->count() > 0)
    <section class="mb-10">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold text-lg">Similar {{ $listing->game->name }} Accounts</h2>
            <a href="{{ route('listings.index', ['game' => $listing->game->slug]) }}"
               class="text-sm text-indigo-400 hover:underline">Browse all →</a>
        </div>
        <div class="grid grid-cols-4 gap-4">
            @foreach($similar as $item)
            @include('listings._card', ['listing' => $item])
            @endforeach
        </div>
    </section>
    @endif

</div>
@endsection
