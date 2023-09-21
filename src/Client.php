<?php

namespace DigitalClaim\Scale;

use DigitalClaim\Scale\Auth;
use DigitalClaim\Scale\Response;
use GuzzleHttp\Client as GuzzleClient;

class Client
{
    /**
     * @var DigitalClaim\Scale\Auth
     */
    protected $auth;

    /**
     * @var GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var int
     */
    protected static $retries = 0;

    /**
     *
     */
    public function __construct(
        ?Auth $auth = null,
        array $options = ['verify' => false, 'debug' => false]
    ) {
        $this->auth   = $auth ?? new Auth;
        $this->client = new GuzzleClient($options);
    }

    /**
     *
     * @return DigitalClaim\Scale\Response
     */
    public function get(string $path): Response
    {
        if (!$this->auth->hasToken($this->auth->getClientId())) {
            $this->refreshToken();
        }

        $response = new Response($this->client->get($this->joinPath($this->auth->getUrl(), $path), [
            'http_errors' => false,
            'headers'     => [
                'Authorization' => 'Bearer ' . $this->auth->getToken($this->auth->getClientId()),
                'Accept'        => 'application/json',
            ],
        ]));

        return $this->processResponse($response, function () use ($path) {
            return $this->get($path);
        });
    }

    /**
     *
     * @return DigitalClaim\Scale\Response
     */
    public function post(string $path, array $data): Response
    {
        if (!$this->auth->hasToken($this->auth->getClientId())) {
            $this->refreshToken();
        }

        $response = new Response($this->client->post($this->joinPath($this->auth->getUrl(), $path), [
            'http_errors' => false,
            'headers'     => [
                'Authorization' => 'Bearer ' . $this->auth->getToken($this->auth->getClientId()),
                'Accept'        => 'application/json',
            ],
            'json'        => $data,
        ]));

        return $this->processResponse($response, function () use ($path, $data) {
            return $this->post($path, $data);
        });
    }

    /**
     * path: /api/token
     */
    protected function refreshToken(): void
    {
        $response = new Response($this->client->post($this->joinPath($this->auth->getUrl(), '/token'), [
            'http_errors' => false,
            'json'        => [
                'client_id'     => $this->auth->getClientId(),
                'client_secret' => $this->auth->getClientSecret(),
                'device_name'   => $this->auth->getDeviceName(),
            ],
        ]));

        $response = $this->processResponse($response);

        $this->auth->setToken($this->auth->getClientId(), $response->json()['data']['token']);
    }

    /**
     * @return DigitalClaim\Scale\Response
     */
    protected function processResponse(Response $response,  ? callable $replay = null) : Response
    {
        if ($response->failed()) {
            if ($response->status() === 401 && $response->json()['message'] === 'Unauthenticated.') {
                if (isset($replay) && self::$retries < 2) {
                    self::$retries += 1;

                    $this->refreshToken();

                    return $replay();
                }
            }

            return $response->throw();
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
