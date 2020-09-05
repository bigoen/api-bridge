<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\Model\Traits;

use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
trait ArrayToObjectTrait
{
    public static function arrayToObject(string $class, array $arr, ?string $idProperty = null, $id = null): object
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $model = new $class();
        foreach ($arr as $property => $value) {
            if ($accessor->isWritable($model, $property)) {
                $accessor->setValue($model, $property, $value);
            }
        }
        if (!is_null($idProperty) && !is_null($id) && $accessor->isWritable($model, $idProperty)) {
            $accessor->setValue($model, $idProperty, $id);
        }

        return $model;
    }
}
