<?php

declare(strict_types=1);

namespace App\Tests\Behat\Utils;

use App\Tests\Behat\Utils\Model\WireMockDto;
use bupy7\xml\constructor\XmlConstructor;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use WireMock\Client\MappingBuilder;
use WireMock\Client\ResponseDefinitionBuilder;
use WireMock\Client\ValueMatchingStrategy;
use WireMock\Client\WireMock;

class WireMockHelper
{
    private WireMock $wireMock;

    public function __construct()
    {
        $this->wireMock = WireMock::create('wiremock');
    }

    public function mockOutputRequest(WireMockDto $wireMockDto): void
    {
        if (Request::METHOD_POST === $wireMockDto->getMethod()) {
            $this->mockOutputPostRequest($wireMockDto);
        } elseif (Request::METHOD_GET === $wireMockDto->getMethod()) {
            $this->mockOutputGetRequest($wireMockDto);
        } elseif (Request::METHOD_DELETE === $wireMockDto->getMethod()) {
            $this->mockOutputDeleteRequest($wireMockDto);
        } else {
            throw new RuntimeException(sprintf('Wiremock unsupported method "%s".', $wireMockDto->getMethod()));
        }
    }

    private function mockOutputPostRequest(WireMockDto $wireMockDto): void
    {
        $mock = WireMock::post(WireMock::urlEqualTo($wireMockDto->getUri()))
            ->willReturn($this->getResponseObj($wireMockDto));
        $this->addHeaders($mock, $wireMockDto);
        $this->addRequestBody($mock, $wireMockDto);

        $this->wireMock->stubFor($mock);
    }

    private function mockOutputGetRequest(WireMockDto $wireMockDto): void
    {
        $mock = WireMock::get(WireMock::urlEqualTo($wireMockDto->getUri()));
        $mock->willReturn($this->getResponseObj($wireMockDto));
        $this->addHeaders($mock, $wireMockDto);

        $this->wireMock->stubFor($mock);
    }

    private function mockOutputDeleteRequest(WireMockDto $wireMockDto): void
    {
        $mock = WireMock::delete(WireMock::urlEqualTo($wireMockDto->getUri()));
        $mock->willReturn($this->getResponseObj($wireMockDto));
        $this->addHeaders($mock, $wireMockDto);

        $this->wireMock->stubFor($mock);
    }

    private function addHeaders(MappingBuilder $mock, WireMockDto $wireMockDto): void
    {
        foreach ($wireMockDto->getRequestHeaders() as $header => $value) {
            $mock->withHeader($header, new ValueMatchingStrategy('equalTo', $value));
        }
        if (null !== $wireMockDto->getRequestContentType()) {
            $mock->withHeader('Content-Type', new ValueMatchingStrategy('equalTo', $wireMockDto->getRequestContentType()));
        }
    }

    private function addRequestBody(MappingBuilder $mock, WireMockDto $wireMockDto): void
    {
        if (null === $wireMockDto->getRequestBody()) {
            return;
        }

        $mock->withRequestBody(new ValueMatchingStrategy('equalTo',
            $this->serializeRequestBody($wireMockDto)
        ));
    }

    private function serializeRequestBody(WireMockDto $wireMockDto): string
    {
        if (null === $wireMockDto->getRequestBody()) {
            return '';
        }

        if ('application/x-www-form-urlencoded' === $wireMockDto->getRequestContentType()) {
            return http_build_query($wireMockDto->getRequestBody());
        }

        return (string) json_encode($wireMockDto->getRequestBody());
    }

    public function reset(): void
    {
        $this->wireMock->reset();
    }

    private function getResponseObj(WireMockDto $wireMockDto): ResponseDefinitionBuilder
    {
        $response = WireMock::aResponse()
            ->withStatus($wireMockDto->getResponseCode());

        if (null !== $wireMockDto->getResponseContentType()) {
            $response->withHeader('Content-Type', $wireMockDto->getResponseContentType());
        }

        if (is_array($wireMockDto->getResponseBody())) {
            if ('application/xml' === $wireMockDto->getResponseContentType()) {
                $xmlConstructor = new XmlConstructor(['startDocument' => false]);
                $response->withBody($xmlConstructor->fromArray($wireMockDto->getResponseBody())->toOutput());
            } else {
                $response->withBody((string) json_encode($wireMockDto->getResponseBody()));
            }
        } elseif (is_string($wireMockDto->getResponseBody())) {
            $response->withBody($wireMockDto->getResponseBody());
        }

        return $response;
    }
}
