<?php

require_once __DIR__ . '/vendor/autoload.php';

use DigitalClaim\Scale\Auth;
use DigitalClaim\Scale\Collection;

$auth = new Auth(
    'https://scale.local:8890/api',
    'sqlKnsj7iVb57GLwRk4LpigyThlmW3wP',
    'cREI4agy8UNriHXCTDC0hqstefQdWPvtV4wrcolu1iJVOuEWq1EbHhnCuuZW9MIm',
    'test'
);

$collection = new Collection('claims-8d60484b-aef1-45f0-b7da-5028cde54520-1662472838', $auth);

$data = $collection->paginate(1, [
    'data' => [],
], 1)->json();

print_r($data);

$data = $collection->paginate(2, [
    'data' => [],
], 1)->json();

print_r($data);

$data = $collection->getFileMeta('63175286c48ad845421d0d52', 'dbfe582a-d6a1-48c4-bf5b-9a356a3d00ff-1683127009')->json();

print_r($data);
