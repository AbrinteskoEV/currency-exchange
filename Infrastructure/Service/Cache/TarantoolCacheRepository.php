<?php

declare(strict_types=1);

namespace Infrastructure\Service\Cache;

use Infrastructure\Client\BaseHttpClient;
use Infrastructure\Client\TarantoolHttpClientFactory;

class TarantoolCacheRepository
{
    private const COMPLEX_KEY_DELIMITER = ':';

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
    private const CACHE_FLUSHING_ENDPOINT = '/cache/delete';
    private const CACHE_FLUSHING_API_METHOD = 'POST';

    /**
     * @param string $cacheKey
     *
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function retrieve(string $cacheKey): mixed
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
    public function store(string $cacheKey, mixed $data): array
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
    public function remove(string $cacheKey): bool
    {
        $response = $this->tarantoolHttpClient->sendRequest(
            self::CACHE_FLUSHING_API_METHOD,
            self::CACHE_FLUSHING_ENDPOINT,
            ['cache_key' => $cacheKey]
        );

        return $response['success'];
    }

    /**
     * @param array<string> $keyPartList
     * @param string|null $complexKeyPart
     *
     * @return string
     */
    public function formatComplexKey(array $keyPartList, ?string $complexKeyPart = null): string
    {
        foreach ($keyPartList as $part) {
            $complexKeyPart .= self::COMPLEX_KEY_DELIMITER . $part;
        }

        return ltrim($complexKeyPart, self::COMPLEX_KEY_DELIMITER);
    }
}
