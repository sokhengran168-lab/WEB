@extends('layouts.app')
@section('title', 'Create Auction')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">

    @if ($errors->any())
        <div class="text-red-400 bg-red-900/20 border border-red-800 p-4 rounded-2xl mb-6">
            <ul class="space-y-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight mb-2">Create New Auction</h1>
        <p class="text-gray-400">
            List your account securely. Highest bidder wins — payment held in escrow until delivery.
        </p>
    </div>

    <form method="POST" action="{{ route('auctions.store') }}" enctype="multipart/form-data" class="space-y-8">
        @csrf

        {{-- Pass games data safely via a JSON script tag --}}
{{-- Pass games data safely via a JSON script tag --}}
<script id="games-data" type="application/json">@json($gamesData)</script>

<div x-data="gameForm"
     data-old-game="{{ old('game_id') }}"
     data-old-rank="{{ old('rank') }}"
     data-old-server="{{ old('server') }}">

            {{-- Game Information --}}
            <div class="bg-gray-900 border border-gray-800 rounded-3xl p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-9 h-9 bg-indigo-500/10 text-indigo-400 rounded-2xl flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold">Game Information</h2>
                        <p class="text-sm text-gray-500">Tell buyers what they're getting</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-400 mb-2">
                            Game <span class="text-red-400">*</span>
                        </label>
                        <select name="game_id"
                                x-model="selectedGame"
                                @change="updateOptions(true)"
                                required
                                class="w-full bg-gray-800 border border-gray-700 rounded-2xl px-5 py-3.5 text-white focus:outline-none focus:border-indigo-500 transition">
                            <option value="" selected hidden>Select a game...</option>
                            @foreach($games as $game)
                                <option value="{{ $game->id }}" {{ old('game_id') == $game->id ? 'selected' : '' }}>
                                    {{ $game->name }} — {{ $game->category }}
                                </option>
                            @endforeach
                        </select>
                        @error('game_id')
                            <p class="text-red-400 text-sm mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Rank / Tier</label>
                        <select name="rank"
                                x-model="selectedRank"
                                :disabled="ranks.length === 0"
                                x-html="rankOptions"
                                class="w-full bg-gray-800 border border-gray-700 rounded-2xl px-5 py-3.5 text-white focus:outline-none focus:border-indigo-500 transition disabled:opacity-40">
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Server</label>
                        <select name="server"
                                x-model="selectedServer"
                                :disabled="servers.length === 0"
                                x-html="serverOptions"
                                class="w-full bg-gray-800 border border-gray-700 rounded-2xl px-5 py-3.5 text-white focus:outline-none focus:border-indigo-500 transition disabled:opacity-40">
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Level</label>
                        <input type="number" name="level" min="1" value="{{ old('level') }}"
                               placeholder="e.g. 100"
                               class="w-full bg-gray-800 border border-gray-700 rounded-2xl px-5 py-3.5 text-white placeholder:text-gray-600 focus:outline-none focus:border-indigo-500 transition">
                        <p class="text-xs text-gray-500 mt-1">Enter the current account level.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">
                            Platform <span class="text-red-400">*</span>
                        </label>
                        <select name="platform" required
                                class="w-full bg-gray-800 border border-gray-700 rounded-2xl px-5 py-3.5 text-white focus:outline-none focus:border-indigo-500 transition">
                            <option value="" hidden>Select platform...</option>
                            <option value="Mobile"  {{ old('platform') === 'Mobile'  ? 'selected' : '' }}>Mobile</option>
                            <option value="PC"      {{ old('platform') === 'PC'      ? 'selected' : '' }}>PC</option>
                            <option value="Console" {{ old('platform') === 'Console' ? 'selected' : '' }}>Console</option>
                        </select>
                        @error('platform')
                            <p class="text-red-400 text-sm mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- Listing Details --}}
            <div class="bg-gray-900 border border-gray-800 rounded-3xl p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-9 h-9 bg-emerald-500/10 text-emerald-400 rounded-2xl flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold">Listing Details</h2>
                        <p class="text-sm text-gray-500">Give your auction a compelling title</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">
                        Auction Title <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           placeholder="Mythic Account • 150+ Skins • Rare Collections"
                           class="w-full bg-gray-800 border border-gray-700 rounded-2xl px-5 py-3.5 text-white placeholder:text-gray-500 focus:outline-none focus:border-indigo-500 transition">
                    @error('title')
                        <p class="text-red-400 text-sm mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Auction Settings --}}
            <div class="bg-gray-900 border border-gray-800 rounded-3xl p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-9 h-9 bg-amber-500/10 text-amber-400 rounded-2xl flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold">Auction Settings</h2>
                        <p class="text-sm text-gray-500">Set bidding rules and duration</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">
                            Starting Price (USD) <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-5 top-1/2 -translate-y-1/2 text-2xl text-gray-400 font-light">$</span>
                            <input type="number" name="starting_price" value="{{ old('starting_price') }}"
                                step="0.01" min="1" required placeholder="0.00"
                                class="w-full bg-gray-800 border border-gray-700 rounded-2xl pl-11 pr-5 py-3.5 text-lg font-semibold text-white placeholder:text-gray-600 focus:outline-none focus:border-indigo-500 transition">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">The minimum amount bidding starts at.</p>
                        @error('starting_price')
                            <p class="text-red-400 text-sm mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Minimum Bid Increment (USD)</label>
                        <div class="relative">
                            <span class="absolute left-5 top-1/2 -translate-y-1/2 text-2xl text-gray-400 font-light">$</span>
                            <input type="number" name="bid_increment" value="{{ old('bid_increment', 1) }}" step="0.5" min="0.5"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-2xl pl-11 pr-5 py-3.5 text-lg font-semibold text-white focus:outline-none focus:border-indigo-500 transition">
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-400 mb-2">
                            Auction Ends <span class="text-red-400">*</span>
                        </label>
                        <input type="datetime-local" name="auction_ends_at" id="auctionEndsAt" required
                               value="{{ old('auction_ends_at') }}"
                               class="w-full bg-gray-800 border border-gray-700 rounded-2xl px-5 py-3.5 text-white focus:outline-none focus:border-indigo-500 transition">
                        <p class="text-xs text-gray-500 mt-2">Minimum 1 hour from now. Recommended: 24–72 hours.</p>
                        @error('auction_ends_at')
                            <p class="text-red-400 text-sm mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

        </div>{{-- end x-data --}}

        {{-- Image Modal --}}
        <div id="imageModal" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-50">
            <img id="modalImage" class="max-w-[90%] max-h-[90%] rounded-xl shadow-2xl transition-transform duration-200 scale-95">
            <button id="closeModal" class="absolute top-5 right-5 text-white text-3xl leading-none">✕</button>
        </div>

        {{-- Drop Zone --}}
        <div id="dropZone"
             class="border-2 border-dashed border-gray-700 hover:border-indigo-500 rounded-3xl
                    text-center transition cursor-pointer relative overflow-hidden min-h-[300px]">

            <div id="uploadPlaceholder" class="flex flex-col items-center justify-center h-full py-10 px-6">
                <div class="mx-auto w-16 h-16 bg-gray-800 rounded-2xl flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5V7.5A2.5 2.5 0 015.5 5h13A2.5 2.5 0 0121 7.5v9a2.5 2.5 0 01-2.5 2.5h-13A2.5 2.5 0 013 16.5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 11l2.5 3L14 10l4 5H6l2-4z" />
                    </svg>
                </div>
                <p class="font-medium mb-1">Drop images here or click to upload</p>
                <p class="text-sm text-gray-500">JPG, PNG, WEBP • Max 5MB • Max 8 images</p>
            </div>

            <div id="imagePreview" class="absolute inset-0 hidden bg-gray-900"></div>
            <input type="file" name="images[]" id="imageInput" multiple accept="image/*" class="hidden">
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-between pt-6">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-2 text-gray-400 hover:text-white transition">
                ← Cancel
            </a>
            <button type="submit"
                    class="bg-gradient-to-r from-yellow-400 to-amber-500 hover:from-yellow-300 hover:to-amber-400
                           text-black font-bold px-10 py-4 rounded-2xl flex items-center gap-3 transition-all active:scale-95">
                Submit Auction for Review →
            </button>
        </div>

    </form>
</div>
@endsection
{{-- No @push('scripts') needed — everything is handled by app.js --}}
