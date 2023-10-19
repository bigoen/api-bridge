<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\HttpClient\Traits;

use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
trait GetTrait
{
    public function get(): ?object
    {
        return self::arrayToObject(new $this->class(), $this->getToArray(), $this->convertProperties);
    }

    public function getToArray(): array
    {
        return $this->setReturnOnlyResponse(false)->request('GET', $this->getItemUrl());
    }

    public function getToResponse(): ResponseInterface
    {
        return $this->setReturnOnlyResponse(true)->request('GET', $this->getItemUrl());
    }

    public function getAll(array $convertProperties = []): array
    {
        $objects = [];
        $arr = $this->getAllToArray();
        if (\count($convertProperties) > 0) {
            $arr = self::convertProperties($convertProperties, $arr);
        }
        foreach ($arr as $value) {
            $objects[] = self::arrayToObject(new $this->class(), $value, $this->convertProperties);
        }

        return $objects;
    }

    public function getAllToArray(): array
    {
        return $this->setReturnOnlyResponse(false)->request('GET', $this->getUrl());
    }
}
