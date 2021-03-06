<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\Bridge\ApiPlatform\Model\Traits;

use Bigoen\ApiBridge\Model\ConvertDateTimeProperty;
use Bigoen\ApiBridge\Model\ConvertTimestampProperty;
use Bigoen\ApiBridge\Model\ConvertTreeProperty;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\PropertyAccess\Exception\InvalidArgumentException;
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
        $arr = self::convertProperties($convertProperties, $arr);
        foreach ($arr as $property => $value) {
            if ($accessor->isWritable($model, $property)) {
                $propertyValue = $accessor->getValue($model, $property);
                if ($propertyValue instanceof Collection && is_array($value)) {
                    foreach ($value as $data) {
                        $propertyValue->add($data);
                    }
                } else {
                    try {
                        $accessor->setValue($model, $property, $value);
                    } catch (InvalidArgumentException $e) {
                        // not convert or php <= 7.4 type errors.
                    }
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

    public static function convertProperties(array $convertProperties, array $arr): array
    {
        foreach ($convertProperties as $convertProperty) {
            if ($convertProperty instanceof ConvertTreeProperty) {
                $arr = self::convertTreeProperty($convertProperty, $arr);
            } elseif ($convertProperty instanceof ConvertDateTimeProperty) {
                $arr = self::convertDateTimeProperty($convertProperty, $arr);
            } elseif ($convertProperty instanceof ConvertTimestampProperty) {
                $arr = self::convertTimestampProperty($convertProperty, $arr);
            }
        }

        return $arr;
    }

    public static function convertTreeProperty(ConvertTreeProperty $convertProperty, array $arr): array
    {
        $accessor = self::getPropertyAccessor();
        $property = $convertProperty->property;
        $deep = $convertProperty->deep;
        $convertValues = $convertProperty->items;
        $subConvertProperties = $convertProperty->convertProperties;
        $haveSubConvertProperties = count($subConvertProperties) > 0;
        if (false !== strpos($deep, '[]') && $accessor->isWritable($arr, $property)) {
            $items = [];
            $subArr = $accessor->getValue($arr, $property) ?? [];
            foreach ($subArr as $key => $item) {
                $deepKey = str_replace('[]', "[$key]", $deep);
                $accessValue = $accessor->getValue($arr, $deepKey);
                if (!is_null($accessValue) && isset($convertValues[$accessValue])) {
                    if (is_string($item)) {
                        $items[] = $convertValues[$accessValue];
                    } elseif (is_array($item)) {
                        if (true === $haveSubConvertProperties) {
                            $item = self::convertProperties($subConvertProperties, $item);
                        }
                        $items[] = self::arrayToObject($convertValues[$accessValue], $item);
                    }
                } elseif (is_array($item)) {
                    if (true === $haveSubConvertProperties) {
                        $item = self::convertProperties($subConvertProperties, $item);
                    }
                    if (is_string($convertProperty->itemClass)) {
                        $items[] = self::arrayToObject(new $convertProperty->itemClass(), $item);
                    }
                }
            }
            $accessor->setValue($arr, $property, $items);
        } else {
            if ($accessor->isReadable($arr, $deep) && $accessor->isWritable($arr, $property)) {
                $accessValue = $accessor->getValue($arr, $deep);
                $onlyConvertAccessValue = $accessor->getValue($arr, $property);
                if (!is_null($accessValue) && isset($convertValues[$accessValue])) {
                    $accessor->setValue(
                        $arr,
                        $property,
                        $convertValues[$accessValue]
                    );
                } elseif (!is_null($onlyConvertAccessValue) && is_array($onlyConvertAccessValue)) {
                    if (true === $haveSubConvertProperties) {
                        $onlyConvertAccessValue = self::convertProperties($subConvertProperties, $onlyConvertAccessValue);
                    }
                    if (is_string($convertProperty->itemClass)) {
                        $accessor->setValue(
                            $arr,
                            $property,
                            self::arrayToObject(new $convertProperty->itemClass(), $onlyConvertAccessValue)
                        );
                    }
                }
            }
        }

        return $arr;
    }

    public static function convertDateTimeProperty(ConvertDateTimeProperty $convertProperty, array $arr): array
    {
        $accessor = self::getPropertyAccessor();
        $property = $convertProperty->property;
        if (!$accessor->isReadable($arr, $property)) {
            return $arr;
        }
        $strValue = $accessor->getValue($arr, $property);
        if (is_string($strValue)) {
            $value = DateTime::createFromFormat($convertProperty->format, $strValue);
            $accessor->setValue($arr, $property, $value);
        }

        return $arr;
    }

    public static function convertTimestampProperty(ConvertTimestampProperty $convertProperty, array $arr): array
    {
        $accessor = self::getPropertyAccessor();
        $property = $convertProperty->property;
        if (!$accessor->isReadable($arr, $property)) {
            return $arr;
        }
        $intValue = $accessor->getValue($arr, $property);
        if (is_int($intValue)) {
            $value = (new DateTime())->setTimestamp($intValue);
            $accessor->setValue($arr, $property, $value);
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
