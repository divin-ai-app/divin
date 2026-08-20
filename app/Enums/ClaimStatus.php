<?php

namespace App\Enums;

// BusinessProfile.claim_status — coarse claim state of the profile itself.
// Distinct from ClaimRequestStatus, which tracks one claim attempt's lifecycle.
enum ClaimStatus: string
{
    case Unclaimed = 'unclaimed';
    case PendingClaim = 'pending_claim';
    case Claimed = 'claimed';
    case Verified = 'verified';
}
