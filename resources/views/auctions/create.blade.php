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

        @include('auctions._form')

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
