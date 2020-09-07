<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\HttpClient\Traits;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
trait DeleteTrait
{
    public function delete(): bool
    {
        return 204 === $this->request('DELETE', $this->getItemUrl());
    }
}
