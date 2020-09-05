<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\Model\Traits;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
trait ArrayObjectConverterTrait
{
    public static function arrayToObject(string $class, array $arr): object
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $model = new $class();
        foreach ($arr as $property => $value) {
            if ($accessor->isWritable($model, $property)) {
                $accessor->setValue($model, $property, $value);
            }
        }

        return $model;
    }

    public static function objectToArray(object $model): array
    {
        $propertyInfo = new PropertyInfoExtractor([new ReflectionExtractor()]);
        $accessor = PropertyAccess::createPropertyAccessor();
        $arr = [];
        foreach ($propertyInfo->getProperties(get_class($model)) as $property) {
            if ($accessor->isReadable($model, $property)) {
                $arr[$property] = $accessor->getValue($model, $property);
            }
        }

        return $arr;
    }
}
