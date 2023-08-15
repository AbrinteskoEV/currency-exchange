<?php

declare(strict_types=1);

namespace Infrastructure\Client;

class TarantoolCachingClient
{
    private BaseHttpClient $tarantoolHttpClient;

    /**
     * @param TarantoolHttpClientFactory $tarantoolHttpClientFactory
     */
    public function __construct(TarantoolHttpClientFactory $tarantoolHttpClientFactory)
    {
        $this->tarantoolHttpClient = $tarantoolHttpClientFactory->create();
    }

    private const CACHE_GETTING_ENDPOINT = '/cache';
    private const CACHE_GETTING_API_METHOD = 'GET';
    private const CACHE_STORING_ENDPOINT = '/cache';
    private const CACHE_STORING_API_METHOD = 'POST';
    private const CACHE_FLUSHING_ENDPOINT = '/cache/flush';
    private const CACHE_FLUSHING_API_METHOD = 'POST';

    /**
     * @param string $cacheKey
     *
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function getCache(string $cacheKey): mixed
    {
        $response = $this->tarantoolHttpClient->sendRequest(
            self::CACHE_GETTING_API_METHOD,
            self::CACHE_GETTING_ENDPOINT,
            ['cache_key' => $cacheKey]
        );

        return $response['data'];
    }

    /**
     * @param string $cacheKey
     * @param mixed $data
     *
     * @return array
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function storeCache(string $cacheKey, mixed $data): array
    {
        $response = $this->tarantoolHttpClient->sendRequest(
            self::CACHE_STORING_API_METHOD,
            self::CACHE_STORING_ENDPOINT,
            ['cache_key' => $cacheKey, 'data' => $data]
        );

        return $response['data'];
    }

    /**
     * @param string $cacheKey
     *
     * @return bool
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function flushCache(string $cacheKey): bool
    {
        $response = $this->tarantoolHttpClient->sendRequest(
            self::CACHE_FLUSHING_API_METHOD,
            self::CACHE_FLUSHING_ENDPOINT,
            ['cache_key' => $cacheKey]
        );

        return $response['success'];
    }
}
