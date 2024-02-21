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
        $response = $this->getToResponse();
        // check is success.
        if (Response::HTTP_OK === $response->getStatusCode()) {
            $class = $this->getClass();
            $convertProperties = $this->convertProperties;
        } else {
            $class = $this->getThrowClass();
            $convertProperties = $this->throwConvertProperties;
        }

        return self::arrayToObject(new $class(), $response->toArray($this->isThrow()), $convertProperties);
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
