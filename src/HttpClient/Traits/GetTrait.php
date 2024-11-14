<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\HttpClient\Traits;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
trait GetTrait
{
    public function get(): ?object
    {
        return $this->responseToObject($this->getToResponse());
    }

    public function getToArray(): array
    {
        return $this->setReturnOnlyResponse(false)->request('GET', $this->getItemUrl());
    }

    public function getToResponse(): ResponseInterface
    {
        return $this->setReturnOnlyResponse(true)->request('GET', $this->getItemUrl());
    }

    public function getAll(array $convertProperties = []): object|array
    {
        $objects = [];
        $response = $this->getToResponse();
        // check is success.
        if (Response::HTTP_OK === $response->getStatusCode()) {
            $arr = $response->toArray($this->isThrow());
            if (\count($convertProperties) > 0) {
                $arr = self::convertProperties($convertProperties, $arr);
            }
            foreach ($arr as $value) {
                $objects[] = self::arrayToObject(new $this->class(), $value, $this->convertProperties);
            }
        } else {
            $objects = self::arrayToObject(new $this->throwClass(), $response->toArray($this->isThrow()), $this->throwConvertProperties);
        }

        return $objects;
    }

    public function getAllToArray(): array
    {
        return $this->setReturnOnlyResponse(false)->request('GET', $this->getUrl());
    }
}
