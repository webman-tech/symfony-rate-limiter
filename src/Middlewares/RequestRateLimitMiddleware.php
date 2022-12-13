<?php

namespace WebmanTech\SymfonyRateLimiter\Middlewares;

use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;
use WebmanTech\SymfonyRateLimiter\Facades\RateLimiter;

class RequestRateLimitMiddleware implements MiddlewareInterface
{
    private $config = [
        'id' => RateLimiter::ID_REQUEST,
        'keyBy' => ['path'], // path/ip/header:xxx/get:xxx/post:xxx
    ];

    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * @inheritDoc
     */
    public function process(Request $request, callable $handler): Response
    {
        $keys = [];
        $keyValues = $this->getKeyValues();
        foreach ($this->config['keyBy'] as $key) {
            $arr = explode(':', $key);
            $realKey = $arr[0];
            unset($arr[0]);
            $keys[$key] = call_user_func($keyValues[$realKey], $request, ...$arr);
        }
        $limiter = RateLimiter::instance($this->config['id'])->create($this->getKeyByKeys($keys));
        $limiter->consume()->ensureAccepted();

        return $handler($request);
    }

    protected function getKeyValues(): array
    {
        return [
            'path' => function (Request $request) {
                return $request->route->getPath();
            },
            'ip' => function (Request $request) {
                return $request->getRealIp();
            },
            'header' => function (Request $request, string $name) {
                return $request->header($name);
            },
            'get' => function (Request $request, string $name) {
                return $request->get($name);
            },
            'post' => function (Request $request, string $name) {
                return $request->post($name);
            },
        ];
    }

    protected function getKeyByKeys(array $keys): string
    {
        return serialize($keys);
    }
}