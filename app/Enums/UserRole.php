<?php

namespace App\Enums;

enum UserRole : string
{
    case Admin = "Admin";
    case Payer = 'Payer';
    case Sender = 'Sender';
}