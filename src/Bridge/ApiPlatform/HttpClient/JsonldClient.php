<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\Bridge\ApiPlatform\HttpClient;

use Bigoen\ApiBridge\HttpClient\AbstractClient;
use Bigoen\ApiBridge\HttpClient\Traits\DeleteTrait;
use Bigoen\ApiBridge\HttpClient\Traits\GetTrait;
use Bigoen\ApiBridge\HttpClient\Traits\PostTrait;
use Bigoen\ApiBridge\HttpClient\Traits\PutTrait;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
class JsonldClient extends AbstractClient
{
    use GetTrait, PostTrait, PutTrait, DeleteTrait;

    const JSONLD_ID = 'jsonldId';
    const JSONLD_TYPE = 'jsonldType';

    public static function arrayToObject(string $class, array $arr): object
    {
        $model = parent::arrayToObject($class, $arr);
        $accessor = PropertyAccess::createPropertyAccessor();
        if (
            isset($arr['@id'], $arr['@type'])
            && $accessor->isWritable($model, self::JSONLD_ID)
            && $accessor->isWritable($model, self::JSONLD_TYPE)
        ) {
            $accessor->setValue($model, self::JSONLD_ID, $arr['@id']);
            $accessor->setValue($model, self::JSONLD_TYPE, $arr['@type']);
        }

        return $model;
    }

    public static function objectToArray(object $model): array
    {
        $arr = parent::objectToArray($model);
        $accessor = PropertyAccess::createPropertyAccessor();
        if (
            $accessor->isReadable($model, self::JSONLD_ID)
            && $accessor->isReadable($model, self::JSONLD_TYPE)
        ) {
            $arr['@id'] = $accessor->getValue($model, self::JSONLD_ID);
            $arr['@type'] = $accessor->getValue($model, self::JSONLD_TYPE);
        }

        return $arr;
    }
}
