<?php

namespace App\Enums;

enum ResolutionAction: string
{
    case AcceptedNewValue = 'accepted_new_value';
    case KeptCurrentValue = 'kept_current_value';
}
