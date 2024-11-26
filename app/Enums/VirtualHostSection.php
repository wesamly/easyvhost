<?php

namespace App\Enums;

enum VirtualHostSection: string
{
    case HTTP = 'http';
    case HTTPS = 'https';
}
