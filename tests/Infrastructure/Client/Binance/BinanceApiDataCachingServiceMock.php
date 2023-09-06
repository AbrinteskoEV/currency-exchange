<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Client\Binance;

use Domain\Dictionary\Cache\CacheKeyNamespaceDictionary;
use Infrastructure\Service\Cache\TarantoolCacheRepository;
use Infrastructure\Service\Market\Binance\Cache\BinanceApiDataCachingService;

/**
 * @property string $binanceApiNamespace
 * @property TarantoolCacheRepository $cacheRepository
 */
class BinanceApiDataCachingServiceMock extends BinanceApiDataCachingService
{
    public const TEST_CACHE_KEY = 'TEST_BINANCE_API';

    private string $binanceApiNamespace;
    private TarantoolCacheRepository $cacheRepository;

    /**
     * @param TarantoolCacheRepository $cacheRepository
     */
    public function __construct(TarantoolCacheRepository $cacheRepository)
    {
        parent::__construct($cacheRepository);
        $this->binanceApiNamespace = self::TEST_CACHE_KEY;
        $this->cacheRepository = $cacheRepository;
    }

    /**
     * @return void
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function cleanTestCache(): void
    {
        $this->removeUsedWeight();

        foreach (BinanceHttpClientMock::TEST_REQUEST_LIST as $requestLabel) {
            $this->removeLastRequestCallTimestamp($requestLabel);
        }
    }

    /**
     * @return string
     */
    protected function getAvailableWeightComplexKey(): string
    {
        return $this->cacheRepository->formatComplexKey([
            CacheKeyNamespaceDictionary::AVAILABLE_WEIGHT,
        ], $this->binanceApiNamespace);
    }

    /**
     * @return string
     */
    protected function getUsedWeightComplexKey(): string
    {
        return $this->cacheRepository->formatComplexKey([
            CacheKeyNamespaceDictionary::USED_WEIGHT,
        ], $this->binanceApiNamespace);
    }

    /**
     * @param string $requestCacheKey
     *
     * @return string
     */
    protected function getLastRequestCallComlexKey(string $requestCacheKey): string
    {
        return $this->cacheRepository->formatComplexKey([
            CacheKeyNamespaceDictionary::LAST_SEND,
            $requestCacheKey
        ], $this->binanceApiNamespace);
    }
}
