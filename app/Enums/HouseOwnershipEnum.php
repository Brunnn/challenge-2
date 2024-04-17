<?php 

namespace App\Enums;

enum HouseOwnershipEnum: string
{
    case NONE = 'none';
    case OWNED = 'owned';
    case RENTED = 'rented';
}