<?php

namespace App\Enums;

enum VerificationMethod: string
{
    case EmailMatch = 'email_match';
    case PhoneMatch = 'phone_match';
    case DocumentUpload = 'document_upload';
    case AdminCode = 'admin_code';
}
