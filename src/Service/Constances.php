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

    const ARRAY_STATUS = ['pending', 'rejected', 'accepted', 'cancelled', 'deleted', 'draft'];

    const ARRAY_LEVEL_LOG = ['error', 'info', 'debug', 'warning'];

    const LEVEL_INFO = 'info';

    const LEVEL_DEBUG = 'debug';

    const LEVEL_WARNING = 'warning';

    const LEVEL_ERROR = 'error';
}