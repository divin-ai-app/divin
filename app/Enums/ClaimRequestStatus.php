<?php

namespace App\Enums;

enum ClaimRequestStatus: string
{
    case Submitted = 'submitted';
    case AwaitingVerification = 'awaiting_verification';
    case Verified = 'verified';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
