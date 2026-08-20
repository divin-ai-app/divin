<?php

namespace App\Enums;

// CountryClearance.legal_status — gates auto-generation per country. See
// plan §4 "Ingestion API contract": the (future) ingestion endpoint rejects
// any country whose status isn't Cleared.
enum LegalStatus: string
{
    case NotStarted = 'not_started';
    case InReview = 'in_review';
    case Cleared = 'cleared';
    case ExcludedGdpr = 'excluded_gdpr';
}
