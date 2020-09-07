<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\Bridge\ApiPlatform\Model\Traits;

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

    public static function arrayToObject(string $class, array $arr): object
    {
        $accessor = self::getPropertyAccessor();
        $model = new $class();
        foreach ($arr as $property => $value) {
            if ($accessor->isWritable($model, $property)) {
                $accessor->setValue($model, $property, $value);
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
