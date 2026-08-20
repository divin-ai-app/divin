<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ClaimRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\ClaimRequest;
use App\Support\ClaimGranter;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Claim review queue (plan §4 flow 4). Exists mainly for the
 * document-upload fallback — claims a public_email OTP could verify never
 * land here, they resolve themselves in ClaimController::verifyOtp.
 */
class ClaimController extends Controller
{
    public function index(string $locale): View
    {
        $claimRequests = ClaimRequest::query()
            ->with(['profile', 'requester'])
            ->whereIn('status', [ClaimRequestStatus::Submitted, ClaimRequestStatus::AwaitingVerification])
            ->latest()
            ->paginate(20);

        return view('admin.claims.index', compact('claimRequests'));
    }

    public function approve(string $locale, ClaimRequest $claimRequest): RedirectResponse
    {
        ClaimGranter::grant($claimRequest, ClaimRequestStatus::Approved, 'admin_approved', Auth::id());

        return back()->with('status', "Claim approved — {$claimRequest->profile->name} is now marked claimed.");
    }

    public function reject(string $locale, ClaimRequest $claimRequest, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'review_notes' => ['required', 'string', 'max:2000'],
        ]);

        $claimRequest->update([
            'status' => ClaimRequestStatus::Rejected,
            'reviewed_by_staff_id' => Auth::id(),
            'review_notes' => $data['review_notes'],
        ]);

        $claimRequest->events()->create([
            'actor_user_id' => Auth::id(),
            'action' => 'admin_rejected',
            'metadata' => ['notes' => $data['review_notes']],
        ]);

        return back()->with('status', 'Claim rejected.');
    }
}
