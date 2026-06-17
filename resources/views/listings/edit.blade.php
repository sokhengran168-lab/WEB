@extends('layouts.app')
@section('title', 'Edit Listing')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">

    <h1 class="text-2xl font-bold mb-1">✏️ Edit Listing</h1>
    <p class="text-gray-400 text-sm mb-6">
        Editing will re-submit your listing for admin review.
    </p>

    <form method="POST" action="{{ route('listings.update', $listing) }}"
          enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        @include('listings._form', [
                'listing' => $listing,
                'games' => $games,
                'gamesData' => $gamesData
            ])


        {{-- ── Actions ── --}}
        <div class="flex items-center justify-between pt-6">
            <a href="{{ route('dashboard') }}"
               class="text-gray-400 hover:text-red-400 text-sm transition">
                ← Cancel
            </a>
            <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-500 text-white px-6 py-2.5
                           rounded-xl font-semibold text-sm transition">
                Save &amp; Resubmit for Review →
            </button>
        </div>

    </form>
</div>
@endsection
