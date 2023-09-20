<?php

namespace DigitalClaim\Scale;

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
     *
     */
    public function __construct(?string $url = null, ?string $clientId = null, ?string $clientSecret = null, ?string $deviceName = null)
    {
        $this->url          = $url ?? env('SCALE_API');
        $this->clientId     = $clientId ?? env('SCALE_CLIENT_ID');
        $this->clientSecret = $clientSecret ?? env('SCALE_SECRET');
        $this->deviceName   = $deviceName ?? env('SCALE_DEVICE_NAME');
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
        return Cache::get("DigitalClaim\Scale::token.$clientId", $this->token);
    }

    /**
     *
     */
    public function setToken(string $clientId, string $token): void
    {
        $this->token = $token;

        Cache::forever("DigitalClaim\Scale::token.$clientId", $token);
    }

    /**
     *
     */
    public function hasToken(string $clientId): bool
    {
        return !is_null($this->getToken($clientId));
    }
}
