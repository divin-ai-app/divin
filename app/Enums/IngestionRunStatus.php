<?php

namespace App\Enums;

enum IngestionRunStatus: string
{
    case Success = 'success';
    case Partial = 'partial';
    case Failed = 'failed';
}
