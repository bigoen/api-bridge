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

    public function getAll(): JsonldPagination
    {
        return JsonldPagination::new($this->class, $this->getAllToArray(), $this->convertProperties);
    }

    public function getAllToArray(): array
    {
        return $this->setReturnOnlyResponse(false)->request('GET', $this->getUrl());
    }
}
