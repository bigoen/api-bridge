<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\HttpClient;

use Bigoen\ApiBridge\Constant\ResourceConstant;
use Bigoen\ApiBridge\Model\Traits\ArrayToObjectTrait;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
abstract class AbstractService
{
    use ArrayToObjectTrait;

    protected HttpClientInterface $httpClient;

    /**
     * @var string|int|null
     */
    protected $id = null;
    protected ?string $class = null;
    protected ?string $baseUrl = null;
    protected ?string $path = null;
    protected array $options = [];

    protected string $idProperty = ResourceConstant::DEFAULT_ID_PROPERTY;
    protected string $idPath = ResourceConstant::DEFAULT_ID_PATH;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getItemUrl(): string
    {
        return str_replace(
            $this->idPath,
            $this->id,
            $this->baseUrl.$this->path
        );
    }

    public function clear(bool $isClearBaseUrl = true): self
    {
        $this->class = null;
        if (true === $isClearBaseUrl) {
            $this->baseUrl = null;
        }
        $this->path = null;
        $this->options = [];
    }

    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setClass(?string $class): self
    {
        $this->class = $class;

        return $this;
    }

    public function setBaseUrl(?string $baseUrl): self
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function setIdProperty(string $idProperty): self
    {
        $this->idProperty = $idProperty;

        return $this;
    }

    public function setIdPath(string $idPath): self
    {
        $this->idPath = $idPath;

        return $this;
    }
}
