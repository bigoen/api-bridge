<?php

declare(strict_types=1);

namespace Bigoen\ApiBridge\HttpClient;

use Bigoen\ApiBridgeConverter\Model\Traits\ArrayObjectConverterTrait;
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
abstract class AbstractClient
{
    use ArrayObjectConverterTrait;

    public const JSON = 'json';
    public const JSONLD = 'jsonld';
    public const XML = 'xml';

    protected HttpClientInterface $httpClient;
    protected bool $throw = true;
    protected bool $returnOnlyResponse = false;

    protected int $deleteSuccessStatusCode = 204;

    public static array $formats = [
        self::JSON => ['application/json', 'application/x-json'],
        self::JSONLD => ['application/ld+json'],
        self::XML => ['text/xml', 'application/xml', 'application/x-xml'],
    ];

    /**
     * @var string|int|null
     */
    protected $id = null;
    protected ?string $class = null;
    protected ?string $throwClass = null;
    protected ?string $baseUrl = null;
    protected ?string $path = null;
    protected ?string $contentType = null;
    protected array $options = [];

    protected string $idProperty = 'id';
    protected string $idPath = '{id}';
    protected string $format = self::JSON;

    protected array $convertProperties = [];
    protected array $sendingConvertProperties = [];
    protected array $throwConvertProperties = [];

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @return array|int|ResponseInterface
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws DecodingExceptionInterface
     */
    public function request(string $method, string $url, ?array $data = null)
    {
        if (\is_array($data) && \in_array($method, ['POST', 'PUT', 'PATCH'])) {
            if (self::XML === $this->format) {
                $data = $this->xmlEncode($data);
                $this->options['body'] = $data;
                $this->options['headers']['Content-Type'] = 'text/xml; charset=utf-8';
            } elseif (\in_array($this->format, [self::JSON, self::JSONLD])) {
                $this->options['json'] = $data;
            } else {
                $this->options['body'] = $data;
            }
        }
        $response = $this->httpClient->request($method, $url, $this->options);
        if (true === $this->returnOnlyResponse) {
            $this->setContentType($response);

            return $response;
        }
        if ('DELETE' === $method) {
            return $response->getStatusCode();
        }
        $this->setContentType($response);
        if (self::XML === $this->contentType) {
            return $this->xmlDecode($response->getContent($this->throw), 'array');
        }

        return $response->toArray($this->throw);
    }

    public function xmlEncode($data, string $format = self::XML, array $context = []): ?string
    {
        $str = (new XmlEncoder())->encode($data, $format, $context);

        return \is_string($str) ? $str : null;
    }

    public function xmlDecode(string $data, string $format, array $context = []): ?array
    {
        $arr = (new XmlEncoder())->decode($data, $format, $context);

        return \is_array($arr) ? $arr : null;
    }

    public function getUrl(): string
    {
        return $this->baseUrl.$this->path;
    }

    public function getItemUrl(): string
    {
        return str_replace(
            $this->idPath,
            (string) $this->id,
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
        if (!isset($response->getHeaders($this->throw)['content-type'][0])) {
            return $this;
        }
        $contentType = $response->getHeaders($this->throw)['content-type'][0];
        foreach (static::$formats as $name => $format) {
            foreach ($format as $strFormat) {
                if (false !== strpos($contentType, $strFormat)) {
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

    public function setThrow(bool $throw): self
    {
        $this->throw = $throw;

        return $this;
    }

    public function setReturnOnlyResponse(bool $returnOnlyResponse): self
    {
        $this->returnOnlyResponse = $returnOnlyResponse;

        return $this;
    }

    public function setDeleteSuccessStatusCode(int $deleteSuccessStatusCode): self
    {
        $this->deleteSuccessStatusCode = $deleteSuccessStatusCode;

        return $this;
    }

    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    public function isThrow(): bool
    {
        return $this->throw;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function setClass(?string $class): self
    {
        $this->class = $class;

        return $this;
    }

    public function getThrowClass(): ?string
    {
        return $this->throwClass;
    }

    public function setThrowClass(?string $throwClass): self
    {
        $this->throwClass = $throwClass;
        if (null !== $throwClass) {
            $this->setThrow(false);
        }

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

    public function setFormat(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function setFormats(array $formats): self
    {
        self::$formats = $formats;

        return $this;
    }

    public function setConvertProperties(array $convertProperties): self
    {
        $this->convertProperties = $convertProperties;

        return $this;
    }

    public function setSendingConvertProperties(array $sendingConvertProperties): self
    {
        $this->sendingConvertProperties = $sendingConvertProperties;

        return $this;
    }

    public function setThrowConvertProperties(array $throwConvertProperties): self
    {
        $this->throwConvertProperties = $throwConvertProperties;

        return $this;
    }
}
