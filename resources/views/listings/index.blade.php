@extends('layouts.app')
@section('title', 'GameTradeHub — Buy & Sell Game Accounts')

@section('content')

{{-- ═══ ONBOARDING HERO ══════════════════════════════════════════════════════ --}}
<div class="relative overflow-hidden min-h-screen flex flex-col justify-center" style="
    background: radial-gradient(ellipse at 20% 50%, rgba(99,102,241,0.18) 0%, transparent 55%),
                radial-gradient(ellipse at 80% 20%, rgba(139,92,246,0.12) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 100%, rgba(6,182,212,0.08) 0%, transparent 50%),
                linear-gradient(180deg, #020817 0%, #0a0a18 100%);
">
    {{-- Grid overlay --}}
    <div class="absolute inset-0 pointer-events-none" style="
        background-image:
            linear-gradient(rgba(99,102,241,0.04) 1px, transparent 1px),
            linear-gradient(90deg, rgba(99,102,241,0.04) 1px, transparent 1px);
        background-size: 48px 48px;
    "></div>

    {{-- Glow blobs --}}
    <div class="absolute top-1/4 left-1/4 w-96 h-96 rounded-full pointer-events-none" style="background: radial-gradient(circle, rgba(99,102,241,0.12), transparent 70%); filter: blur(40px);"></div>
    <div class="absolute bottom-1/4 right-1/4 w-64 h-64 rounded-full pointer-events-none" style="background: radial-gradient(circle, rgba(139,92,246,0.1), transparent 70%); filter: blur(30px);"></div>

    <div class="relative z-10 max-w-3xl mx-auto px-6 py-20 flex flex-col items-center text-center w-full">

        {{-- Live badge --}}
        <div class="inline-flex items-center gap-2 mb-8 px-4 py-2 rounded-full border"
             style="background: rgba(99,102,241,0.08); border-color: rgba(99,102,241,0.2);">
            <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
            <span class="text-xs font-semibold tracking-widest text-indigo-300 uppercase">Live Marketplace</span>
        </div>

        {{-- Headline --}}
        <h1 class="font-black leading-tight mb-5" style="font-size: clamp(36px, 6vw, 64px); letter-spacing: -0.02em;">
            <span class="text-white">Buy & sell</span><br>
            <span style="background: linear-gradient(135deg, #6366f1 0%, #a78bfa 50%, #06b6d4 100%);
                         -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                game accounts
            </span>
        </h1>

        <p class="text-gray-400 mb-10 leading-relaxed" style="font-size: 16px; max-width: 480px;">
            The safest place to trade gaming accounts.<br>
            <span class="text-indigo-300">Escrow-protected</span> · Verified sellers · Instant delivery.
        </p>

        {{-- Search bar --}}
        <form id="searchForm" method="GET" action="{{ route('home') }}"
              class="w-full max-w-xl mb-4">
            <div class="flex gap-2">
                <div class="relative flex-1">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text"
                           id="heroSearchInput"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search by game, rank, server…"
                           autocomplete="off"
                           oninput="toggleClearBtn()"
                           class="w-full pl-11 pr-4 py-3.5 rounded-xl text-sm text-white
                                  placeholder-gray-600 outline-none transition-all duration-200"
                           style="background: rgba(255,255,255,0.05);
                                  border: 1px solid rgba(255,255,255,0.1);"
                           onfocus="this.style.borderColor='rgba(99,102,241,0.6)'; this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.15)'"
                           onblur="this.style.borderColor='rgba(255,255,255,0.1)'; this.style.boxShadow='none'">

<button type="button"
            id="clearSearchBtn"
            onclick="clearSearch()"
            class="hidden absolute right-3 top-1/2 -translate-y-1/2
                   text-gray-500 hover:text-white transition">

        <i class="fa-solid fa-xmark text-sm"></i>

    </button>

                </div>
                <button type="submit"
                        class="px-6 py-3.5 rounded-xl text-white text-sm font-bold tracking-wide
                               transition-all duration-150 hover:opacity-90 active:scale-95 whitespace-nowrap"
                        style="background: linear-gradient(135deg, #6366f1, #8b5cf6);
                               box-shadow: 0 4px 24px rgba(99,102,241,0.35);">
                    Search
                </button>
            </div>
        </form>

        {{-- Trending label --}}
        <p class="text-xs text-gray-600 uppercase tracking-widest mb-3">Trending searches</p>

        {{-- Random game chips — shuffled server side --}}
        <div class="flex flex-wrap justify-center gap-2 mb-10" id="trendingChips">
            @foreach($games->shuffle()->take(7) as $game)
            <button type="button"
                    onclick="quickSearch('{{ addslashes($game->name) }}', {{ $game->id }})"
                    class="px-4 py-2 rounded-full text-xs font-medium transition-all duration-150
                           text-gray-400 hover:text-indigo-300 active:scale-95"
                    style="background: rgba(255,255,255,0.04);
                           border: 1px solid rgba(255,255,255,0.08);"
                    onmouseover="this.style.borderColor='rgba(99,102,241,0.4)'; this.style.background='rgba(99,102,241,0.08)'"
                    onmouseout="this.style.borderColor='rgba(255,255,255,0.08)'; this.style.background='rgba(255,255,255,0.04)'">
                {{ $game->name }}
            </button>
            @endforeach
        </div>

        {{-- Stats --}}
        <div class="flex items-center gap-10 mb-12">
            <div class="text-center">
                <div class="text-2xl font-black text-indigo-400 tabular-nums"
                     data-count="{{ $totalListings }}">0+</div>
                <div class="text-xs text-gray-600 uppercase tracking-widest mt-1">Listings</div>
            </div>
            <div class="w-px h-10" style="background: rgba(255,255,255,0.07)"></div>
            <div class="text-center">
                <div class="text-2xl font-black text-green-400">{{ number_format($totalSales) }}+</div>
                <div class="text-xs text-gray-600 uppercase tracking-widest mt-1">Sold</div>
            </div>
            <div class="w-px h-10" style="background: rgba(255,255,255,0.07)"></div>
            <div class="text-center">
                <div class="text-2xl font-black text-yellow-400">{{ number_format($totalSellers) }}+</div>
                <div class="text-xs text-gray-600 uppercase tracking-widest mt-1">Sellers</div>
            </div>
        </div>

        {{-- Featured listing cards --}}
        @if($featured->count() > 0)
        <div class="w-full grid grid-cols-3 gap-3 mb-10">
            @foreach($featured->shuffle()->take(3) as $item)
            <a href="{{ route('listings.show', $item) }}"
               class="group text-left rounded-xl p-4 transition-all duration-200"
               style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07);"
               onmouseover="this.style.borderColor='rgba(99,102,241,0.35)'; this.style.background='rgba(99,102,241,0.06)'; this.style.transform='translateY(-2px)'"
               onmouseout="this.style.borderColor='rgba(255,255,255,0.07)'; this.style.background='rgba(255,255,255,0.03)'; this.style.transform='translateY(0)'">
                @if($item->is_featured)
                <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full mb-2"
                      style="background: rgba(239,68,68,0.15); color: #f87171;">Hot</span>
                @elseif(($item->views_count ?? 0) > 50)
                <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full mb-2"
                      style="background: rgba(99,102,241,0.15); color: #a5b4fc;">Popular</span>
                @endif
                <div class="text-xs text-gray-600 uppercase tracking-wide mb-1">{{ $item->game->name }}</div>
                <div class="text-sm font-semibold text-gray-200 mb-2 leading-snug">
                    {{ Str::limit($item->title, 40) }}
                </div>
                <div class="text-base font-bold text-indigo-400">${{ number_format($item->price, 2) }}</div>
            </a>
            @endforeach
        </div>
        @endif

        {{-- Trust strip --}}
        <div class="flex items-center gap-6 flex-wrap justify-center">
            @foreach([
                ['M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'Secure escrow'],
                ['M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-.343-.03-.67-.07-1z', 'Verified sellers'],
                ['M13 10V3L4 14h7v7l9-11h-7z', 'Instant listing'],
            ] as [$path, $label])
            <div class="flex items-center gap-2 text-xs text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/>
                </svg>
                {{ $label }}
            </div>
            @endforeach
        </div>

    </div>

    {{-- Scroll cue --}}
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex flex-col items-center gap-1 animate-bounce">
        <span class="text-xs text-gray-700">Browse accounts</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-700" fill="none"
             viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </div>
</div>

{{-- ═══ GAME CATEGORY NAV (sticky) ════════════════════════════════════════════ --}}
<div class="bg-gray-950 border-b border-gray-900 sticky top-0 z-30" id="browse">
    <div class="max-w-7xl mx-auto px-4 py-3">
        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-hide">

            <a href="{{ route('home', request()->only(['search','sort','platform','min_price','max_price'])) }}"
               data-filter
               class="flex-shrink-0 flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold
                      transition whitespace-nowrap
                      {{ !request('game_id') ? 'bg-indigo-600 text-white' : 'bg-gray-900 text-gray-400 hover:bg-gray-800 hover:text-white border border-gray-800' }}">
                All Games
            </a>

            @foreach($games as $game)
            <a href="{{ route('home', array_merge(request()->only(['search','sort','platform','min_price','max_price']), ['game_id' => $game->id])) }}"
               data-filter
               class="flex-shrink-0 flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold
                      transition whitespace-nowrap
                      {{ request('game_id') == $game->id ? 'bg-indigo-600 text-white' : 'bg-gray-900 text-gray-400 hover:bg-gray-800 hover:text-white border border-gray-800' }}">
                {{ $game->name }}
                @if(isset($game->active_listings_count) && $game->active_listings_count > 0)
                <span class="bg-gray-800 text-gray-500 px-1.5 py-0.5 rounded-full text-xs">
                    {{ $game->active_listings_count }}
                </span>
                @endif
            </a>
            @endforeach

            <div class="w-px h-5 bg-gray-800 flex-shrink-0 mx-1"></div>

            <a href="{{ route('auctions.index') }}"
               class="flex-shrink-0 flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold
                      bg-yellow-500/10 border border-yellow-500/20 text-yellow-400
                      hover:bg-yellow-500/20 transition whitespace-nowrap">
                <span class="w-1.5 h-1.5 bg-yellow-400 rounded-full animate-pulse"></span>
                Live Auctions
                @if(isset($auctionCount) && $auctionCount > 0)
                <span class="bg-yellow-500 text-black px-1.5 py-0.5 rounded-full text-xs font-black">
                    {{ $auctionCount }}
                </span>
                @endif
            </a>

        </div>
    </div>
</div>

{{-- ═══ LIVE AUCTIONS STRIP ════════════════════════════════════════════════════ --}}
@if($liveAuctions->count() > 0)
<div class="border-b border-yellow-500/10" style="background: rgba(250,204,21,0.03);">
    <div class="max-w-7xl mx-auto px-4 py-5">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></span>
                <span class="text-xs font-bold text-yellow-400 tracking-widest uppercase">Live Auctions</span>
            </div>
            <a href="{{ route('auctions.index') }}"
               class="text-xs text-yellow-400/50 hover:text-yellow-400 transition">View all →</a>
        </div>
        <div class="grid grid-cols-4 gap-3">
            @foreach($liveAuctions as $auction)
            <a href="{{ route('auctions.show', $auction) }}"
               class="block rounded-xl p-3 transition-all duration-150"
               style="background: rgba(255,255,255,0.03); border: 1px solid rgba(250,204,21,0.08);"
               onmouseover="this.style.borderColor='rgba(250,204,21,0.25)'"
               onmouseout="this.style.borderColor='rgba(250,204,21,0.08)'">
                <div class="text-xs text-yellow-400/60 font-bold mb-1 uppercase tracking-wide">{{ $auction->game->name }}</div>
                <div class="text-sm font-semibold text-white mb-3 leading-snug line-clamp-1">{{ $auction->title }}</div>
                <div class="flex items-end justify-between">
                    <div>
                        <div class="text-xs text-gray-600 mb-0.5">{{ $auction->current_bid ? 'Current bid' : 'Starting' }}</div>
                        <div class="text-sm font-black text-yellow-400">
                            ${{ number_format($auction->current_bid ?? $auction->starting_price, 2) }}
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-gray-600 mb-0.5">Ends</div>
                        <div class="text-xs font-bold text-red-400">{{ $auction->timeRemaining() }}</div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ═══ MAIN BROWSE ════════════════════════════════════════════════════════════ --}}
