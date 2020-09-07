<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\HttpClient\Traits;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
trait GetTrait
{
    public function get(): ?object
    {
        return self::arrayToObject($this->class, $this->getToArray());
    }

    public function getToArray(): array
    {
        return $this->request('GET', $this->getItemUrl());
    }

    public function getAll(?string $dataPath = null): array
    {
        $objects = [];
        $arr = $this->getAllToArray();
        if (is_string($dataPath) && isset($arr[$dataPath])) {
            $arr = $arr[$dataPath];
        }
        foreach ($arr as $value) {
            $objects[] = self::arrayToObject($this->class, $value);
        }

        return $objects;
    }

    public function getAllToArray(): array
    {
        return $this->request('GET', $this->getUrl());
    }
}
