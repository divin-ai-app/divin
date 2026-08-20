<?php

namespace App\Http\Controllers;

use App\Enums\ProfileStatus;
use App\Models\BusinessProfile;
use App\Support\SchemaOrgBuilder;
use Illuminate\Contracts\View\View;
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
}
