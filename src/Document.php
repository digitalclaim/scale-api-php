<?php

namespace DigitalClaim\Scale;

use DigitalClaim\Scale\Auth;
use DigitalClaim\Scale\Request;
use Illuminate\Http\Client\Response;

class Document
{
    /**
     * @var DigitalClaim\Scale\Request
     */
    protected $request;

    /**
     * @var String
     */
    protected $collectionUid;

    /**
     *
     */
    public function __construct(?String $collectionUid = null, ?Auth $auth = null)
    {
        $this->request       = new Request($auth);
        $this->collectionUid = $collectionUid ?? env('SCALE_DEFAULT_COLLECTION');
    }

    /**
     * path: /api/collection/{collectionUid}/document/get/{documentUid}
     *
     * @return Illuminate\Http\Client\Response
     */
    public function get(string $uid, array $data): Response
    {
        return $this->request->post("/collection/{$this->collectionUid}/document/get/$uid", $data);
    }

    /**
     * path: /api/collection/{collectionUid}/document/create
     *
     * @return Illuminate\Http\Client\Response
     */
    public function create(array $data): Response
    {
        return $this->request->post("/collection/{$this->collectionUid}/document/create", $data);
    }

    /**
     * path: /api/collection/{collectionUid}/document/delete/{documentUid}
     *
     * @return Illuminate\Http\Client\Response
     */
    public function delete(string $uid, bool $softdelete = false): Response
    {
        return $this->request->post("/collection/{$this->collectionUid}/document/delete/$uid", [
            'data' => [
                'softdelete' => $softdelete,
            ],
        ]);
    }

    /**
     * path: /api/collection/{collectionUid}/document/update/{uid}
     *
     * @return Illuminate\Http\Client\Response
     */
    public function update(string $uid, array $data): Response
    {
        return $this->request->post("/collection/{$this->collectionUid}/document/update/$uid", $data);
    }

    /**
     * path: /api/collection/{collectionUid}/document/paginate/{page}
     *
     * @return Illuminate\Http\Client\Response
     */
    public function paginate(string $page, array $data, int $size = 20): Response
    {
        return $this->request->post("/collection/{$this->collectionUid}/document/paginate/$page/$size", $data);
    }

    /**
     * path: /api/collection/{collectionUid}/document/push
     *
     * @return Illuminate\Http\Client\Response
     */
    public function push(string $path, $item, bool $unique, array $filter = [])
    {
        return $this->request->post("/collection/{$this->collectionUid}/document/push", [
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
     * @return Illuminate\Http\Client\Response
     */
    public function aggregate(array $aggregation, array $filter = []): Response
    {
        return $this->request->post("/collection/{$this->collectionUid}/document/aggregate", [
            'data' => [
                'aggregation' => $aggregation,
                'filter'      => $filter,
            ],
        ]);
    }

    /**
     * path: /api/collection/{$this->collecion->uid}/document/{$documentUid}/file/get/{$fileUid}
     *
     * @return Illuminate\Http\Client\Response
     */
    public function getFile(string $documentUid, string $fileUid): Response
    {
        return $this->request->get("/collection/{$this->collectionUid}/document/$documentUid/file/get/$fileUid");
    }

    /**
     * path: /api/collection/{$this->collecion->uid}/document/{$documentUid}/file/get/{$fileUid}/meta
     *
     * @return Illuminate\Http\Client\Response
     */
    public function getFileMeta(string $documentUid, string $fileUid): Response
    {
        return $this->request->get("/collection/{$this->collectionUid}/document/$documentUid/file/get/$fileUid/meta");
    }

    /**
     * path: /api/collection/{$this->collecion->uid}/document/{$documentUid}/file/update/{$fileUid}
     *
     * @return Illuminate\Http\Client\Response
     */
    public function updateFile(string $documentUid, string $fileUid, string $name, array $meta): Response
    {
        return $this->request->post("/collection/{$this->collectionUid}/document/$documentUid/file/update/$fileUid", [
            'data' => [
                'name' => $name,
                'meta' => $meta,
            ],
        ]);
    }

    /**
     * path: /api/collection/{$this->collecion->uid}/document/{$documentUid}/file/delete/{$fileUid}
     *
     * @return Illuminate\Http\Client\Response
     */
    public function deleteFile(string $documentUid, string $fileUid): Response
    {
        return $this->request->post("/collection/{$this->collectionUid}/document/$documentUid/file/delete/$fileUid", []);
    }

    /**
     * path: /api/collection/{$this->collecion->uid}/document/{$documentUid}/file/create
     *
     * @return Illuminate\Http\Client\Response
     */
    public function putFile(string $documentUid, string $name, string $payload, array $meta = []): Response
    {
        return $this->request->post("/collection/{$this->collectionUid}/document/$documentUid/file/create", [
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
     * @return Illuminate\Http\Client\Response
     */
    public function getSignedUrlToReadFile(string $documentUid, string $fileUid, string $mime = 'application/octet-stream'): Response
    {
        return $this->request->get("/collection/{$this->collectionUid}/document/$documentUid/file/sign/$fileUid?mime=" . urlencode($mime));
    }

    /**
     * path: /api/collection/{$this->collecion->uid}/document/{$documentUid}/file/sign
     *
     * @return Illuminate\Http\Client\Response
     */
    public function getSignedUrlToPutFile(string $documentUid, string $name, array $meta = [], string $mime = 'application/octet-stream'): Response
    {
        return $this->request->post("/collection/{$this->collectionUid}/document/$documentUid/file/sign", [
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
     * @return Illuminate\Http\Client\Response
     */
    public function invoke(string $method, array $data = []): Response
    {
        return $this->request->post("/method/invoke/{$method}", [
            'data' => $data,
        ]);
    }
}
