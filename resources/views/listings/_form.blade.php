        {{-- Pass games data safely via a JSON script tag --}}
        <script id="games-data" type="application/json">@json($gamesData)</script>

        <div x-data="gameForm"
            data-old-game="{{ old('game_id', $listing->game_id ?? '') }}"
            data-old-rank="{{ old('rank', $listing->rank ?? '') }}"
            data-old-server="{{ old('server', $listing->server ?? '') }}">

            {{-- ── GAME INFORMATION ──────────────────────────────────── --}}
            <div class="bg-gray-900 border border-gray-800 rounded-3xl p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-9 h-9 bg-indigo-500/10 text-indigo-400 rounded-2xl flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold">Game Informatio=n</h2>
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
                            <option value="" disabled selected hidden>Select a game</option>
                            @foreach($games as $game)
                                <option value="{{ $game->id }}">
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
                        <label class="block text-sm font-medium text-gray-400 mb-2">Level</label>
                        <input type="number" name="level" value="{{ old('level', $listing->level ?? '') }}" min="1" placeholder="e.g. 120"
                            class="w-full bg-gray-800 border border-gray-700 rounded-2xl px-5 py-3.5 text-white placeholder:text-gray-600 focus:outline-none focus:border-indigo-500 transition">
                        <p class="text-xs text-gray-500 mt-1">Enter the current account level.</p>
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
                        <label class="block text-sm font-medium text-gray-400 mb-2">
                            Platform <span class="text-red-400">*</span>
                        </label>
                        <select name="platform" required
                                class="w-full bg-gray-800 border border-gray-700 rounded-2xl px-5 py-3.5 text-white focus:outline-none focus:border-indigo-500 transition">
                            <option value="" disabled selected hidden>Select platform...</option>
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

            {{-- ── LISTING DETAILS ───────────────────────────────────── --}}
            <div class="bg-gray-900 border border-gray-800 rounded-3xl p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-9 h-9 bg-emerald-500/10 text-emerald-400 rounded-2xl flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold">Listing Details</h2>
                        <p class="text-sm text-gray-500">Describe what makes this account worth buying</p>
                    </div>
                </div>

                <div class="space-y-5">

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">
                            Title <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title', $listing->title ?? '') }}" required
                            placeholder="e.g. Mythic Account | 150 Skins | All Heroes"
                            class="w-full bg-gray-800 border border-gray-700 rounded-2xl px-5 py-3.5 text-white placeholder:text-gray-500 focus:outline-none focus:border-indigo-500 transition">
                        @error('title')
                            <p class="text-red-400 text-sm mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">
                            Description <span class="text-red-400">*</span>
                        </label>
                        <textarea name="description" rows="5" required
                                placeholder="Describe the account — skins, heroes, rank history, any notable items..."
                                class="w-full bg-gray-800 border border-gray-700 rounded-2xl px-5 py-3.5 text-white placeholder:text-gray-500 focus:outline-none focus:border-indigo-500 transition resize-none">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-400 text-sm mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">
                            Price (USD) <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-5 top-1/2 -translate-y-1/2 text-2xl text-gray-400 font-light">$</span>
                            <input type="number" name="price" value="{{ old('price') }}" step="0.01" min="1" required
                                placeholder="0.00"
                                class="w-full bg-gray-800 border border-gray-700 rounded-2xl pl-11 pr-5 py-3.5 text-lg font-semibold text-white placeholder:text-gray-600 focus:outline-none focus:border-indigo-500 transition">
                        </div>
                        @error('price')
                            <p class="text-red-400 text-sm mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- ── CONTACT INFORMATION ───────────────────────────────── --}}
            <div class="bg-gray-900 border border-gray-800 rounded-3xl p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-9 h-9 bg-sky-500/10 text-sky-400 rounded-2xl flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold">Contact Information</h2>
                        <p class="text-sm text-gray-500">Buyers will reach out via these channels</p>
                    </div>
                </div>

                <div class="space-y-5">

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">
                            Telegram <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 font-medium">@</span>
                            <input type="text" name="contact_telegram" id="telegramInput" required
                                value="{{ old('contact_telegram', $listing->contact_telegram ?? '') }}" placeholder="yourusername"
                                class="w-full bg-gray-800 border border-gray-700 rounded-2xl pl-10 pr-5 py-3.5 text-white placeholder:text-gray-600 focus:outline-none focus:border-indigo-500 transition">
                        </div>
                        @error('contact_telegram')
                            <p class="text-red-400 text-sm mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">
                                WhatsApp <span class="text-gray-600 font-normal text-xs ml-1">optional</span>
                            </label>
                            <input type="text" name="contact_whatsapp" value="{{ old('contact_whatsapp') }}"
                                placeholder="+60123456789"
                                class="w-full bg-gray-800 border border-gray-700 rounded-2xl px-5 py-3.5 text-white placeholder:text-gray-600 focus:outline-none focus:border-indigo-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">
                                Discord <span class="text-gray-600 font-normal text-xs ml-1">optional</span>
                            </label>
                            <input type="text" name="contact_discord" value="{{ old('contact_discord') }}"
                                placeholder="username#0000"
                                class="w-full bg-gray-800 border border-gray-700 rounded-2xl px-5 py-3.5 text-white placeholder:text-gray-600 focus:outline-none focus:border-indigo-500 transition">
                        </div>
                    </div>

                </div>
            </div>

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
        </div> {{-- ✅ CLOSE x-data --}}
