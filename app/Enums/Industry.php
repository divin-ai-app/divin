<?php

namespace App\Enums;

enum Industry: string
{
    case Healthcare = 'healthcare';
    case Hospitality = 'hospitality';
    case Retail = 'retail';
    case Food = 'food';
    case FinancialServices = 'financial-services';
    case Other = 'other';

    /** Config-driven copy (config/industries.php) — see plan §3. */
    public function label(): string
    {
        return config("industries.{$this->value}.name", ucfirst($this->value));
    }
}
