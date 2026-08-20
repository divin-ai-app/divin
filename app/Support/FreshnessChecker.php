<?php

namespace App\Support;

use App\Enums\FreshnessSeverity;
use App\Models\BusinessProfile;
use App\Models\DataSource;

/**
 * Diffs a DataSource's current_snapshot against the live BusinessProfile
 * fields it was captured from (plan §4 flow 3 "Managed freshness alert").
 * Pure comparison logic, kept separate from Console\Commands\CheckFreshness
 * so it's independently testable and reusable if a real ingestion pipeline
 * ever calls it directly instead of via the scheduled command.
 *
 * current_snapshot is a flat JSON object keyed by the same field names as
 * BusinessProfile — only the fields below are ever compared; anything else
 * in the snapshot is ignored.
 */
class FreshnessChecker
{
    /** Field => severity if that field has drifted. Contact-critical fields outrank cosmetic ones. */
    private const FIELD_SEVERITY = [
        'phone' => FreshnessSeverity::High,
        'address_line1' => FreshnessSeverity::High,
        'website' => FreshnessSeverity::Medium,
        'description_short' => FreshnessSeverity::Low,
        'name' => FreshnessSeverity::Medium,
    ];

    /**
     * @return array<int, array{field: string, label: string, current_value: ?string, source_value: string, resolution: ?string}>
     */
    public static function compare(BusinessProfile $profile, DataSource $dataSource): array
    {
        $snapshot = $dataSource->current_snapshot ?? [];
        $discrepancies = [];

        foreach (array_keys(self::FIELD_SEVERITY) as $field) {
            if (! array_key_exists($field, $snapshot)) {
                continue;
            }

            $sourceValue = trim((string) $snapshot[$field]);
            $currentValue = trim((string) ($profile->{$field} ?? ''));

            if ($sourceValue !== '' && $sourceValue !== $currentValue) {
                $discrepancies[] = [
                    'field' => $field,
                    'label' => self::label($field),
                    'current_value' => $currentValue !== '' ? $currentValue : null,
                    'source_value' => $sourceValue,
                    // Resolved per-field, independently — see DashboardController::resolveFreshness.
                    'resolution' => null,
                ];
            }
        }

        return $discrepancies;
    }

    /** @param array<int, array{field: string}> $discrepancies */
    public static function severityFor(array $discrepancies): FreshnessSeverity
    {
        $severities = array_map(
            fn (array $d) => self::FIELD_SEVERITY[$d['field']] ?? FreshnessSeverity::Low,
            $discrepancies,
        );

        return match (true) {
            in_array(FreshnessSeverity::High, $severities, true) => FreshnessSeverity::High,
            in_array(FreshnessSeverity::Medium, $severities, true) => FreshnessSeverity::Medium,
            default => FreshnessSeverity::Low,
        };
    }

    private static function label(string $field): string
    {
        return match ($field) {
            'phone' => 'Phone',
            'address_line1' => 'Address',
            'website' => 'Website',
            'description_short' => 'Short description',
            'name' => 'Business name',
            default => ucfirst(str_replace('_', ' ', $field)),
        };
    }
}
