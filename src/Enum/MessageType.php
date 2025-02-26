<?php

namespace App\Enum;

enum MessageType: string
{
    case USER = 'user';
    case AI = 'ai';
    case SYSTEM = 'system';
}
