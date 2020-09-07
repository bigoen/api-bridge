<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\Bridge\ApiPlatform\HttpClient;

use Bigoen\ApiBridge\Bridge\ApiPlatform\HttpClient\Traits\GetTrait;
use Bigoen\ApiBridge\Bridge\ApiPlatform\Model\Traits\ArrayObjectConverterTrait;
use Bigoen\ApiBridge\HttpClient\AbstractClient;
use Bigoen\ApiBridge\HttpClient\Traits\DeleteTrait;
use Bigoen\ApiBridge\HttpClient\Traits\PostTrait;
use Bigoen\ApiBridge\HttpClient\Traits\PutTrait;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
class JsonldClient extends AbstractClient
{
    use GetTrait, PostTrait, PutTrait, DeleteTrait, ArrayObjectConverterTrait;
}
