<?php

namespace App\Enums;

enum TransactionType: string
{
    case TRANSACTION = 'transaction';
    case SALES       = 'sales';
    case RETURN      = 'return';
    case ADJUST      = 'adjustment';
}