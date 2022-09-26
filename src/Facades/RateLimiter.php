<?php

namespace WebmanTech\SymfonyRateLimiter\Facades;

use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;

class RateLimiter
{
    protected static $instances = [];

    public static function instance(string $name): RateLimiterFactory
    {
        if (!isset(static::$instances[$name])) {
            static::$instances[$name] = static::createFactory($name);
        }

        return static::$instances[$name];
    }

    protected static function createFactory(string $name): RateLimiterFactory
    {
        $config = config("plugin.webman-tech.symfony-rate-limiter.rate_limiter.{$name}", []);
        $storage = $config['storage'] ?? new InMemoryStorage();
        if ($storage instanceof \Closure) {
            $storage = call_user_func($storage);
        }
        $lockFactory = $config['lockFactory'] ?? null;
        if ($lockFactory instanceof \Closure) {
            $lockFactory = call_user_func($lockFactory);
        }
        return new RateLimiterFactory([
            'id' => $name,
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