@extends('layouts.app')
@section('title', 'Post a Listing — GameTradeHub')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10">

    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight mb-2">Post a Listing</h1>
        <p class="text-gray-400 text-sm">List your account for sale. Buyers contact you directly via your chosen channels.</p>
    </div>

    @if ($errors->any())
        <div class="text-red-400 bg-red-900/20 border border-red-800 p-4 rounded-2xl mb-6">
            <ul class="space-y-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('listings.store') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf

        @include('listings._form', [
                'listing' => null,
                'games' => $games,
                'gamesData' => $gamesData
            ])


        {{-- Submit --}}
        <div class="flex items-center justify-between pt-4">
            <a href="{{ route('dashboard') }}"
            class="flex items-center gap-2 text-gray-400 hover:text-white transition text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Cancel
            </a>
            <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-500 active:scale-95 text-white font-semibold
                        px-10 py-4 rounded-2xl transition-all flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Post Listing
            </button>
        </div>
    </form>
</div>
@endsection
{{-- No @push('scripts') needed — everything is handled by app.js --}}
