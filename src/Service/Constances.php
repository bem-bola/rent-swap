<?php

namespace App\Service;


use phpDocumentor\Reflection\Types\Self_;

class Constances {
    // Status
    const ACCEPTED = 'accepted';
    const BANNED = 'banned';
    const CANCELLED = 'cancelled';

    const DELETED = 'deleted';
    const DRAFT = 'draft';
    const PENDING = 'pending';
    const REJECTED = 'rejected';

    const VALIDED = 'validated';

    const NOVALIDED = 'no_validated';

    const SUSPENDED = 'suspended';

    // Log levels
    const LEVEL_DEBUG = 'debug';
    const LEVEL_ERROR = 'error';
    const LEVEL_INFO = 'info';
    const LEVEL_WARNING = 'warning';

    // Arrays
    const ARRAY_LEVEL_LOG = [self::LEVEL_ERROR, self::LEVEL_WARNING, self::LEVEL_DEBUG, self::LEVEL_INFO];
    const ARRAY_ROLES = ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_MODERATOR'];
    const ARRAY_STATUS = [self::PENDING, self::REJECTED, self::ACCEPTED, self::CANCELLED, self::DELETED, self::DRAFT, self::VALIDED, self::BANNED, self::SUSPENDED];

    // Other
    const NB_REPET_PASSWORD = 3;
}
