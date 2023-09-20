<?php

namespace DigitalClaim\Scale;

use DigitalClaim\Scale\Auth;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;

class Request
{
    /**
     * @var DigitalClaim\Scale\Auth
     */
    protected $auth;

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * @var bool
     */
    protected $verify = false;

    /**
     * @var int
     */
    protected static $retries = 0;

    /**
     * @var
     */
    protected $http;

    /**
     *
     */
    public function __construct(?Auth $auth = null)
    {
        $this->auth = $auth ?? new Auth;
        $this->http = new Factory();
    }

    /**
     *
     * @return Illuminate\Http\Client\Response
     */
    public function get(string $path): Response
    {
        if (!$this->auth->hasToken($this->auth->getClientId())) {
            $this->refreshToken();
        }

        $response = $this->http->withOptions([
            'debug'  => $this->debug,
            'verify' => $this->verify,
            // 'delay'  => rand(0, 1000)
        ])->withHeaders([
            'Authorization' => 'Bearer ' . $this->auth->getToken($this->auth->getClientId()),
            'Accept'        => 'application/json',
        ])->get($this->joinPath($this->auth->getUrl(), $path));

        return $this->processResponse($response, function () use ($path) {
            return $this->get($path);
        });
    }

    /**
     *
     * @return Illuminate\Http\Client\Response
     */
    public function post(string $path, array $data): Response
    {
        if (!$this->auth->hasToken($this->auth->getClientId())) {
            $this->refreshToken();
        }

        $response = $this->http->withOptions([
            'debug'  => $this->debug,
            'verify' => $this->verify,
        ])->withHeaders([
            'Authorization' => 'Bearer ' . $this->auth->getToken($this->auth->getClientId()),
            'Accept'        => 'application/json',
        ])->post($this->joinPath($this->auth->getUrl(), $path), $data);

        return $this->processResponse($response, function () use ($path, $data) {
            return $this->post($path, $data);
        });
    }

    /**
     * path: /api/token
     */
    protected function refreshToken(): void
    {
        $response = $this->http->withOptions([
            'debug'  => $this->debug,
            'verify' => $this->verify,
        ])->post($this->joinPath($this->auth->getUrl(), '/token'), [
            'client_id'     => $this->auth->getClientId(),
            'client_secret' => $this->auth->getClientSecret(),
            'device_name'   => $this->auth->getDeviceName(),
        ]);

        $response = $this->processResponse($response);

        $this->auth->setToken($this->auth->getClientId(), Arr::get($response->json(), 'data.token'));
    }

    /**
     * @return Illuminate\Http\Client\Response
     */
    protected function processResponse(Response $response,  ? callable $replay = null) : Response
    {
        if ($response->failed()) {
            if ($response->status() === 401 && Arr::get($response->json(), 'message') === 'Unauthenticated.') {
                if (isset($replay) && self::$retries < 2) {
                    self::$retries += 1;

                    $this->refreshToken();

                    return $replay();
                }
            }

            $response->throw();
        }

        self::$retries = 0;

        return $response;
    }

    /**
     * @return string
     */
    protected function joinPath(): string
    {
        $parts   = func_get_args();
        $trimmed = [];

        foreach ($parts as $part) {
            $trimmed[] = trim($part, '/');
        }

        return implode('/', $trimmed);
    }
}
