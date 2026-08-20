<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Industry;
use App\Enums\ProfileStatus;
use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index(string $locale, Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:190'],
            'status' => ['nullable', Rule::enum(ProfileStatus::class)],
            'country' => ['nullable', 'string', 'size:2'],
            'industry' => ['nullable', Rule::enum(Industry::class)],
        ]);

        $profiles = BusinessProfile::query()
            ->when($filters['q'] ?? null, fn ($query, $q) => $query->where('name', 'like', "%{$q}%"))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['country'] ?? null, fn ($query, $country) => $query->where('country_code', $country))
            ->when($filters['industry'] ?? null, fn ($query, $industry) => $query->where('industry', $industry))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $countries = BusinessProfile::query()->select('country_code')->distinct()->orderBy('country_code')->pluck('country_code');

        return view('admin.profiles.index', [
            'profiles' => $profiles,
            'countries' => $countries,
            'filters' => $filters,
        ]);
    }

    public function show(string $locale, BusinessProfile $profile): View
    {
        $profile->load(['services', 'images', 'subscription', 'claimRequests.requester', 'disputes', 'countryClearance']);

        return view('admin.profiles.show', compact('profile'));
    }
}
