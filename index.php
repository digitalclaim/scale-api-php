<?php

require_once __DIR__ . '/vendor/autoload.php';

use DigitalClaim\Scale\Auth;
use DigitalClaim\Scale\Document;

$auth = new Auth(
    'https://scale.local:8890/api',
    'sqlKnsj7iVb57GLwRk4LpigyThlmW3wP',
    'cREI4agy8UNriHXCTDC0hqstefQdWPvtV4wrcolu1iJVOuEWq1EbHhnCuuZW9MIm',
    'test'
);

$repository = new Document('claims-8d60484b-aef1-45f0-b7da-5028cde54520-1662472838', $auth);

$data = $repository->paginate(1, [
    'data' => [],
], 5)->json();

print_r($data);

$data = $repository->paginate(2, [
    'data' => [],
], 5)->json();

print_r($data);
