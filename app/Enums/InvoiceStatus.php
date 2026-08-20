<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case Paid = 'paid';
    case Open = 'open';
    case Void = 'void';
    case Uncollectible = 'uncollectible';
}
