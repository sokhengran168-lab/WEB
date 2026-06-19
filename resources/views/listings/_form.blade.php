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
                    <option value="" disabled selected hidden>Select a game</option>
                    @foreach($games as $game)
                        <option value="{{ $game->id }}">{{ $game->name }} — {{ $game->category }}</option>
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
                <input type="number" name="level"
                       value="{{ old('level', $listing->level ?? '') }}"
                       min="1" placeholder="e.g. 120"
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
                    <option value="Mobile"  {{ old('platform', $listing->platform ?? '') === 'Mobile'  ? 'selected' : '' }}>Mobile</option>
                    <option value="PC"      {{ old('platform', $listing->platform ?? '') === 'PC'      ? 'selected' : '' }}>PC</option>
                    <option value="Console" {{ old('platform', $listing->platform ?? '') === 'Console' ? 'selected' : '' }}>Console</option>
                </select>
                @error('platform')
                    <p class="text-red-400 text-sm mt-1.5">{{ $message }}</p>
                @enderror
            </div>

        </div>
    </div>

    {{-- ── LISTING DETAILS ───────────────────────────────────── --}}
    <div class="bg-gray-900 border border-gray-800 rounded-3xl p-8 mt-5">
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
                <input type="text" name="title"
                       value="{{ old('title', $listing->title ?? '') }}"
                       required
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
                          class="w-full bg-gray-800 border border-gray-700 rounded-2xl px-5 py-3.5 text-white placeholder:text-gray-500 focus:outline-none focus:border-indigo-500 transition resize-none">{{ old('description', $listing->description ?? '') }}</textarea>
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
                    <input type="number" name="price"
                           value="{{ old('price', $listing->price ?? '') }}"
                           step="0.01" min="1" required placeholder="0.00"
                           class="w-full bg-gray-800 border border-gray-700 rounded-2xl pl-11 pr-5 py-3.5 text-lg font-semibold text-white placeholder:text-gray-600 focus:outline-none focus:border-indigo-500 transition">
                </div>
                @error('price')
                    <p class="text-red-400 text-sm mt-1.5">{{ $message }}</p>
                @enderror
            </div>

        </div>
    </div>

    {{-- ── CONTACT INFORMATION ───────────────────────────────── --}}
    <div class="bg-gray-900 border border-gray-800 rounded-3xl p-8 mt-5">
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
                           value="{{ old('contact_telegram', $listing->contact_telegram ?? '') }}"
                           placeholder="yourusername"
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
                    <input type="text" name="contact_whatsapp"
                           value="{{ old('contact_whatsapp', $listing->contact_whatsapp ?? '') }}"
                           placeholder="+60123456789"
                           class="w-full bg-gray-800 border border-gray-700 rounded-2xl px-5 py-3.5 text-white placeholder:text-gray-600 focus:outline-none focus:border-indigo-500 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">
                        Discord <span class="text-gray-600 font-normal text-xs ml-1">optional</span>
                    </label>
                    <input type="text" name="contact_discord"
                           value="{{ old('contact_discord', $listing->contact_discord ?? '') }}"
                           placeholder="username#0000"
                           class="w-full bg-gray-800 border border-gray-700 rounded-2xl px-5 py-3.5 text-white placeholder:text-gray-600 focus:outline-none focus:border-indigo-500 transition">
                </div>
            </div>

        </div>
    </div>

    {{-- ── IMAGES ────────────────────────────────────────────── --}}
    <div class="bg-gray-900 border border-gray-800 rounded-3xl p-8 mt-5">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-9 h-9 bg-violet-500/10 text-violet-400 rounded-2xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-semibold">Screenshots &amp; Proof</h2>
                <p class="text-sm text-gray-500">Upload clear screenshots — rank, skins, inventory</p>
            </div>
        </div>

        {{-- ── EXISTING IMAGES (edit mode only) ──────────────── --}}
        {{-- Uses the same class names expected by edit.js: existingImages, existing-image-item, delete-checkbox, delete-toggle --}}
        @if(isset($listing) && $listing && $listing->images->count() > 0)
        <div class="mb-6">
            <p class="text-sm font-medium text-gray-400 mb-3">
                Current images
                <span class="text-gray-600 font-normal ml-1">— click Remove to mark for deletion</span>
            </p>

            <div id="existingImages" class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                @foreach($listing->images as $image)
                <div class="existing-image-item relative group">

                    {{-- Hidden checkbox — checked = delete this image on submit --}}
                    <input type="checkbox"
                           class="delete-checkbox hidden"
                           name="delete_images[]"
                           value="{{ $image->id }}">

                    {{-- Image --}}
                    <img src="{{ $image->image_path }}"
                         alt="Listing image"
                         onclick="openExistingModal('{{ $image->image_path }}')"
                         class="w-full aspect-square object-cover rounded-2xl border border-gray-700
                                transition-all duration-200 cursor-zoom-in">

                    {{-- Remove / Undo button — edit.js toggles its state --}}
                    <button type="button"
                            class="delete-toggle
                                   absolute top-2 right-2
                                   opacity-0 hover:opacity-100
                                   bg-black/60 hover:bg-red-600/90
                                   text-white text-xs font-semibold
                                   px-2.5 py-1 rounded-lg
                                   transition-all duration-150">
                        Remove
                    </button>

                </div>
                @endforeach
            </div>

            <p class="text-xs text-gray-600 mt-3">
                {{ $listing->images->count() }} image{{ $listing->images->count() !== 1 ? 's' : '' }} uploaded.
                Marked images are deleted when you save.
            </p>
        </div>

        <div class="border-t border-gray-800 mb-6"></div>
        <p class="text-sm font-medium text-gray-400 mb-3">
            Add more images
            <span class="text-gray-600 font-normal text-xs ml-1">optional</span>
        </p>
        @endif

        {{-- Image modal (used by existing image zoom) --}}
        <div id="imageModal"
             class="fixed inset-0 bg-black/80 hidden items-center justify-center z-50"
             onclick="if(event.target===this) closeExistingModal()">
            <img id="modalImage"
                 class="max-w-[90vw] max-h-[90vh] rounded-2xl shadow-2xl scale-95 transition-transform duration-200">
            <button id="closeModal"
                    onclick="closeExistingModal()"
                    class="absolute top-5 right-5 w-9 h-9 bg-gray-800 hover:bg-gray-700
                           rounded-full flex items-center justify-center text-white transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Drop zone — IDs match upload.js expectations --}}
        <div id="dropZone"
             class="border-2 border-dashed border-gray-700 hover:border-indigo-500 rounded-3xl
                    text-center transition cursor-pointer relative overflow-hidden min-h-[200px]">

            <div id="uploadPlaceholder" class="flex flex-col items-center justify-center h-full py-10 px-6">
                <div class="mx-auto w-14 h-14 bg-gray-800 rounded-2xl flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-gray-400" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                </div>
                <p class="font-medium text-gray-300 mb-1">Drop images here or click to upload</p>
                <p class="text-sm text-gray-500">JPG, PNG, WEBP · Max 10MB · Up to 8 images</p>
            </div>

            {{-- upload.js renders the preview here --}}
            <div id="imagePreview" class="absolute inset-0 hidden bg-gray-900"></div>

            <input type="file" name="images[]"
                    id="imageInput"
                    multiple
                    accept="image/jpeg,image/png,image/webp"
                    class="block w-full mt-4 text-sm text-white">

        </div>

        @error('images')
            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
        @enderror
        @error('images.*')
            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
        @enderror

    </div>{{-- end images card --}}

</div>{{-- end x-data --}}

{{-- Existing-image modal helpers (separate from upload.js modal) --}}
<script>
function openExistingModal(src) {
    const modal = document.getElementById('imageModal');
    const img   = document.getElementById('modalImage');
    img.src = src;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => { img.classList.remove('scale-95'); img.classList.add('scale-100'); }, 30);
}
function closeExistingModal() {
    const modal = document.getElementById('imageModal');
    const img   = document.getElementById('modalImage');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    img.classList.remove('scale-100');
    img.classList.add('scale-95');
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeExistingModal(); });
</script>
