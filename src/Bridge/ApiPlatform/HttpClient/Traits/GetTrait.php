<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\Bridge\ApiPlatform\HttpClient\Traits;

use Bigoen\ApiBridge\Bridge\ApiPlatform\Model\JsonldPagination;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
trait GetTrait
{
    public function get(): ?object
    {
        return self::arrayToObject($this->class, $this->getToArray());
    }

    public function getToArray(): array
    {
        return $this->request('GET', $this->getItemUrl());
    }

    public function getAll(): JsonldPagination
    {
        return JsonldPagination::new($this->class, $this->getAllToArray());
    }

    public function getAllToArray(): array
    {
        return $this->request('GET', $this->getUrl());
    }
}
