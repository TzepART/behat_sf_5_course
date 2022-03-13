<?php

declare(strict_types=1);

namespace App\Tests\Behat\Utils\Model;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WireMockDto
{
    private const URI_KEY = 'uri';
    private const METHOD_KEY = 'method';
    private const REQUEST_BODY_KEY = 'request_body';
    private const REQUEST_CONTENT_TYPE = 'request_content_type';
    private const RESPONSE_BODY_KEY = 'response_body';
    private const RESPONSE_CODE_KEY = 'response_code';
    private const REQUEST_HEADERS_KEY = 'request_headers';
    private const RESPONSE_CONTENT_TYPE = 'response_content_type';

    private string $uri;
    private string $method;
    private int $responseCode;
    /**
     * @var array<mixed>
     */
    private ?array $requestBody = null;

    private ?string $requestContentType = null;
    /**
     * @var array<mixed>
     */
    private array $requestHeaders = [];
    /**
     * @var string|mixed[]|null
     */
    private $responseBody;

    private ?string $responseContentType = null;

    public function __construct(string $uri, string $method = Request::METHOD_POST, int $responseCode = Response::HTTP_OK)
    {
        $this->uri = $uri;
        $this->method = $method;
        $this->responseCode = $responseCode;
    }

    /**
     * @return mixed[]|null
     */
    public function getRequestBody(): ?array
    {
        return $this->requestBody;
    }

    /**
     * @param mixed[]|null $requestBody
     */
    public function setRequestBody(?array $requestBody): self
    {
        $this->requestBody = $requestBody;

        return $this;
    }

    public function getRequestContentType(): ?string
    {
        return $this->requestContentType;
    }

    public function setRequestContentType(?string $requestContentType): self
    {
        $this->requestContentType = $requestContentType;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getRequestHeaders(): array
    {
        return $this->requestHeaders;
    }

    /**
     * @param mixed[] $requestHeaders
     */
    public function setRequestHeaders(array $requestHeaders): self
    {
        $this->requestHeaders = $requestHeaders;

        return $this;
    }

    /**
     * @return string|mixed[]|null
     */
    public function getResponseBody()
    {
        return $this->responseBody;
    }

    /**
     * @param string|mixed[]|null $responseBody
     */
    public function setResponseBody($responseBody): self
    {
        $this->responseBody = $responseBody;

        return $this;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function setResponseCode(int $responseCode): self
    {
        $this->responseCode = $responseCode;

        return $this;
    }

    public function getResponseCode(): int
    {
        return $this->responseCode;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getResponseContentType(): ?string
    {
        return $this->responseContentType;
    }

    public function setResponseContentType(?string $responseContentType): self
    {
        $this->responseContentType = $responseContentType;

        return $this;
    }

    /**
     * @param mixed[] $wireMockData
     */
    public static function make(array $wireMockData): WireMockDto
    {
        $wireMockDto = new self($wireMockData[self::URI_KEY]);

        if (isset($wireMockData[self::METHOD_KEY])) {
            $wireMockDto->setMethod($wireMockData[self::METHOD_KEY]);
        }

        if (isset($wireMockData[self::REQUEST_BODY_KEY])) {
            $wireMockDto->setRequestBody($wireMockData[self::REQUEST_BODY_KEY]);
        }

        if (isset($wireMockData[self::REQUEST_CONTENT_TYPE])) {
            $wireMockDto->setRequestContentType($wireMockData[self::REQUEST_CONTENT_TYPE]);
        }

        if (isset($wireMockData[self::RESPONSE_BODY_KEY])) {
            $wireMockDto->setResponseBody($wireMockData[self::RESPONSE_BODY_KEY]);
        }

        if (isset($wireMockData[self::RESPONSE_CODE_KEY])) {
            $wireMockDto->setResponseCode($wireMockData[self::RESPONSE_CODE_KEY]);
        }

        if (isset($wireMockData[self::REQUEST_HEADERS_KEY])) {
            $wireMockDto->setRequestHeaders($wireMockData[self::REQUEST_HEADERS_KEY]);
        }

        if (isset($wireMockData[self::RESPONSE_CONTENT_TYPE])) {
            $wireMockDto->setResponseContentType($wireMockData[self::RESPONSE_CONTENT_TYPE]);
        }

        return $wireMockDto;
    }
}
