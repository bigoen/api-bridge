<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\HttpClient\Traits;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
trait PostTrait
{
    public function post(object $model): object
    {
        // request directly.
        if (true === $this->isThrow() || null === $this->getThrowClass()) {
            $class = $this->getClass();

            return self::arrayToObject(
                new $class(),
                $this->postToArray(self::objectToArray($model)),
                $this->convertProperties
            );
        }
        $response = $this->postToResponse(self::objectToArray($model));
        // check is success.
        if (\in_array($response->getStatusCode(), [Response::HTTP_OK, Response::HTTP_CREATED])) {
            $class = $this->getClass();
            $convertProperties = $this->convertProperties;
        } else {
            $class = $this->getThrowClass();
            $convertProperties = $this->throwConvertProperties;
        }

        return self::arrayToObject(
            new $class(),
            $response->toArray($this->isThrow()),
            $convertProperties
        );
    }

    public function postToArray(array $arr): array
    {
        return $this->request('POST', $this->getUrl(), $arr);
    }

    public function postToResponse(array $arr): ResponseInterface
    {
        return $this->setReturnOnlyResponse(true)->request('POST', $this->getUrl(), $arr);
    }
}
