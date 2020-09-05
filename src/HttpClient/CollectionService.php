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
class CollectionService extends AbstractService
{
    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function post(object $model): object
    {
        return self::arrayToObject(get_class($model), $this->postFromArray(self::objectToArray($model)));
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function get(): array
    {
        $objects = [];
        $arr = $this->getFromArray();
        if (self::JSONLD === $this->getContentType()) {
            $arr = $arr['hydra:member'];
        }
        foreach ($arr as $value) {
            $objects[] = self::arrayToObject($this->class, $value);
        }

        return $objects;
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function postFromArray(array $arr): array
    {
        return $this->request('POST', $this->getUrl(), $arr);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getFromArray(): array
    {
        return $this->request('GET', $this->getUrl());
    }
}
