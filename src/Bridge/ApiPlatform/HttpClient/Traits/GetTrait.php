<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\Bridge\ApiPlatform\HttpClient\Traits;

use Bigoen\ApiBridge\Bridge\ApiPlatform\Model\JsonldPagination;
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
        return $this->request('GET', $this->getItemUrl());
    }

    public function getToResponse(): ResponseInterface
    {
        return $this->setReturnOnlyResponse(true)->request('GET', $this->getItemUrl());
    }

    public function getAll(): JsonldPagination
    {
        return JsonldPagination::new($this->class, $this->getAllToArray(), $this->convertProperties);
    }

    public function getAllToArray(): array
    {
        return $this->request('GET', $this->getUrl());
    }
}
