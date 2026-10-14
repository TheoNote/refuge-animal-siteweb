<?php

namespace App\Enum;

enum RoleUser : string
{
    case Admin = 'ROLE_ADMIN';
    case Worker = 'ROLE_WORKER';
    case User = 'ROLE_USER';
}
