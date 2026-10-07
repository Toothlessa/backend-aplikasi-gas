<?php

namespace App\Enums;

enum ItemType: string
{
    case ASSET = 'ASSET';
    case PRODUCT = 'PRODUCT';
    case CONSUMABLE = 'CONSUMABLE';
    case SERVICE = 'SERVICE';
}
