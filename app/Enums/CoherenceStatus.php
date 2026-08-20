<?php

namespace App\Enums;

enum CoherenceStatus: string
{
    case Aligned = 'aligned';
    case MinorDrift = 'minor_drift';
    case MajorDrift = 'major_drift';
    case Unreachable = 'unreachable';
    case NotChecked = 'not_checked';
}
