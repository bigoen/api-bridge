<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\HttpClient\Traits;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
trait PutTrait
{
    public function put(object $model): object
    {
        return self::arrayToObject(get_class($model), $this->putToArray(self::objectToArray($model)));
    }

    public function putToArray(array $arr): array
    {
        return $this->request('PUT', $this->getItemUrl(), $arr);
    }
}
