<?php

namespace App\Enums;

enum DisputeType: string
{
    case NotMyBusiness = 'not_my_business';
    case IncorrectData = 'incorrect_data';
    case Duplicate = 'duplicate';
    case UnwantedListing = 'unwanted_listing';
    case Other = 'other';
}
