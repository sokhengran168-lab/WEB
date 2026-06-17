{{--
    resources/views/listings/_card.blade.php
    Reusable listing card. Used by index, show (more/similar), and seller pages.
    Variables: $listing (App\Models\Listing)
--}}
<a href="{{ route('listings.show', $listing) }}"
   class="group bg-gray-900 border border-gray-800 hover:border-indigo-500/50
          rounded-xl overflow-hidden transition flex flex-col">

    {{-- Thumbnail --}}
    <div class="h-36 bg-gray-800 overflow-hidden flex-shrink-0">
        @if($listing->firstImage)
            <img src="{{ $listing->firstImage->url }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
        @else
            <div class="w-full h-full flex items-center justify-center text-4xl text-gray-600">
                🎮
            </div>
        @endif
    </div>

    {{-- Body --}}
    <div class="p-3 flex flex-col gap-1 flex-1">

        {{-- Game badge + platform --}}
        <div class="flex items-center gap-2">
            <span class="text-xs bg-indigo-600/20 text-indigo-400 px-2 py-0.5 rounded-full">
                {{ $listing->game->name }}
            </span>
            <span class="text-xs text-gray-600">{{ $listing->platform }}</span>
        </div>

        {{-- Title --}}
        <div class="text-sm font-semibold leading-snug line-clamp-2 group-hover:text-indigo-300 transition">
            {{ $listing->title }}
        </div>

        {{-- Rank / Level --}}
        @if($listing->rank || $listing->level)
        <div class="flex items-center gap-2 text-xs text-gray-500">
            @if($listing->rank)
                <span>{{ $listing->rank }}</span>
            @endif
            @if($listing->rank && $listing->level)
                <span>·</span>
            @endif
            @if($listing->level)
                <span>Lv. {{ $listing->level }}</span>
            @endif
        </div>
        @endif

        {{-- Spacer --}}
        <div class="flex-1"></div>

        {{-- Footer: price + seller --}}
        <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-800">
            <span class="text-base font-bold text-green-400">
                ${{ number_format($listing->price, 2) }}
            </span>
            <div class="flex items-center gap-1.5 text-xs text-gray-500">
                <div class="w-5 h-5 bg-indigo-600 rounded-full flex items-center
                            justify-center text-xs font-bold text-white flex-shrink-0">
                    {{ strtoupper(substr($listing->seller->name, 0, 1)) }}
                </div>
                <span class="truncate max-w-[80px]">{{ $listing->seller->name }}</span>
                @if($listing->seller->is_verified)
                    <span class="text-sky-400">✓</span>
                @endif
            </div>
        </div>

    </div>
</a>
