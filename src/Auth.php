<?php

namespace DigitalClaim\Scale;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

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
     * @var Symfony\Component\Cache\Adapter\FilesystemAdapter
     */
    protected $cache;

    /**
     * @see: https: //github.com/mattstauffer/Torch/blob/master/components/cache/index.php
     */
    public function __construct(
        string $url,
        string $clientId,
        string $clientSecret,
        string $deviceName,
        string $cacheMamespace = 'digitalclaim-scale',
        string $cacheDirectory = __DIR__ . '/cache'
    ) {
        $this->url          = $url;
        $this->clientId     = $clientId;
        $this->clientSecret = $clientSecret;
        $this->deviceName   = $deviceName;
        $this->cache        = new FilesystemAdapter($cacheMamespace, 0, $cacheDirectory);
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
        $item = $this->cache->getItem(base64_encode("token.$clientId"));

        if ($item->isHit()) {
            return $item->get();
        }

        return $this->token;
    }

    /**
     *
     */
    public function setToken(string $clientId, string $token): void
    {
        $this->token = $token;

        $item = $this->cache->getItem(base64_encode("token.$clientId"));

        $item->set($token);

        $this->cache->save($item);
    }

    /**
     *
     */
    public function hasToken(string $clientId): bool
    {
        return !is_null($this->getToken($clientId));
    }
}
