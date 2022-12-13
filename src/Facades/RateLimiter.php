<?php

namespace WebmanTech\SymfonyRateLimiter\Facades;

use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;

/**
 * @method static RateLimiterFactory request()
 */
class RateLimiter
{
    public const ID_REQUEST = 'request';

    protected static $instances = [];

    public static function instance(string $id): RateLimiterFactory
    {
        if (!isset(static::$instances[$id])) {
            static::$instances[$id] = static::createFactory($id);
        }

        return static::$instances[$id];
    }

    protected static function createFactory(string $id): RateLimiterFactory
    {
        $config = config("plugin.webman-tech.symfony-rate-limiter.rate_limiter.{$id}", []);
        $storage = $config['storage'] ?? new InMemoryStorage();
        if ($storage instanceof \Closure) {
            $storage = call_user_func($storage);
        }
        $lockFactory = $config['lockFactory'] ?? null;
        if ($lockFactory instanceof \Closure) {
            $lockFactory = call_user_func($lockFactory);
        }
        return new RateLimiterFactory([
            'id' => $id,
            'policy' => $config['policy'] ?? 'token_bucket',
            'limit' => $config['limit'] ?? null,
            'rate' => $config['rate'] ?? null,
        ], $storage, $lockFactory);
    }

    public static function __callStatic($name, $arguments)
    {
        return static::instance($name);
    }
}