<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LegalStatus;
use App\Http\Controllers\Controller;
use App\Models\CountryClearance;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * The concrete mechanism gating auto-generation by country (plan §4
 * "Ingestion API contract" — the ingestion endpoint rejects any country
 * whose legal_status isn't Cleared). This is the UI that toggles it.
 */
class CountryClearanceController extends Controller
{
    public function index(string $locale): View
    {
        $clearances = CountryClearance::query()->withCount('profiles')->orderBy('country_name')->get();

        return view('admin.country-clearance.index', compact('clearances'));
    }

    public function update(string $locale, CountryClearance $clearance, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'legal_status' => ['required', Rule::enum(LegalStatus::class)],
            'gdpr_excluded' => ['sometimes', 'boolean'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $data['gdpr_excluded'] = $request->boolean('gdpr_excluded');
        $data['reviewed_by_staff_id'] = Auth::id();

        if ($data['legal_status'] === LegalStatus::Cleared->value && $clearance->legal_status !== LegalStatus::Cleared) {
            $data['cleared_at'] = now();
        }

        $clearance->update($data);

        return back()->with('status', "{$clearance->country_name}'s clearance status updated.");
    }
}
