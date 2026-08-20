<?php

namespace App\Enums;

// Annual only — see plan §1 ("no card-fee drag on small transactions").
enum BillingCycle: string
{
    case Annual = 'annual';
}