<div class="bg-gray-950 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 py-8">

        <form id="filterForm" method="GET" action="{{ route('home') }}">
            @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            @if(request('game_id'))
            <input type="hidden" name="game_id" value="{{ request('game_id') }}">
            @endif

            <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
                <div>
                    <h2 class="text-base font-bold text-white tracking-tight">
                        @if(request('search'))
                            Results for "{{ request('search') }}"
                        @elseif(request('game_id'))
                            {{ $games->find(request('game_id'))->name ?? 'Accounts' }}
                        @else
                            All accounts
                        @endif
                    </h2>
                    <p class="text-xs text-gray-600 mt-0.5">{{ $listings->total() }} accounts available</p>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <select name="platform"
                            class="bg-gray-900 border border-gray-800 rounded-xl px-3 py-2
                                   text-xs text-gray-400 focus:outline-none focus:border-indigo-500 cursor-pointer">
                        <option value="">All platforms</option>
                        <option value="Mobile"  {{ request('platform') === 'Mobile'  ? 'selected' : '' }}>Mobile</option>
                        <option value="PC"      {{ request('platform') === 'PC'      ? 'selected' : '' }}>PC</option>
                        <option value="Console" {{ request('platform') === 'Console' ? 'selected' : '' }}>Console</option>
                    </select>

                    <select name="sort"
                            class="bg-gray-900 border border-gray-800 rounded-xl px-3 py-2
                                   text-xs text-gray-400 focus:outline-none focus:border-indigo-500 cursor-pointer">
                        <option value="">Latest</option>
                        <option value="price_asc"  {{ request('sort') === 'price_asc'  ? 'selected' : '' }}>Price: Low → High</option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High → Low</option>
                        <option value="popular"    {{ request('sort') === 'popular'    ? 'selected' : '' }}>Popular</option>
                    </select>

                    <div class="flex items-center gap-1 bg-gray-900 border border-gray-800 rounded-xl px-3 py-2">
                        <span class="text-xs text-gray-600">$</span>
                        <input type="number" name="min_price" value="{{ request('min_price') }}"
                               placeholder="Min"
                               class="w-14 bg-transparent text-xs text-gray-400 focus:outline-none placeholder-gray-700">
                        <span class="text-xs text-gray-600">—</span>
                        <input type="number" name="max_price" value="{{ request('max_price') }}"
                               placeholder="Max"
                               class="w-14 bg-transparent text-xs text-gray-400 focus:outline-none placeholder-gray-700">
                        <button type="submit"
                                class="text-xs text-indigo-400 hover:text-indigo-300 ml-1">↵</button>
                    </div>

                    @if(request()->hasAny(['platform','sort','min_price','max_price']))
                    <a href="{{ route('home', request()->only(['search','game_id'])) }}"
                       class="text-xs text-gray-600 hover:text-gray-400 transition px-1">Clear</a>
                    @endif
                </div>
            </div>
        </form>

        <div id="listingsArea" class="transition-all duration-300 will-change-transform opacity-100 translate-y-0">
            @include('partials.listings-grid', ['listings' => $listings])
        </div>

    </div>
