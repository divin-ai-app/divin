<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketingSiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_default_locale(): void
    {
        $this->get('/')->assertRedirect('/en');
    }

    public function test_home_page_renders(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertSee('divin.ai', false);
    }

    /**
     * Regression test for the positional route-parameter binding bug: a
     * controller method that omits an earlier route segment (here {locale})
     * silently receives the WRONG value for its own parameter unless every
     * preceding segment is declared. See MarketingController's class doc.
     */
    public function test_industry_show_page_renders_the_correct_industry(): void
    {
        $this->get('/en/industries/hospitality')
            ->assertOk()
            ->assertSee('Hospitality', false)
            ->assertDontSee('Trying to access array offset', false);
    }

    public function test_unknown_industry_slug_404s(): void
    {
        $this->get('/en/industries/not-a-real-industry')->assertNotFound();
    }

    public function test_robots_txt_allows_ai_crawlers(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('GPTBot')
            ->assertSee('ClaudeBot')
            ->assertSee('PerplexityBot');
    }

    public function test_sitemap_lists_marketing_pages(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('/en/pricing', false)
            ->assertSee('/en/industries/food', false);
    }

    public function test_llms_txt_is_reachable(): void
    {
        $this->get('/llms.txt')->assertOk()->assertSee('divin.ai');
    }

    public function test_contact_form_submission_succeeds(): void
    {
        // Mail::raw() bypasses Mailable classes, so Mail::fake()'s
        // assertSent() can't target it — the meaningful assertion is that
        // submission succeeds and flashes a confirmation, without throwing.
        // End-to-end delivery was verified manually against Mailpit.
        $this->post('/en/contact', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'Hello there.',
        ])
            ->assertRedirect()
            ->assertSessionHas('status');
    }
}
