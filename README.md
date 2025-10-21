# scale-api-php

PHP library for ARGUS.scale

### Example

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use DigitalClaim\Scale\Auth;
use DigitalClaim\Scale\Document;

$auth = new Auth(
    'CLIENT_URL',
    'CLIENT_ID',
    'CLIENT_SECRET',
    'test'
);

$repository = new Document('SOME_DOCUMENT_COLLECTION', $auth);

$data = $repository->paginate(1, [
    'data' => [],
], 10);

print_r($data);
```
