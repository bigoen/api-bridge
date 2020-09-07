<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\Bridge\ApiPlatform\Model;

use Bigoen\ApiBridge\Bridge\ApiPlatform\Model\Traits\ArrayObjectConverterTrait;
use Bigoen\ApiBridge\Bridge\ApiPlatform\Model\Traits\JsonldModelTrait;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
class JsonldPagination
{
    use ArrayObjectConverterTrait, JsonldModelTrait;

    public ?string $jsonldContext = null;
    public array $members = [];
    public ?int $totalItems = null;
    public ?string $firstPagePath = null;
    public ?string $lastPagePath = null;
    public ?string $nextPagePath = null;

    public static function new(string $class, array $data): self
    {
        $object = new self();
        $object->jsonldContext = $data['@context'] ?? null;
        $object->jsonldId = $data['@id'] ?? null;
        $object->jsonldType = $data['@type'] ?? null;
        if (isset($data['hydra:member'])) {
            foreach ($data['hydra:member'] as $value) {
                $object->members[] = self::arrayToObject($class, $value);
            }
        }
        $object->totalItems = $data['hydra:totalItems'] ?? null;
        $object->firstPagePath = $data['hydra:view']['hydra:first'] ?? null;
        $object->lastPagePath = $data['hydra:view']['hydra:last'] ?? null;
        $object->nextPagePath = $data['hydra:view']['hydra:next'] ?? null;

        return $object;
    }
}
