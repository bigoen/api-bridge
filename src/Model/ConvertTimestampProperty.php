<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\Model;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
class ConvertTimestampProperty implements ConvertPropertyInterface
{
    public ?string $property = null;

    public static function new(string $property): self
    {
        $object = new self();
        $object->property = $property;

        return $object;
    }
}
