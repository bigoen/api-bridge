<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\HttpClient\Traits;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
trait PostTrait
{
    public function post(object $model): object
    {
        $class = get_class($model);

        return self::arrayToObject(new $class(), $this->postToArray(self::objectToArray($model)));
    }

    public function postToArray(array $arr): array
    {
        return $this->request('POST', $this->getUrl(), $arr);
    }
}