</div>

{{-- ═══ TRUST SECTION ════════════════════════════════════════════════════════ --}}
@if(!request()->hasAny(['search','game_id']))
<div class="border-t border-gray-900 bg-gray-950 py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-4 gap-8">
            @foreach([
                ['M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'indigo', 'Escrow protection', 'Funds held safely until you confirm receipt of the account.'],
                ['M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-.343-.03-.67-.07-1z', 'emerald', 'Verified sellers', 'Every seller goes through our identity onboarding process.'],
                ['M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.975 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118L3.98 9.132c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z', 'amber', 'Buyer reviews', 'Real reviews from verified buyers only — no fake ratings.'],
                ['M13 10V3L4 14h7v7l9-11h-7z', 'violet', 'Instant listing', 'List your account for sale in under two minutes.'],
            ] as [$path, $color, $title, $desc])
            <div class="text-center">
                <div class="w-12 h-12 rounded-2xl mx-auto mb-4 flex items-center justify-center
                            {{ $color === 'indigo'  ? 'bg-indigo-500/10'  : '' }}
                            {{ $color === 'emerald' ? 'bg-emerald-500/10' : '' }}
                            {{ $color === 'amber'   ? 'bg-amber-500/10'   : '' }}
                            {{ $color === 'violet'  ? 'bg-violet-500/10'  : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-6 h-6
                                {{ $color === 'indigo'  ? 'text-indigo-400'  : '' }}
                                {{ $color === 'emerald' ? 'text-emerald-400' : '' }}
                                {{ $color === 'amber'   ? 'text-amber-400'   : '' }}
                                {{ $color === 'violet'  ? 'text-violet-400'  : '' }}"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/>
                    </svg>
                </div>
                <div class="text-sm font-semibold text-white mb-2">{{ $title }}</div>
                <div class="text-xs text-gray-500 leading-relaxed">{{ $desc }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const filterForm   = document.getElementById('filterForm');
    const listingsArea = document.getElementById('listingsArea');
    let debounce;
    // toggleClearBtn();

    if (!filterForm || !listingsArea) return;

    function loadListings() {
        const params = new URLSearchParams(new FormData(filterForm)).toString();

        // listingsArea.style.opacity = '0.4';
        listingsArea.classList.add('opacity-0', 'translate-y-2');

        fetch("{{ route('home') }}?" + params, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.text())
        .then(html => {
            listingsArea.innerHTML = html;

            requestAnimationFrame(() => {
                listingsArea.classList.remove('opacity-0', 'translate-y-2');
            });

            history.replaceState(null, '', '?' + params);
        });
    }

window.toggleClearBtn = function () {
    const input = document.getElementById('heroSearchInput');
    const btn = document.getElementById('clearSearchBtn');

    if (input.value.trim().length > 0) {
        btn.classList.remove('hidden');
    } else {
        btn.classList.add('hidden');
    }
};


window.clearSearch = function () {
    const input = document.getElementById('heroSearchInput');
    const btn = document.getElementById('clearSearchBtn');

    input.value = '';
    btn.classList.add('hidden');
    input.focus();

    const searchHidden = filterForm.querySelector('[name="search"]');
    if (searchHidden) searchHidden.value = '';

    const gameInput = filterForm.querySelector('[name="game_id"]');
    if (gameInput) gameInput.value = '';

    document.querySelectorAll('[data-filter]').forEach(el => {
        el.classList.remove('bg-indigo-600', 'text-white');
        el.classList.add('bg-gray-900', 'text-gray-400', 'border', 'border-gray-800');
    });

    loadListings();
};

    // Hero search form — clear game_id when doing a text search
    document.getElementById('searchForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const q = document.getElementById('heroSearchInput').value;

        let hidden = filterForm.querySelector('[name="search"]');
        if (hidden) hidden.value = q;
        else {
            const h = document.createElement('input');
            h.type = 'hidden'; h.name = 'search'; h.value = q;
            filterForm.appendChild(h);
        }

        // Clear game filter when doing a text search
        const gameInput = filterForm.querySelector('[name="game_id"]');
        if (gameInput) gameInput.value = '';

        // Reset active tab UI to "All Games"
        document.querySelectorAll('[data-filter]').forEach(el => {
            el.classList.remove('bg-indigo-600', 'text-white');
            el.classList.add('bg-gray-900', 'text-gray-400', 'border', 'border-gray-800');
        });
        document.querySelector('[data-filter]').classList.add('bg-indigo-600', 'text-white');

        loadListings();
        const browse = document.getElementById('browse');
        if (window.scrollY < browse.offsetTop - 100) {
            browse.scrollIntoView({ behavior: 'smooth' });
        }
    });

    // Live search — also clear game_id
    document.getElementById('heroSearchInput').addEventListener('input', function () {
        toggleClearBtn();

        const q = this.value;
        const hidden = filterForm.querySelector('[name="search"]');
        if (hidden) hidden.value = q;

        // Clear game filter when typing
        const gameInput = filterForm.querySelector('[name="game_id"]');
        if (gameInput) gameInput.value = '';

        clearTimeout(debounce);
        debounce = setTimeout(loadListings, 300);
    });

    // Filter selects
    filterForm.querySelectorAll('select').forEach(el => {
        el.addEventListener('change', loadListings);
    });

    // Price inputs
    filterForm.querySelectorAll('input[name="min_price"], input[name="max_price"]').forEach(el => {
        el.addEventListener('change', loadListings);
    });

    // Quick search from chip click
    window.quickSearch = function (value, gameId = null) {
        // Clear text search when a chip is used as a game filter
        document.getElementById('heroSearchInput').value = '';
        toggleClearBtn();

        let searchInput = filterForm.querySelector('[name="search"]');
        if (!searchInput) {
            searchInput = document.createElement('input');
            searchInput.type = 'hidden';
            searchInput.name = 'search';
            filterForm.appendChild(searchInput);
        }
        searchInput.value = ''; // chips filter by game, not text

        if (gameId) {
            let gameInput = filterForm.querySelector('[name="game_id"]');
            if (!gameInput) {
                gameInput = document.createElement('input');
                gameInput.type = 'hidden';
                gameInput.name = 'game_id';
                filterForm.appendChild(gameInput);
            }
            gameInput.value = gameId;
        }

        clearTimeout(debounce);
        debounce = setTimeout(() => {
            loadListings();
            const browse = document.getElementById('browse');
            if (window.scrollY < browse.offsetTop - 100) {
                browse.scrollIntoView({ behavior: 'smooth' });
            }
        }, 80);
    };

    // Counter animation
    document.querySelectorAll('[data-count]').forEach(el => {
        const target = parseInt(el.dataset.count, 10);
        let v = 0;
        const step = Math.ceil(target / 24);
        const iv = setInterval(() => {
            v = Math.min(v + step, target);
            el.textContent = v.toLocaleString() + '+';
            if (v >= target) clearInterval(iv);
        }, 35);
    });

    document.querySelectorAll('[data-filter]').forEach(link => {
    link.addEventListener('click', function (e) {
        e.preventDefault();

        const url = new URL(this.href);
        history.pushState(null, '', url);

        const params = url.searchParams;

        let gameInput = filterForm.querySelector('[name="game_id"]');

        if (!gameInput) {
            gameInput = document.createElement('input');
            gameInput.type = 'hidden';
            gameInput.name = 'game_id';
            filterForm.appendChild(gameInput);
        }

        const gameId = params.get('game_id') || '';
        gameInput.value = gameId;

        // ADD THIS PART (UI update)
        document.querySelectorAll('[data-filter]').forEach(el => {
            el.classList.remove('bg-indigo-600', 'text-white');
            el.classList.add('bg-gray-900', 'text-gray-400', 'border', 'border-gray-800');
        });

        this.classList.remove('bg-gray-900', 'text-gray-400', 'border', 'border-gray-800');
        this.classList.add('bg-indigo-600', 'text-white');

        loadListings();
    });
});

});
</script>
@endpush
