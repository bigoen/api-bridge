<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\HttpClient\Traits;

use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
trait DeleteTrait
{
    public function delete(): bool
    {
        return $this->deleteSuccessStatusCode === $this->request('DELETE', $this->getItemUrl());
    }

    public function deleteToResponse(): ResponseInterface
    {
        return $this->setReturnOnlyResponse(true)->request('DELETE', $this->getItemUrl());
    }
}
