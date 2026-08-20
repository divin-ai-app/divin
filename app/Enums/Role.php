<?php

namespace App\Enums;

enum Role: string
{
    case Owner = 'owner';
    case Editor = 'editor';
    case Staff = 'staff';
    case Admin = 'admin';
}
