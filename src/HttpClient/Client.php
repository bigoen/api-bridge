<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\HttpClient;

use Bigoen\ApiBridge\HttpClient\Traits\DeleteTrait;
use Bigoen\ApiBridge\HttpClient\Traits\GetTrait;
use Bigoen\ApiBridge\HttpClient\Traits\PostTrait;
use Bigoen\ApiBridge\HttpClient\Traits\PutTrait;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
class Client extends AbstractClient
{
    use GetTrait, PostTrait, PutTrait, DeleteTrait;
}
