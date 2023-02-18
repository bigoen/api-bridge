<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\Model;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
class ConvertProperty implements ConvertPropertyInterface
{
    public ?string $property = null;
    public ?string $apiProperty = null;

    public static function new(string $property, string $apiProperty): self
    {
        $object = new self();
        $object->property = $property;
        $object->apiProperty = $apiProperty;

        return $object;
    }
}
