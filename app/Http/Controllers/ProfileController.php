<?php

namespace App\Http\Controllers;

use App\Enums\DisputeStatus;
use App\Enums\DisputeType;
use App\Enums\ProfileStatus;
use App\Models\BusinessProfile;
use App\Support\SchemaOrgBuilder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    // See MarketingController's class doc for why $locale is always the
    // first parameter on locale-scoped actions — same positional-binding
    // reasoning applies here.
    public function show(string $locale, BusinessProfile $profile): View
    {
        if ($profile->status !== ProfileStatus::Published) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $profile->load(['services', 'images']);

        return view('marketing.profile.show', [
            'profile' => $profile,
            'schema' => SchemaOrgBuilder::forProfile($profile),
        ]);
    }

    /**
     * Public dispute intake (plan §4 flow 5) — no login required, anyone
     * can flag a profile. Lands in the admin dispute queue (Phase 5) for
     * staff to triage.
     */
    public function report(string $locale, BusinessProfile $profile): View
    {
        if ($profile->status !== ProfileStatus::Published) {
            abort(Response::HTTP_NOT_FOUND);
        }

        return view('marketing.profile.report', compact('profile'));
    }

    public function submitReport(string $locale, BusinessProfile $profile, Request $request): RedirectResponse
    {
        if ($profile->status !== ProfileStatus::Published) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $data = $request->validate([
            'type' => ['required', Rule::enum(DisputeType::class)],
            'submitter_email' => ['required', 'email', 'max:190'],
            'description' => ['required', 'string', 'max:2000'],
        ]);

        $profile->disputes()->create([
            ...$data,
            'submitted_by_user_id' => auth()->id(),
            'status' => DisputeStatus::Open,
        ]);

        return redirect()
            ->route('marketing.profile.show', ['locale' => $locale, 'profile' => $profile])
            ->with('status', "Thanks — we've received your report and will look into it.");
    }
}
