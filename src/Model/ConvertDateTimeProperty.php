<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\Model;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
class ConvertDateTimeProperty implements ConvertPropertyInterface
{
    public ?string $property = null;
    public ?string $format = null;

    public static function new(string $property, string $format = \DateTimeInterface::ISO8601): self
    {
        $object = new self();
        $object->property = $property;
        $object->format = $format;

        return $object;
    }
}
