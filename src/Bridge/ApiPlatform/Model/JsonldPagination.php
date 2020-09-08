<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\Bridge\ApiPlatform\Model;

use Bigoen\ApiBridge\Bridge\ApiPlatform\Model\Traits\ArrayObjectConverterTrait;
use Bigoen\ApiBridge\Bridge\ApiPlatform\Model\Traits\JsonldModelTrait;
use Bigoen\ApiBridge\Model\ConvertProperty;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
class JsonldPagination
{
    use ArrayObjectConverterTrait, JsonldModelTrait;

    public ?string $jsonldContext = null;
    public array $members = [];
    public ?int $totalItems = null;
    public ?string $firstPagePath = null;
    public ?string $lastPagePath = null;
    public ?string $nextPagePath = null;

    public static function new(string $class, array $data, array $convertProperties = []): self
    {
        $object = new self();
        $object->jsonldContext = $data['@context'] ?? null;
        $object->jsonldId = $data['@id'] ?? null;
        $object->jsonldType = $data['@type'] ?? null;
        // hydra view details.
        $object->totalItems = $data['hydra:totalItems'] ?? null;
        $object->firstPagePath = $data['hydra:view']['hydra:first'] ?? null;
        $object->lastPagePath = $data['hydra:view']['hydra:last'] ?? null;
        $object->nextPagePath = $data['hydra:view']['hydra:next'] ?? null;
        if (!isset($data['hydra:member'])) {
            return $object;
        }
        $accessor = self::getPropertyAccessor();
        foreach ($data['hydra:member'] as $value) {
            foreach ($convertProperties as $convertProperty) {
                if (!$convertProperty instanceof ConvertProperty) {
                    continue;
                }
                $property = $convertProperty->property;
                $deep = $convertProperty->deep;
                $convertValues = $convertProperty->items;
                if (false !== strpos($deep, '[]') && $accessor->isWritable($value, $property)) {
                    $items = [];
                    foreach ($accessor->getValue($value, $property) as $key => $item) {
                        $deepKey = str_replace('[]', "[$key]", $deep);
                        $accessValue = $accessor->getValue($value, $deepKey);
                        if (!is_null($accessValue) && isset($convertValues[$accessValue])) {
                            $items[] = self::arrayToObject($convertValues[$accessValue], $item);
                        } else {
                            $items[] = self::arrayToObject(new $convertProperty->itemClass(), $item);
                        }
                    }
                    $accessor->setValue($value, $property, $items);
                } else {
                    if ($accessor->isReadable($value, $deep) && $accessor->isWritable($value, $property)) {
                        $accessValue = $accessor->getValue($value, $deep);
                        if (!is_null($accessValue) && isset($convertValues[$accessValue])) {
                            $accessor->setValue(
                                $value,
                                $property,
                                $convertValues[$accessValue]
                            );
                        }
                    }
                }
            }
            $object->members[] = self::arrayToObject(new $class(), $value);
        }

        return $object;
    }
}
