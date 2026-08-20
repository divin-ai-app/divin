<?php

namespace App\Support;

use App\Models\BusinessProfile;

/**
 * Builds JSON-LD for a public business profile page — see plan §2's
 * "practice what it preaches" requirement and §5 ("full schema.org
 * structured data on every business profile page").
 */
class SchemaOrgBuilder
{
    /** Map our Industry enum to the most specific schema.org type available. */
    private const TYPE_MAP = [
        'healthcare' => 'MedicalBusiness',
        'hospitality' => 'LodgingBusiness',
        'retail' => 'Store',
        'food' => 'Restaurant',
        'financial-services' => 'FinancialService',
        'other' => 'LocalBusiness',
    ];

    /**
     * Intentionally built here rather than inline in a .blade.php file:
     * Blade's compiler text-matches `@word` as directives even inside PHP
     * string literals, so a literal `'@context'`/`'@type'` array key
     * written directly in a Blade template gets silently corrupted (Blade
     * has a real `@context` directive). Keeping all schema.org JSON
     * construction in plain PHP avoids that footgun entirely.
     */
    public static function organization(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'divin.ai',
            'url' => url('/'),
            'description' => 'An open, AI-engine-agnostic business registry that publishes verified, structured business profiles crawlable by every major AI engine.',
        ];
    }

    public static function faqPage(array $faqs): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(fn (array $faq) => [
                '@type' => 'Question',
                'name' => $faq['q'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['a']],
            ], $faqs),
        ];
    }

    public static function forProfile(BusinessProfile $profile): array
    {
        $type = self::TYPE_MAP[$profile->industry->value] ?? 'LocalBusiness';

        $data = [
            '@context' => 'https://schema.org',
            '@type' => $type,
            'name' => $profile->name,
            'description' => $profile->description_short,
            'url' => route('marketing.profile.show', ['locale' => app()->getLocale(), 'profile' => $profile]),
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $profile->address_line1,
                'addressLocality' => $profile->city,
                'addressCountry' => $profile->country_code,
            ],
        ];

        if ($profile->phone) {
            $data['telephone'] = $profile->phone;
        }

        if ($profile->website) {
            $data['sameAs'] = [$profile->website];
        }

        if ($profile->price_range) {
            $data['priceRange'] = $profile->price_range;
        }

        if ($profile->lat && $profile->lng) {
            $data['geo'] = [
                '@type' => 'GeoCoordinates',
                'latitude' => $profile->lat,
                'longitude' => $profile->lng,
            ];
        }

        $image = $profile->images->first();
        if ($image) {
            $data['image'] = $image->url;
        }

        $hoursSpec = self::openingHoursSpecification($profile->hours ?? []);
        if ($hoursSpec) {
            $data['openingHoursSpecification'] = $hoursSpec;
        }

        return $data;
    }

    private static function openingHoursSpecification(array $hours): array
    {
        $dayMap = [
            'monday' => 'Monday', 'tuesday' => 'Tuesday', 'wednesday' => 'Wednesday',
            'thursday' => 'Thursday', 'friday' => 'Friday', 'saturday' => 'Saturday', 'sunday' => 'Sunday',
        ];

        $spec = [];

        foreach ($dayMap as $key => $schemaDay) {
            $entry = $hours[$key] ?? null;

            if (! is_array($entry) || empty($entry['open']) || empty($entry['close'])) {
                continue;
            }

            $spec[] = [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => "https://schema.org/{$schemaDay}",
                'opens' => $entry['open'],
                'closes' => $entry['close'],
            ];
        }

        return $spec;
    }
}
