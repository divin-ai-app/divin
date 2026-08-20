<?php

namespace App\Http\Controllers\Api;

use App\Enums\Industry;
use App\Enums\LegalStatus;
use App\Enums\ProfileStatus;
use App\Enums\SourceType;
use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\CountryClearance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * The contract the future external ingestion/crawler pipeline will call
 * (that pipeline itself is out of scope this phase — see plan §4/§8). Gated
 * by App\Http\Middleware\VerifyIngestionKey (X-Ingestion-Key header).
 *
 * Idempotent upsert keyed by (country_code, external_source_id): the
 * canonical_id is derived deterministically from those two on first insert
 * and never changes again, so the public slug — generated once, also only
 * on first insert — stays stable across re-ingestion even if the source's
 * own name/details change.
 */
class IngestionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'country_code' => ['required', 'string', 'size:2'],
            'source_type' => ['required', Rule::enum(SourceType::class)],
            'external_source_id' => ['required', 'string', 'max:190'],
            'name' => ['required', 'string', 'max:190'],
            'industry' => ['required', Rule::enum(Industry::class)],
            'category' => ['required', 'string', 'max:190'],
            'city' => ['required', 'string', 'max:190'],
            'address_line1' => ['required', 'string', 'max:255'],
            'description_short' => ['required', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'hours' => ['nullable', 'array'],
            'price_range' => ['nullable', 'string', 'max:10'],
            'source_url' => ['nullable', 'url', 'max:2048'],
            'source_snapshot' => ['required', 'array'],
        ]);

        $countryCode = strtoupper($data['country_code']);

        $clearance = CountryClearance::query()->where('country_code', $countryCode)->first();

        if (! $clearance || $clearance->legal_status !== LegalStatus::Cleared) {
            return response()->json([
                'message' => "Country {$countryCode} is not cleared for auto-generation.",
            ], 403);
        }

        $canonicalId = $countryCode.'-'.Str::slug($data['external_source_id']);

        $profile = BusinessProfile::query()->firstOrNew(['canonical_id' => $canonicalId]);
        $isNew = ! $profile->exists;

        $profile->fill([
            'name' => $data['name'],
            'industry' => $data['industry'],
            'category' => $data['category'],
            'country_code' => $countryCode,
            'city' => $data['city'],
            'address_line1' => $data['address_line1'],
            'description_short' => $data['description_short'],
            'phone' => $data['phone'] ?? null,
            'hours' => $data['hours'] ?? null,
            'price_range' => $data['price_range'] ?? null,
            'status' => ProfileStatus::Published,
        ]);

        if ($isNew) {
            $profile->canonical_id = $canonicalId;
            $profile->slug = Str::slug("{$canonicalId}-{$data['name']}");
            $profile->published_at = now();
        }

        $profile->save();

        $profile->dataSources()->updateOrCreate(
            ['source_type' => $data['source_type']],
            [
                'source_url' => $data['source_url'] ?? null,
                'current_snapshot' => $data['source_snapshot'],
                'last_checked_at' => now(),
            ],
        );

        return response()->json([
            'status' => $isNew ? 'created' : 'updated',
            'profile' => [
                'id' => $profile->id,
                'canonical_id' => $profile->canonical_id,
                'slug' => $profile->slug,
            ],
        ], $isNew ? 201 : 200);
    }
}
