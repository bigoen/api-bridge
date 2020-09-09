<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\Bridge\ApiPlatform\Model\Traits;

use Bigoen\ApiBridge\Model\ConvertProperty;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
trait ArrayObjectConverterTrait
{
    static string $atId = 'jsonldId';
    static string $atType = 'jsonldType';

    protected static ?PropertyAccessor $propertyAccessor = null;
    protected static ?PropertyInfoExtractor $propertyInfo = null;

    public static function arrayToObject(object $model, array $arr, array $convertProperties = []): object
    {
        $accessor = self::getPropertyAccessor();
        foreach ($convertProperties as $convertProperty) {
            if (!$convertProperty instanceof ConvertProperty) {
                continue;
            }
            $property = $convertProperty->property;
            $deep = $convertProperty->deep;
            $convertValues = $convertProperty->items;
            if (false !== strpos($deep, '[]') && $accessor->isWritable($arr, $property)) {
                $items = [];
                foreach ($accessor->getValue($arr, $property) as $key => $item) {
                    $deepKey = str_replace('[]', "[$key]", $deep);
                    $accessValue = $accessor->getValue($arr, $deepKey);
                    if (!is_null($accessValue) && isset($convertValues[$accessValue])) {
                        $items[] = self::arrayToObject($convertValues[$accessValue], $item);
                    } else {
                        $items[] = self::arrayToObject(new $convertProperty->itemClass(), $item);
                    }
                }
                $accessor->setValue($arr, $property, $items);
            } else {
                if ($accessor->isReadable($arr, $deep) && $accessor->isWritable($arr, $property)) {
                    $accessValue = $accessor->getValue($arr, $deep);
                    if (!is_null($accessValue) && isset($convertValues[$accessValue])) {
                        $accessor->setValue(
                            $arr,
                            $property,
                            $convertValues[$accessValue]
                        );
                    }
                }
            }
        }
        foreach ($arr as $property => $value) {
            if ($accessor->isWritable($model, $property)) {
                $propertyValue = $accessor->getValue($model, $property);
                if ($propertyValue instanceof Collection && is_array($value)) {
                    foreach ($value as $data) {
                        $propertyValue->add($data);
                    }
                } else {
                    $accessor->setValue($model, $property, $value);
                }
            }
        }
        if (
            isset($arr['@id'], $arr['@type'])
            && $accessor->isWritable($model, self::$atId)
            && $accessor->isWritable($model, self::$atType)
        ) {
            $accessor->setValue($model, self::$atId, $arr['@id']);
            $accessor->setValue($model, self::$atType, $arr['@type']);
        }

        return $model;
    }

    public static function objectToArray(object $model): array
    {
        $propertyInfo = self::getPropertyInfo();
        $accessor = self::getPropertyAccessor();
        $arr = [];
        foreach ($propertyInfo->getProperties(get_class($model)) as $property) {
            if ($accessor->isReadable($model, $property)) {
                $arr[$property] = $accessor->getValue($model, $property);
            }
        }
        if (
            $accessor->isReadable($model, self::$atId)
            && $accessor->isReadable($model, self::$atType)
        ) {
            $arr['@id'] = $accessor->getValue($model, self::$atId);
            $arr['@type'] = $accessor->getValue($model, self::$atType);
        }

        return $arr;
    }

    public static function getPropertyAccessor(): PropertyAccessor
    {
        if (!self::$propertyAccessor instanceof PropertyAccessor) {
            self::$propertyAccessor = PropertyAccess::createPropertyAccessor();
        }

        return self::$propertyAccessor;
    }

    public static function getPropertyInfo(): PropertyInfoExtractor
    {
        if (!self::$propertyInfo instanceof PropertyInfoExtractor) {
            self::$propertyInfo = new PropertyInfoExtractor([new ReflectionExtractor()]);
        }

        return self::$propertyInfo;
    }
}
