<?php

namespace App\Enum;

enum AssignmentStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case WAITLIST =  'waitlist';
}
