<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\Model;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
class ConvertUnsetsProperty implements ConvertPropertyInterface
{
    public array $properties = [];

    public static function new(array $properties = []): self
    {
        $object = new self();
        $object->properties = $properties;

        return $object;
    }
}
