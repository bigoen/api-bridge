<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\HttpClient\Traits;

use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
trait PatchTrait
{
    public function patch(object $model): object
    {
        // request directly.
        if (true === $this->isThrow() || null === $this->getThrowClass()) {
            $class = $this->getClass();

            return self::arrayToObject(
                new $class(),
                $this->patchToArray(self::objectToArray($model, $this->sendingConvertProperties)),
                $this->convertProperties
            );
        }
        $response = $this->patchToResponse(self::objectToArray($model, $this->sendingConvertProperties));

        return $this->responseToObject($response);
    }

    public function patchToArray(array $arr): array
    {
        return $this->request('PATCH', $this->getItemUrl(), $arr);
    }

    public function patchToResponse(array $arr): ResponseInterface
    {
        return $this->setReturnOnlyResponse(true)->request('PATCH', $this->getUrl(), $arr);
    }
}
