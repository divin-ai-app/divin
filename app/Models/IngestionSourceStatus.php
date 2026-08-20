<?php

namespace App\Models;

use App\Enums\IngestionRunStatus;
use App\Enums\SourceType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['country_code', 'source_type', 'last_run_at', 'last_run_status', 'records_ingested', 'records_failed', 'next_scheduled_run'])]
class IngestionSourceStatus extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'source_type' => SourceType::class,
            'last_run_status' => IngestionRunStatus::class,
            'last_run_at' => 'datetime',
            'next_scheduled_run' => 'datetime',
        ];
    }
}
