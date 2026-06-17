<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(Listing $listing)
    {
        if ($listing->user_id === auth()->id()) {
            return back()->with('error', 'You cannot report your own listing.');
        }

        return view('reports.create', compact('listing'));
    }

    public function store(Request $request, Listing $listing)
    {
        if ($listing->user_id === auth()->id()) {
            return back()->with('error', 'You cannot report your own listing.');
        }

        // Check already reported
        $existing = Report::where('listing_id', $listing->id)
            ->where('reporter_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return back()->with('error', 'You have already reported this listing.');
        }

        $request->validate([
            'reason'  => 'required|in:scam,fake_screenshots,wrong_info,duplicate,inappropriate,other',
            'details' => 'nullable|string|max:500',
        ]);

        // Save report
        // Report::create([
        //     'listing_id'  => $listing->id,
        //     'reporter_id' => auth()->id(),
        //     'reason'      => $request->reason,
        //     'details'     => $request->details,
        // ]);
        Report::create([
            'listing_id'  => $listing->id,
            'reporter_id' => auth()->id(),
            'reason'      => $request->reason,
            'details'     => $request->details,
            'status'      => 'pending',
        ]);

        // Auto-flag logic
        $reportsCount = Report::where('listing_id', $listing->id)
            ->where('status', 'pending')
            ->count();

        if ($reportsCount >= 3 && !$listing->is_flagged) {

            if ($reportsCount >= 5) {
                $reason = "HIGH RISK: Reported {$reportsCount} times";
            } else {
                $reason = "Reported {$reportsCount} times";
            }

            $listing->update([
                'is_flagged'  => true,
                'flag_reason' => $reason,
                'flagged_at'  => now(),
            ]);
        }

        return back()->with('success', 'Report submitted. Our team will review within 24 hours.');
    }
}
