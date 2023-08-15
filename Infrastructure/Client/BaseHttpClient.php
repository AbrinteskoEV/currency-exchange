<?php

declare(strict_types=1);

namespace Infrastructure\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

class BaseHttpClient
{
    private Client $httpClient;
    private string $apiUrl;

    /**
     * @param Client $httpClient
     * @param string $apiUrl
     */
    public function __construct(
        Client $httpClient,
        string $apiUrl
    ) {
        $this->httpClient = $httpClient;
        $this->apiUrl = $apiUrl;
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $requestData
     *
     * @return array
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function sendRequest(string $method, string $endpoint, array $requestData = []): array
    {
        /** @var RequestInterface $preparedRequest */
        $preparedRequest = $this->prepareRequest($method, $endpoint, $requestData);

        $response = $this->httpClient->send($preparedRequest);
        $responseBody = $response->getBody();
        $responseArray = json_decode($responseBody->getContents(), true, 512, JSON_THROW_ON_ERROR);

        return $responseArray;
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $requestData
     *
     * @return MessageInterface
     *
     * @throws \JsonException
     */
    private function prepareRequest(string $method, string $endpoint, array $requestData): MessageInterface
    {
        $url = $this->apiUrl . $endpoint;

        if ($method === 'GET') {
            $query = http_build_query($requestData);
            $requestData = $url . '?' . $query;

            $httpRequest = new Request($method, $requestData);
        } else {
            $requestBodyStream = $this->createRequestStreamFromBody($requestData);
            $httpRequest = new Request($method, $url);
            $httpRequest = $httpRequest->withBody($requestBodyStream);
        }

        /** @var Request $httpRequest */
        return $httpRequest->withHeader('Content-Type', 'application/json');
    }

    /**
     * @param array $requestBody
     *
     * @return StreamInterface
     *
     * @throws \JsonException
     */
    private function createRequestStreamFromBody(array $requestBody): StreamInterface
    {
        $jsonBody = json_encode($requestBody, JSON_THROW_ON_ERROR);
        return Utils::streamFor($jsonBody);
    }
}
