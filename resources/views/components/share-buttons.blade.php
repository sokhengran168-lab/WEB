@props(['listing'])

<div x-data="shareButtons('{{ route('listings.share', $listing) }}', '{{ $listing->isAuction() ? route('auctions.show', $listing) : route('listings.show', $listing) }}')"
     @click.outside="open = false"
     class="relative inline-block">

    {{-- Trigger button --}}
    <button @click="open = !open"
            class="flex items-center justify-center w-9 h-9
                   bg-indigo-500/10 hover:bg-indigo-500/20
                   border border-indigo-500/20 text-indigo-400
                   rounded-lg transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round">
            <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/>
            <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
            <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
        </svg>
    </button>

    {{-- Dropdown menu --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute z-50 mt-2 w-44 origin-top-left left-0
                bg-gray-900 border border-gray-700 rounded-xl shadow-xl p-1.5">

        <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 px-2.5 pt-1 pb-1.5">
            Share via
        </p>

        {{-- WhatsApp --}}
        <button @click="share('whatsapp')"
                class="flex items-center gap-2.5 w-full px-2.5 py-1.5
                       text-xs text-green-400 hover:bg-green-500/10
                       rounded-lg transition text-left">
            <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="currentColor">...</svg>
            WhatsApp
        </button>

        {{-- Telegram --}}
        <button @click="share('telegram')"
                class="flex items-center gap-2.5 w-full px-2.5 py-1.5
                       text-xs text-sky-400 hover:bg-sky-500/10
                       rounded-lg transition text-left">
            <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="currentColor">...</svg>
            Telegram
        </button>

        {{-- Facebook --}}
        <button @click="share('facebook')"
                class="flex items-center gap-2.5 w-full px-2.5 py-1.5
                       text-xs text-blue-400 hover:bg-blue-500/10
                       rounded-lg transition text-left">
            <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="currentColor">...</svg>
            Facebook
        </button>

        {{-- Twitter/X --}}
        <button @click="share('twitter')"
                class="flex items-center gap-2.5 w-full px-2.5 py-1.5
                       text-xs text-gray-300 hover:bg-gray-500/10
                       rounded-lg transition text-left">
            <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="currentColor">...</svg>
            X / Twitter
        </button>

        <div class="border-t border-gray-800 my-1"></div>

        {{-- Copy Link --}}
        <button @click="copyLink()"
                class="flex items-center gap-2.5 w-full px-2.5 py-1.5
                       text-xs text-indigo-400 hover:bg-indigo-500/10
                       rounded-lg transition text-left">
            <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
            </svg>
            <span x-text="copied ? 'Copied!' : 'Copy link'"></span>
        </button>

    </div>

    @if($listing->share_count > 0)
    <span class="text-xs text-gray-600 ml-2">{{ $listing->share_count }}</span>
    @endif

</div>

@push('scripts')
<script>
function shareButtons(shareRoute, listingUrl) {
    return {
        open: false,
        copied: false,

        async share(platform) {
            this.open = false;
            try {
                const response = await fetch(shareRoute, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ platform })
                });
                const data = await response.json();
                if (data.url) window.open(data.url, '_blank', 'width=600,height=400');
            } catch (error) {
                console.error('Share failed:', error);
            }
        },

        async copyLink() {
            this.open = false;
            try {
                const response = await fetch(shareRoute, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ platform: 'copy' })
                });
                const data = await response.json();
                await navigator.clipboard.writeText(data.url || listingUrl);
            } catch {
                await navigator.clipboard.writeText(listingUrl);
            }
            this.copied = true;
            setTimeout(() => this.copied = false, 2000);
        }
    }
}
</script>
@endpush
