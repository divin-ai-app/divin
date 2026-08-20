<?php

namespace App\Enums;

enum DisputeStatus: string
{
    case Open = 'open';
    case InReview = 'in_review';
    case Corrected = 'corrected';
    case Removed = 'removed';
    case Rejected = 'rejected';
}
