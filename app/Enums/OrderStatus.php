<?php

namespace App\Enums;

enum OrderStatus : string
{
    case Pending = "Pending";
    case Paid = 'Paid';
    case Failed = 'Failed';
}