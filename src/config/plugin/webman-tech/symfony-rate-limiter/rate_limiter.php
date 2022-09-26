<?php

return [
    'requestGlobal' => [
        'policy' => 'token_bucket',
        'limit' => 10,
        'rate' => ['interval' => '15 minutes'],
        'storage' => function () {
            return new \Symfony\Component\RateLimiter\Storage\InMemoryStorage();
        },
        'lockFactory' => null,
    ],
];