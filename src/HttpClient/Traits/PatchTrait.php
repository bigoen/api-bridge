<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\HttpClient\Traits;

use Symfony\Component\HttpFoundation\Response;
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
        // check is success.
        if (Response::HTTP_OK === $response->getStatusCode()) {
            $class = $this->getClass();
            $convertProperties = $this->convertProperties;
        } else {
            $class = $this->getThrowClass();
            $convertProperties = $this->throwConvertProperties;
        }

        return self::arrayToObject(
            new $class(),
            $response->toArray($this->isThrow()),
            $convertProperties
        );
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
