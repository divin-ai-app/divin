<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DisputeStatus;
use App\Enums\ProfileStatus;
use App\Http\Controllers\Controller;
use App\Models\Dispute;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Dispute queue (plan §4 flow 5). Public intake is ProfileController's
 * report()/submitReport() — no login required to file one. Staff resolve
 * as CORRECTED (they've since edited the profile directly), REMOVED
 * (unpublishes it), or REJECTED (reason required either way, for the audit trail).
 */
class DisputeController extends Controller
{
    public function index(string $locale): View
    {
        $disputes = Dispute::query()
            ->with('profile')
            ->whereIn('status', [DisputeStatus::Open, DisputeStatus::InReview])
            ->latest()
            ->paginate(20);

        return view('admin.disputes.index', compact('disputes'));
    }

    public function resolve(string $locale, Dispute $dispute, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::enum(DisputeStatus::class)->only([
                DisputeStatus::Corrected, DisputeStatus::Removed, DisputeStatus::Rejected,
            ])],
            'resolution_notes' => ['required', 'string', 'max:2000'],
        ]);

        $dispute->update([
            'status' => $data['status'],
            'resolution_notes' => $data['resolution_notes'],
            'resolved_by_staff_id' => Auth::id(),
            'resolved_at' => now(),
        ]);

        if ($data['status'] === DisputeStatus::Removed->value) {
            $dispute->profile->update(['status' => ProfileStatus::Removed]);
        }

        return back()->with('status', 'Dispute resolved.');
    }
}
