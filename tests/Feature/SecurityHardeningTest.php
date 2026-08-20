<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_responses_carry_baseline_security_headers(): void
    {
        $response = $this->get('/en');

        $response->assertOk();
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
    }

    public function test_contact_form_is_rate_limited(): void
    {
        Mail::fake();

        $payload = ['name' => 'Test', 'email' => 'test@example.com', 'message' => 'Hello there.'];

        for ($i = 0; $i < 5; $i++) {
            $this->post('/en/contact', $payload)->assertRedirect();
        }

        $this->post('/en/contact', $payload)->assertStatus(429);
    }

    public function test_login_link_consumption_is_rate_limited(): void
    {
        for ($i = 0; $i < 20; $i++) {
            $this->get('/en/login/not-a-real-token-'.$i);
        }

        $this->get('/en/login/one-more-token')->assertStatus(429);
    }

    public function test_http_requests_are_redirected_to_https_at_the_htaccess_level(): void
    {
        // Apache/.htaccess handles this in production (Laravel's test
        // client never goes through Apache) — this just documents the
        // rule exists and matches what's actually deployed, so a future
        // edit to public/.htaccess doesn't silently drop it.
        $htaccess = file_get_contents(public_path('.htaccess'));

        $this->assertStringContainsString('RewriteCond %{HTTPS} off', $htaccess);
        $this->assertStringContainsString('RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]', $htaccess);
    }
}
