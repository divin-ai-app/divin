<?php

namespace App\Enums;

enum SourceType: string
{
    case Facebook = 'facebook';
    case OtaBooking = 'ota_booking';
    case OtaAgoda = 'ota_agoda';
    case OtaTripadvisor = 'ota_tripadvisor';
    case OwnWebsite = 'own_website';
    case Registry = 'registry';
    case GbpAdjacent = 'gbp_adjacent';
    case OwnerSubmitted = 'owner_submitted';
}
