<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

/**
 * Placeholder shell for Phase 3's "done" criteria (a claimed profile lands
 * somewhere real after checkout). Profile editing, billing, and the rest of
 * the real dashboard are built out in Phase 4 — see plan §7.
 */
class DashboardController extends Controller
{
    public function index(string $locale): View
    {
        $profiles = Auth::user()->ownerships()->with('profile')->get()->pluck('profile');

        return view('dashboard.index', compact('profiles'));
    }
}
