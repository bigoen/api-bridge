<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\HttpClient;

use Bigoen\ApiBridge\Model\Traits\ArrayObjectConverterTrait;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
abstract class AbstractService
{
    use ArrayObjectConverterTrait;

    const JSON = 'json';
    const JSONLD = 'jsonld';
    const XML = 'xml';

    protected HttpClientInterface $httpClient;

    static array $formats = [
        self::JSON => ['application/json', 'application/x-json'],
        self::JSONLD => ['application/ld+json'],
        self::XML => ['text/xml', 'application/xml', 'application/x-xml'],
    ];

    /**
     * @var string|int|null
     */
    protected $id = null;
    protected ?string $class = null;
    protected ?string $baseUrl = null;
    protected ?string $path = null;
    protected ?string $contentType = null;
    protected array $options = [];

    protected string $idProperty = 'id';
    protected string $idPath = '{id}';
    protected string $format = self::JSON;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws DecodingExceptionInterface
     */
    public function request(string $method, string $url, ?array $data = null): array
    {
        if (is_array($data) && in_array($method, ['POST', 'PUT'])) {
            if (self::XML === $this->format) {
                $data = (new XmlEncoder())->encode($data, self::XML);
                $this->options['body'] = $data;
                $this->options['headers']['Content-Type'] = 'text/xml; charset=utf-8';
            } else {
                $this->options['json'] = $data;
            }
        }
        $response = $this->httpClient->request($method, $url, $this->options);
        $this->setContentType($response);
        if (self::XML === $this->contentType) {
            return (new XmlEncoder())->decode($response->getContent(), 'array');
        }

        return $response->toArray();
    }

    public function getUrl(): string
    {
        return $this->baseUrl.$this->path;
    }

    public function getItemUrl(): string
    {
        return str_replace(
            $this->idPath,
            $this->id,
            $this->getUrl()
        );
    }

    public function clear(bool $isClearBaseUrl = true): self
    {
        $this->id = null;
        $this->class = null;
        if (true === $isClearBaseUrl) {
            $this->baseUrl = null;
        }
        $this->path = null;
        $this->options = [];

        return $this;
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function setContentType(ResponseInterface $response): self
    {
        $contentType = $response->getHeaders()['content-type'][0];
        foreach (static::$formats as $name => $format) {
            foreach ($format as $strFormat) {
                if (strpos($contentType, $strFormat) !== false) {
                    $this->contentType = $name;

                    return $this;
                }
            }
        }

        return $this;
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
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
