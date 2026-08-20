<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IngestionSourceStatus;
use Illuminate\Contracts\View\View;

/**
 * Read-only status view over ingestion_source_statuses. Per plan §8 "Out of
 * scope this phase", the actual ingestion pipeline is a separate backend
 * project — this table has no writer yet, so it will render empty until
 * that pipeline (or a manual seed) starts populating it. The UI is real;
 * the data behind it is not, by design.
 */
class DataSourceController extends Controller
{
    public function index(string $locale): View
    {
        $sources = IngestionSourceStatus::query()->orderBy('country_code')->orderBy('source_type')->get();

        return view('admin.data-sources.index', compact('sources'));
    }
}
