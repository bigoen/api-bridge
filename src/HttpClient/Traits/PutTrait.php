<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\HttpClient\Traits;

use Symfony\Component\HttpFoundation\Response;
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
        $response = $this->putToResponse(self::objectToArray($model, $this->sendingConvertProperties));

        return $this->responseToObject($response);
    }

    public function responseToObject(ResponseInterface $response): object
    {
        // check is success.
        if (Response::HTTP_OK === $response->getStatusCode()) {
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

    public function putToArray(array $arr): array
    {
        return $this->request('PUT', $this->getItemUrl(), $arr);
    }

    public function putToResponse(array $arr): ResponseInterface
    {
        return $this->setReturnOnlyResponse(true)->request('PUT', $this->getUrl(), $arr);
    }
}
