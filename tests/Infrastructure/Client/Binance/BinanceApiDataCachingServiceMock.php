<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Client\Binance;

use Infrastructure\Service\Cache\TarantoolCacheRepository;
use Infrastructure\Service\Market\Binance\Cache\BinanceApiDataCachingService;

/**
 * @property string $binanceApiNamespace
 * @property TarantoolCacheRepository $cacheRepository
 */
class BinanceApiDataCachingServiceMock extends BinanceApiDataCachingService
{
    private const TEST_CACHE_KEY = 'TEST';
    /**
     * @param TarantoolCacheRepository $cacheRepository
     */
    public function __construct(TarantoolCacheRepository $cacheRepository)
    {
        parent::__construct($cacheRepository);
        $this->binanceApiNamespace = $this->cacheRepository->formatComplexKey(
            $this->binanceApiNamespace,
            self::TEST_CACHE_KEY
        );
    }
}
