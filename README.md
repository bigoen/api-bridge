Api Bridge
==
Install:
```
composer require bigoen/api-bridge
```

SimpleClient
==
Create model:
```php
<?php

namespace App\Model;

class Example 
{
    public ?string $name = null;
    public ?string $email = null;
}
```

If you want error class, create model:
```php
<?php

namespace App\Model;

class Error 
{
    public ?string $status = null;
    public ?string $message = null;
}
```

Create client:
```php
use App\Model\Example;
use App\Model\Error;
use Symfony\Component\HttpClient\HttpClient;
use Bigoen\ApiBridge\HttpClient\SimpleClient;

$httpClient = HttpClient::create();
$client = new SimpleClient($httpClient); 
$client
    ->setBaseUrl("http://example.com")
    ->setClass(Example::class)
    ->setThrowClass(Error:class)
    ->setOptions([
        // Set http client request options.
    ]);

// all objects.
$client->setPath("/api/examples")->getAll();

// get object with id.
$client->setPath("/api/examples/{id}")->setId(1)->get();

// post object.
$model = new Example();
$model->name = 'Test';
$model->email = 'test@example.com';
$postModel = $client->setPath("/api/examples")->post($model);

// put object.
$model = $client->setPath("/api/examples/{id}")->setId(1)->get();
$model->name = 'New Name';
$model = $client->setPath("/api/examples/{id}")->put($model);

// delete object.
$isDelete = $client->setPath("/api/examples/{id}")->setId(1)->delete();
```

JsonldClient
==
Create model:
```php
<?php

namespace App\Model;

use Bigoen\ApiBridge\Bridge\ApiPlatform\Model\Traits\JsonldModelTrait;

class Example 
{
    use JsonldModelTrait;

    public ?string $name = null;
    public ?string $email = null;
}
```

Create client for ApiPlatform projects or jsonld apis:
```php
use App\Model\Example;
use Symfony\Component\HttpClient\HttpClient;
use Bigoen\ApiBridge\Bridge\ApiPlatform\HttpClient\JsonldClient;

$httpClient = HttpClient::create();
$client = new JsonldClient($httpClient); 
$client
    ->setBaseUrl("http://example.com")
    ->setClass(Example::class)
    ->setOptions([
        // Set http client request options.
    ]);

// all objects.
$pageOne = $client->setPath("/api/examples")->getAll();
// get next page objects.
$pageTwo = $client->setPath($pageOne->nextPagePath)->getAll();

// get object with id.
$client->setPath("/api/examples/{id}")->setId(1)->get();

// post object.
$model = new Example();
$model->name = 'Test';
$model->email = 'test@example.com';
$postModel = $client->setPath("/api/examples")->post($model);

// put object.
$model = $client->setPath("/api/examples/{id}")->setId(1)->get();
$model->name = 'New Name';
$model = $client->setPath("/api/examples/{id}")->put($model);

// delete object.
$isDelete = $client->setPath("/api/examples/{id}")->setId(1)->delete();
```

Convert api values in tree.
```php
use Bigoen\ApiBridge\Bridge\ApiPlatform\HttpClient\JsonldClient;
use Bigoen\ApiBridge\Model\ConvertDateTimeProperty;
use Bigoen\ApiBridge\Model\ConvertTreeProperty;
use Bigoen\ApiBridge\Model\ConvertProperty;

$categories = $this->entityManager
    ->getRepository(Category::class)
    ->findAllIndexById();
$tags = $this->entityManager
    ->getRepository(Tag::class)
    ->findAllIndexById();
// set converts.
$convertProperties = [
    ConvertTreeProperty::new(
        '[category]',
        false,
        '[category][@id]',
        null,
        $this->getConvertValues('/api/categories/{id}', $categories)
    ),
    ConvertTreeProperty::new(
        '[tags]',
        true,
        '[tags][][@id]',
        Tag::class,
        $this->getConvertValues('/api/tags/{id}', $tags),
        [
            ConvertDateTimeProperty::new('[createdAt]'),
            ConvertDateTimeProperty::new('[updatedAt]'),
        ]
    ),
    ConvertDateTimeProperty::new('[createdAt]'),
    ConvertDateTimeProperty::new('[updatedAt]'),
    // class property and api property.
    ConvertProperty::new('[id]', '[_id]'),
];
/* @var $client JsonldClient */
$client->setConvertProperties($convertProperties);
```
Important: property and deep names details > https://symfony.com/doc/current/components/property_access.html
