<?php

namespace Tests\Feature;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Tests\TestCase;

/**
 * Regression guards for the Phase 7 WCAG AA pass — not a full audit (that
 * needs a real renderer), just cheap static checks for the two specific
 * mistakes found and fixed that are easy to silently reintroduce by
 * copy-pasting an old button/link.
 */
class AccessibilityTest extends TestCase
{
    public function test_no_blade_view_uses_the_contrast_failing_button_colors(): void
    {
        // bg-accent (#FF6B35) against white measures ~2.83:1 — fails WCAG AA
        // (needs 4.5:1) for button/link text. Buttons must use bg-accent-700
        // (~4.8:1) instead; `bg-accent` itself stays fine for non-text uses
        // (decorative dots, dark-background labels — see FreshnessChecker's
        // and AppServiceProvider's neighbors for why those are unaffected).
        $offenders = [];

        foreach ($this->bladeFiles() as $file) {
            $contents = file_get_contents($file->getPathname());

            if (str_contains($contents, 'bg-accent px-') || str_contains($contents, 'hover:bg-accent-600')) {
                $offenders[] = $file->getRelativePathname();
            }
        }

        $this->assertEmpty($offenders, 'These views use the contrast-failing accent button color: '.implode(', ', $offenders));
    }

    public function test_dashboard_edit_form_fields_have_accessible_labels(): void
    {
        $contents = file_get_contents(resource_path('views/dashboard/edit.blade.php'));

        // The hours/service/image quick-add inputs rely on aria-label since
        // there's no room for a persistent visible <label> in that compact
        // layout — placeholder text alone isn't a substitute (WCAG 3.3.2).
        $this->assertStringContainsString('aria-label="Service name"', $contents);
        $this->assertStringContainsString('aria-label="Image description (optional)"', $contents);
    }

    /** @return SplFileInfo[] */
    private function bladeFiles(): iterable
    {
        return Finder::create()->files()->in(resource_path('views'))->name('*.blade.php');
    }
}
