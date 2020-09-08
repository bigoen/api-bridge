<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\Model;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
class ConvertProperty
{
    public ?string $property = null;
    public ?bool $isArray = null;
    public ?string $deep = null;
    public ?string $itemClass = null;
    public ?array $items = null;

    public static function new(string $property, bool $isArray, ?string $deep = null, ?string $itemClass = null, ?array $items = null): self
    {
        $object = new self();
        $object->property = $property;
        $object->isArray = $isArray;
        $object->deep = $deep ?? $property;
        $object->itemClass = $itemClass;
        $object->items = $items;

        return $object;
    }
}
