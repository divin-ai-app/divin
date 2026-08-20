<?php

namespace App\Enums;

enum PlanTier: string
{
    case None = 'none';
    case Registered = 'registered';
    case Managed = 'managed';
}
