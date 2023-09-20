<?php

namespace DigitalClaim\Scale;

use Illuminate\Cache\CacheManager;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Cache;

class Auth
{
    /**
     * @var String
     */
    protected $url;

    /**
     * @var String
     */
    protected $clientId;

    /**
     * @var String
     */
    protected $clientSecret;

    /**
     * @var String
     */
    protected $deviceName;

    /**
     * @var String
     */
    protected $token;

    /**
     * @var
     */
    protected $cache;

    /**
     * @see: https: //github.com/mattstauffer/Torch/blob/master/components/cache/index.php
     */
    public function __construct(
        ?string $url = null,
        ?string $clientId = null,
        ?string $clientSecret = null,
        ?string $deviceName = null,
        array $config = [
            'cache.default'     => 'file',
            'cache.stores.file' => [
                'driver' => 'file',
                'path'   => __DIR__ . '/cache',
            ],
        ]
    ) {
        $this->url          = $url ?? env('SCALE_API');
        $this->clientId     = $clientId ?? env('SCALE_CLIENT_ID');
        $this->clientSecret = $clientSecret ?? env('SCALE_SECRET');
        $this->deviceName   = $deviceName ?? env('SCALE_DEVICE_NAME');

        // Create a new Container object, needed by the cache manager.
        $container = new Container;

        // The CacheManager creates the cache "repository" based on config values
        // which are loaded from the config class in the container.
        // More about the config class can be found in the config component; for now we will use an array
        $container['config'] = $config;

        // To use the file cache driver we need an instance of Illuminate's Filesystem, also stored in the container
        $container['files'] = new Filesystem;

        // Create the CacheManager
        $cacheManager = new CacheManager($container);

        // Get the default cache driver (file in this case)
        $this->cache = $cacheManager->store();
    }

    /**
     *
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     *
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     *
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     *
     */
    public function getDeviceName(): string
    {
        return $this->deviceName;
    }

    /**
     *
     */
    public function getToken(string $clientId): ?string
    {
        return $this->cache->get("DigitalClaim\Scale::token.$clientId", $this->token);
    }

    /**
     *
     */
    public function setToken(string $clientId, string $token): void
    {
        $this->token = $token;

        $this->cache->forever("DigitalClaim\Scale::token.$clientId", $token);
    }

    /**
     *
     */
    public function hasToken(string $clientId): bool
    {
        return !is_null($this->getToken($clientId));
    }
}
