@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold tracking-tight">Dashboard</h1>
    <span class="text-sm text-gray-500">{{ now()->format('l, M d Y') }}</span>
</div>

{{-- Stats --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    @php
        $cards = [
            [
                'value' => $stats['total_users'],
                'sub'   => $stats['new_users_today'] . ' new today',
                'label' => 'Total Users',
                'color' => 'indigo',
                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>',
            ],
            [
                'value' => $stats['active_listings'],
                'sub'   => 'listings live now',
                'label' => 'Active Listings',
                'color' => 'green',
                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016 2.993 2.993 0 0 0 2.25-1.016 3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z"/>',
            ],
            [
                'value' => $stats['flagged_listings'],
                'sub'   => 'need review',
                'label' => 'Flagged',
                'color' => 'yellow',
                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 9m0 6V9"/>',
            ],
            [
                'value' => $stats['open_reports'],
                'sub'   => 'pending review',
                'label' => 'Open Reports',
                'color' => 'red',
                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/>',
            ],
            [
                'value' => $stats['open_disputes'],
                'sub'   => 'awaiting resolution',
                'label' => 'Disputes',
                'color' => 'orange',
                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0 0 12 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 0 1-2.031.352 5.988 5.988 0 0 1-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.97Zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 0 1-2.031.352 5.989 5.989 0 0 1-2.031-.352c-.483-.174-.711-.703-.59-1.202L5.25 4.97Z"/>',
            ],
            [
                'value' => '$' . number_format($stats['total_revenue'], 2),
                'sub'   => '$' . number_format($stats['revenue_today'], 2) . ' today',
                'label' => 'Revenue',
                'color' => 'cyan',
                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/>',
            ],
        ];
    @endphp

    @foreach($cards as $card)
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 bg-{{ $card['color'] }}-500/10">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor"
                 class="w-5 h-5 text-{{ $card['color'] }}-400">
                {!! $card['icon'] !!}
            </svg>
        </div>
        <div>
            <div class="text-2xl font-bold text-{{ $card['color'] }}-400">{{ $card['value'] }}</div>
            <div class="text-xs text-gray-500">{{ $card['label'] }}</div>
            <div class="text-xs text-gray-600">{{ $card['sub'] }}</div>
        </div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-2 gap-5 mb-5">

    {{-- Recent Reports --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-800 flex items-center justify-between">
            <h2 class="font-semibold text-sm flex items-center gap-2">
                Recent Reports
                @if($stats['open_reports'] > 0)
                <span class="bg-red-500/20 text-red-400 text-xs px-2 py-0.5 rounded-full font-medium">
                    {{ $stats['open_reports'] }}
                </span>
                @endif
            </h2>
            <a href="{{ route('admin.reports.index') }}"
               class="text-xs text-sky-400 hover:text-sky-300 transition-colors">View all</a>
        </div>
        @forelse($recent_reports as $report)
        <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-800/50 last:border-0">
            <div class="flex-1 min-w-0">
                <div class="text-sm font-medium truncate text-gray-100">
                    {{ $report->listing->title ?? '—' }}
                </div>
                <div class="text-xs text-gray-500 mt-0.5">
                    {{ $report->reporter->name }}
                    <span class="text-gray-700 mx-1">·</span>
                    {{ ucfirst(str_replace('_', ' ', $report->reason)) }}
                </div>
            </div>
            <a href="{{ route('admin.reports.index') }}"
               class="text-xs text-gray-400 bg-gray-800 hover:bg-gray-700
                      px-2.5 py-1 rounded-lg transition-colors flex-shrink-0">
                Review
            </a>
        </div>
        @empty
        <div class="px-4 py-8 text-center text-gray-600 text-sm">
            No pending reports
        </div>
        @endforelse
    </div>

    {{-- Open Disputes --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-800 flex items-center justify-between">
            <h2 class="font-semibold text-sm flex items-center gap-2">
                Open Disputes
                @if($stats['open_disputes'] > 0)
                <span class="bg-red-500/20 text-red-400 text-xs px-2 py-0.5 rounded-full font-medium">
                    {{ $stats['open_disputes'] }}
                </span>
                @endif
            </h2>
            <a href="{{ route('admin.transactions.index', ['status' => 'disputed']) }}"
               class="text-xs text-sky-400 hover:text-sky-300 transition-colors">View all</a>
        </div>
        @forelse($recent_disputes as $txn)
        <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-800/50 last:border-0">
            <div class="flex-1 min-w-0">
                <div class="text-sm font-medium truncate text-gray-100">
                    {{ $txn->listing->title ?? '—' }}
                </div>
                <div class="text-xs text-gray-500 mt-0.5">
                    {{ $txn->buyer->name }}
                    <span class="text-gray-700 mx-1">vs</span>
                    {{ $txn->seller->name }}
                </div>
            </div>
            <span class="text-green-400 font-semibold text-sm flex-shrink-0 tabular-nums">
                ${{ number_format($txn->amount, 2) }}
            </span>
        </div>
        @empty
        <div class="px-4 py-8 text-center text-gray-600 text-sm">
            No open disputes
        </div>
        @endforelse
    </div>

</div>

{{-- Flagged Listings --}}
<div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
    <div class="px-4 py-3 border-b border-gray-800 flex items-center justify-between">
        <h2 class="font-semibold text-sm flex items-center gap-2">
            Auto-Flagged Listings
            @if($stats['flagged_listings'] > 0)
            <span class="bg-yellow-500/20 text-yellow-400 text-xs px-2 py-0.5 rounded-full font-medium">
                {{ $stats['flagged_listings'] }}
            </span>
            @endif
        </h2>
        <a href="{{ route('admin.listings.index', ['filter' => 'flagged']) }}"
           class="text-xs text-sky-400 hover:text-sky-300 transition-colors">View all</a>
    </div>
    @forelse($flagged_listings as $listing)
    <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-800/50 last:border-0">
        <div class="flex-1 min-w-0">
            <div class="text-sm font-medium truncate text-gray-100">
                {{ $listing->title }}
            </div>
            <div class="text-xs text-red-400/80 mt-0.5">
                {{ $listing->flag_reason }}
            </div>
        </div>
        <span class="text-green-400 font-semibold text-sm flex-shrink-0 tabular-nums">
            ${{ number_format($listing->price, 2) }}
        </span>
        <div class="flex gap-2 flex-shrink-0">
            <a href="{{ route('admin.listings.show', $listing) }}"
               class="text-xs text-gray-400 bg-gray-800 hover:bg-gray-700
                      px-2.5 py-1 rounded-lg transition-colors">
                Review
            </a>
            <form method="POST" action="{{ route('admin.listings.unflag', $listing) }}">
                @csrf @method('PATCH')
                <button class="text-xs bg-green-600/10 hover:bg-green-600/20
                               text-green-400 px-2.5 py-1 rounded-lg transition-colors">
                    Clear
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="px-4 py-8 text-center text-gray-600 text-sm">
        No flagged listings
    </div>
    @endforelse
</div>

@endsection
