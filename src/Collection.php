<?php

namespace DigitalClaim\Scale;

use DigitalClaim\Scale\Auth;
use DigitalClaim\Scale\Client;
use DigitalClaim\Scale\Response;

class Collection
{
    /**
     * @var DigitalClaim\Scale\Client
     */
    protected $client;

    /**
     * @var String
     */
    protected $uid;

    /**
     *
     */
    public function __construct(
        String $uid,
        Auth $auth,
        array $options = ['verify' => false, 'debug' => false]
    ) {
        $this->client = new Client($auth, $options);
        $this->uid    = $uid;
    }

    /**
     * path: /api/collection/{collectionUid}/document/get/{documentUid}
     *
     * @return DigitalClaim\Scale\Response
     */
    public function get(string $uid, array $data): Response
    {
        return $this->client->post("/collection/{$this->uid}/document/get/$uid", $data);
    }

    /**
     * path: /api/collection/{collectionUid}/document/create
     *
     * @return DigitalClaim\Scale\Response
     */
    public function create(array $data): Response
    {
        return $this->client->post("/collection/{$this->uid}/document/create", $data);
    }

    /**
     * path: /api/collection/{collectionUid}/document/delete/{documentUid}
     *
     * @return DigitalClaim\Scale\Response
     */
    public function delete(string $uid, bool $softdelete = false): Response
    {
        return $this->client->post("/collection/{$this->uid}/document/delete/$uid", [
            'data' => [
                'softdelete' => $softdelete,
            ],
        ]);
    }

    /**
     * path: /api/collection/{collectionUid}/document/update/{uid}
     *
     * @return DigitalClaim\Scale\Response
     */
    public function update(string $uid, array $data): Response
    {
        return $this->client->post("/collection/{$this->uid}/document/update/$uid", $data);
    }

    /**
     * path: /api/collection/{collectionUid}/document/paginate/{page}
     *
     * @return DigitalClaim\Scale\Response
     */
    public function paginate(string $page, array $data, int $size = 20): Response
    {
        return $this->client->post("/collection/{$this->uid}/document/paginate/$page/$size", $data);
    }

    /**
     * path: /api/collection/{collectionUid}/document/push
     *
     * @return DigitalClaim\Scale\Response
     */
    public function push(string $path, $item, bool $unique, array $filter = [])
    {
        return $this->client->post("/collection/{$this->uid}/document/push", [
            'data' => [
                'path'   => $path,
                'item'   => $item,
                'unique' => $unique,
                'filter' => $filter,
            ],
        ]);
    }

    /**
     * path: /api/collection/{collectionUid}/document/aggregate
     *
     * @return DigitalClaim\Scale\Response
     */
    public function aggregate(array $aggregation, array $filter = []): Response
    {
        return $this->client->post("/collection/{$this->uid}/document/aggregate", [
            'data' => [
                'aggregation' => $aggregation,
                'filter'      => $filter,
            ],
        ]);
    }

    /**
     * path: /api/collection/{$this->collecion->uid}/document/{$documentUid}/file/get/{$fileUid}
     *
     * @return DigitalClaim\Scale\Response
     */
    public function getFile(string $documentUid, string $fileUid): Response
    {
        return $this->client->get("/collection/{$this->uid}/document/$documentUid/file/get/$fileUid");
    }

    /**
     * path: /api/collection/{$this->collecion->uid}/document/{$documentUid}/file/get/{$fileUid}/meta
     *
     * @return DigitalClaim\Scale\Response
     */
    public function getFileMeta(string $documentUid, string $fileUid): Response
    {
        return $this->client->get("/collection/{$this->uid}/document/$documentUid/file/get/$fileUid/meta");
    }

    /**
     * path: /api/collection/{$this->collecion->uid}/document/{$documentUid}/file/update/{$fileUid}
     *
     * @return DigitalClaim\Scale\Response
     */
    public function updateFile(string $documentUid, string $fileUid, string $name, array $meta): Response
    {
        return $this->client->post("/collection/{$this->uid}/document/$documentUid/file/update/$fileUid", [
            'data' => [
                'name' => $name,
                'meta' => $meta,
            ],
        ]);
    }

    /**
     * path: /api/collection/{$this->collecion->uid}/document/{$documentUid}/file/delete/{$fileUid}
     *
     * @return DigitalClaim\Scale\Response
     */
    public function deleteFile(string $documentUid, string $fileUid): Response
    {
        return $this->client->post("/collection/{$this->uid}/document/$documentUid/file/delete/$fileUid", []);
    }

    /**
     * path: /api/collection/{$this->collecion->uid}/document/{$documentUid}/file/create
     *
     * @return DigitalClaim\Scale\Response
     */
    public function putFile(string $documentUid, string $name, string $payload, array $meta = []): Response
    {
        return $this->client->post("/collection/{$this->uid}/document/$documentUid/file/create", [
            'data' => [
                'name'    => $name,
                'meta'    => $meta,
                'payload' => $payload,
            ],
        ]);
    }

    /**
     * path: /api/collection/{$this->collecion->uid}/document/{$documentUid}/file/sign/{$fileUid}
     *
     * @return DigitalClaim\Scale\Response
     */
    public function getSignedUrlToReadFile(string $documentUid, string $fileUid, string $mime = 'application/octet-stream'): Response
    {
        return $this->client->get("/collection/{$this->uid}/document/$documentUid/file/sign/$fileUid?mime=" . urlencode($mime));
    }

    /**
     * path: /api/collection/{$this->collecion->uid}/document/{$documentUid}/file/sign
     *
     * @return DigitalClaim\Scale\Response
     */
    public function getSignedUrlToPutFile(string $documentUid, string $name, array $meta = [], string $mime = 'application/octet-stream'): Response
    {
        return $this->client->post("/collection/{$this->uid}/document/$documentUid/file/sign", [
            'data' => [
                'name' => $name,
                'meta' => $meta,
                'mime' => $mime,
            ],
        ]);
    }

    /**
     * path: /method/invoke/{$method}
     *
     * @return DigitalClaim\Scale\Response
     */
    public function invoke(string $method, array $data = []): Response
    {
        return $this->client->post("/method/invoke/{$method}", [
            'data' => $data,
        ]);
    }
}
