<?php

namespace DigitalClaim\Scale;

use DigitalClaim\Scale\Auth;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

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
     *
     */
    public function __construct(?Auth $auth = null)
    {
        $this->auth = $auth ?? new Auth;
    }

    /**
     *
     * @return Illuminate\Http\Client\Response
     */
    public function get(string $path): Response
    {
        // \Log::info('Send get request:', [
        //     'path'          => $path,
        //     'url'           => $this->auth->getUrl(),
        //     'client_id'     => $this->auth->getClientId(),
        //     'client_secret' => $this->auth->getClientSecret(),
        // ]);

        if (!$this->auth->hasToken($this->auth->getClientId())) {
            $this->refreshToken();
        }

        $response = Http::withOptions([
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
        // \Log::info('Send post request:', [
        //     'path'          => $path,
        //     'url'           => $this->auth->getUrl(),
        //     'client_id'     => $this->auth->getClientId(),
        //     'client_secret' => $this->auth->getClientSecret(),
        //     'data'          => $data
        // ]);

        // \Log::info('The post data: ' . print_r($data, true));

        if (!$this->auth->hasToken($this->auth->getClientId())) {
            $this->refreshToken();
        }

        $response = Http::withOptions([
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
        $response = Http::withOptions([
            'debug'  => $this->debug,
            'verify' => $this->verify,
        ])->post($this->joinPath($this->auth->getUrl(), '/token'), [
            'client_id'     => $this->auth->getClientId(),
            'client_secret' => $this->auth->getClientSecret(),
            'device_name'   => $this->auth->getDeviceName(),
        ]);

        $response = $this->processResponse($response);

        \Log::info("Response", ['response' => $response->json()]);

        $this->auth->setToken($this->auth->getClientId(), Arr::get($response->json(), 'data.token'));
    }

    /**
     * @return Illuminate\Http\Client\Response
     */
    protected function processResponse(Response $response, ?callable $replay = null): Response
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
