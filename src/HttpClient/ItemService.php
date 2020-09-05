<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\HttpClient;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
class ItemService extends AbstractService
{
    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getItem(): ?object
    {
        return self::arrayToObject(
            $this->class, $this->getItemToArray(), $this->idProperty, $this->id
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getItemToArray(): array
    {
        return $this->httpClient->request(
            'GET',
            $this->getItemUrl(),
            $this->options
        )->toArray();
    }

    public function clear(bool $isClearBaseUrl = true): self
    {
        $this->id = null;
        // clear parent properties.
        parent::clear($isClearBaseUrl);

        return $this;
    }
}
