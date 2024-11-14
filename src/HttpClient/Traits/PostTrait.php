<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\HttpClient\Traits;

use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
trait PostTrait
{
    public function post(?object $model = null): object
    {
        // request directly.
        if (true === $this->isThrow() || null === $this->getThrowClass()) {
            $class = $this->getClass();

            return self::arrayToObject(
                new $class(),
                $this->postToArray(self::objectToArray($model, $this->sendingConvertProperties)),
                $this->convertProperties
            );
        }
        $response = $this->postToResponse($model);

        return $this->responseToObject($response);
    }

    public function postToArray(array $arr): array
    {
        return $this->request('POST', $this->getUrl(), $arr);
    }

    public function postToResponse(array|object $arr): ResponseInterface
    {
        if (is_object($arr)) {
            $arr = self::objectToArray($arr, $this->sendingConvertProperties);
        }

        return $this->setReturnOnlyResponse(true)->request('POST', $this->getUrl(), $arr);
    }
}
