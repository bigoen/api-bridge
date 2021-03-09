<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\HttpClient\Traits;

use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
trait PutTrait
{
    public function put(object $model): object
    {
        $class = get_class($model);
        
        return self::arrayToObject(new $class(), $this->putToArray(self::objectToArray($model)), $this->convertProperties);
    }

    public function putToArray(array $arr): array
    {
        return $this->request('PUT', $this->getItemUrl(), $arr);
    }

    public function putToResponse(array $arr): ResponseInterface
    {
        return $this->setReturnOnlyResponse(true)->request('PUT', $this->getUrl(), $arr);
    }
}
