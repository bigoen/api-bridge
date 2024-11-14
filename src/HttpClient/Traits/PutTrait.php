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
        // request directly.
        if (true === $this->isThrow() || null === $this->getThrowClass()) {
            $class = $this->getClass();

            return self::arrayToObject(
                new $class(),
                $this->putToArray(self::objectToArray($model, $this->sendingConvertProperties)),
                $this->convertProperties
            );
        }
        $response = $this->putToResponse($model);

        return $this->responseToObject($response);
    }

    public function putToArray(array $arr): array
    {
        return $this->request('PUT', $this->getItemUrl(), $arr);
    }

    public function putToResponse(array|object $arr): ResponseInterface
    {
        if (is_object($arr)) {
            $arr = self::objectToArray($arr, $this->sendingConvertProperties);
        }

        return $this->setReturnOnlyResponse(true)->request('PUT', $this->getUrl(), $arr);
    }
}
