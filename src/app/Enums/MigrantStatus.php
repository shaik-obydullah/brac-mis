<?php

namespace App\Enums;

enum MigrantStatus: string
{
    case Registered = 'registered';
    case PreDeparture = 'pre_departure';
    case Deployed = 'deployed';
    case Returned = 'returned';
    case Cancelled = 'cancelled';
}
