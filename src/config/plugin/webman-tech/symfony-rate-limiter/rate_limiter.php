<?php

use WebmanTech\SymfonyRateLimiter\Facades\RateLimiter;

return [
    RateLimiter::ID_REQUEST => [
        'policy' => 'token_bucket',
        'limit' => 1000,
        'rate' => ['interval' => '1 minutes'],
        'storage' => null,
        'lockFactory' => null,
    ],
];