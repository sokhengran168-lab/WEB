@extends('layouts.app')
@section('title', 'Edit Auction')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">

    <h1 class="text-2xl font-bold mb-1">✏️ Edit Auction</h1>
    <p class="text-gray-400 text-sm mb-6">
        Editing will re-submit your auction for admin review.
    </p>

    <form method="POST"
        action="{{ route('auctions.update', $listing) }}"
        enctype="multipart/form-data">

        @csrf
        @method('PATCH')

         @include('auctions._form', ['auction' => $listing])

        {{-- Submit --}}
        <div class="flex items-start justify-between pt-6">

            {{-- Cancel --}}
            <a href="{{ route('dashboard') }}"
            class="text-gray-400 hover:text-white text-sm transition">
                ← Cancel
            </a>

            {{-- Right side --}}
            <div class="flex flex-col items-end">

                <button type="submit"
                        class="bg-yellow-500 hover:bg-yellow-400 text-black px-6 py-2.5
                            rounded-xl font-bold text-sm transition">
                    Update Auction →
                </button>

                <div class="text-xs text-gray-500 mt-2">
                    Changes will be applied immediately.
                </div>

            </div>

        </div>
    </form>
</div>
@endsection
