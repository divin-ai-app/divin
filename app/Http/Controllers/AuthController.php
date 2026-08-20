<?php

namespace App\Http\Controllers;

use App\Mail\LoginLinkMail;
use App\Models\LoginLink;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

/**
 * Account login only — magic-link, built on a single-use hashed token
 * (App\Models\LoginLink), not Laravel's stateless signed URLs, so a link
 * can be invalidated after one use. Deliberately separate from claim-flow
 * ownership verification (ClaimController) — see that controller's docblock.
 */
class AuthController extends Controller
{
    private const LINK_MINUTES_VALID = 15;

    public function showLogin(string $locale): View
    {
        return view('auth.login');
    }

    public function sendLoginLink(string $locale, Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'max:190'],
        ]);

        // Rate-limited at the route level (throttle:6,1) — see routes/web.php.
        [, $rawToken] = LoginLink::issue($request->string('email')->toString(), self::LINK_MINUTES_VALID);

        $url = route('marketing.login.consume', ['locale' => $locale, 'token' => $rawToken]);

        Mail::to($request->string('email')->toString())
            ->send(new LoginLinkMail($url, self::LINK_MINUTES_VALID));

        return redirect()->route('marketing.verify-request', ['locale' => $locale]);
    }

    public function verifyRequest(string $locale): View
    {
        return view('auth.verify-request');
    }

    public function consume(string $locale, string $token, Request $request): RedirectResponse
    {
        $link = LoginLink::findValidByRawToken($token);

        if (! $link) {
            return redirect()
                ->route('marketing.login', ['locale' => $locale])
                ->withErrors(['email' => 'That sign-in link is invalid or has expired. Request a new one below.']);
        }

        $link->markUsed();

        $user = User::query()->firstOrCreate(
            ['email' => $link->email],
            ['name' => explode('@', $link->email)[0]],
        );

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return redirect()->intended(route('marketing.dashboard.index', ['locale' => $locale]));
    }

    public function logout(string $locale, Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('marketing.home', ['locale' => $locale]);
    }
}
