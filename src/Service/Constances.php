<?php

namespace App\Service;


class Constances {
    const PENDING = 'pending';
    const REJECTED = 'rejected';
    const ACCEPTED = 'accepted';
    const CANCELLED = 'cancelled';

    const DELETED = 'deleted';
    const VALIDED = 'validated';
    const DRAFT = 'draft';
    const BANNED = 'banned';

    const ARRAY_STATUS = [self::PENDING, self::REJECTED, self::ACCEPTED, self::CANCELLED, self::DELETED, self::DRAFT, self::VALIDED];

    const LEVEL_INFO = 'info';

    const LEVEL_DEBUG = 'debug';

    const LEVEL_WARNING = 'warning';

    const LEVEL_ERROR = 'error';

    const ARRAY_LEVEL_LOG = [self::LEVEL_ERROR, self::LEVEL_WARNING, self::LEVEL_DEBUG, self::LEVEL_INFO];

    const ARRAY_ROLES = ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_MODERATOR'];

}