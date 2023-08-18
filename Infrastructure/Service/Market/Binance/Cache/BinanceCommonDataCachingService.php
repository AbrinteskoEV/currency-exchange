<?php

declare(strict_types=1);

namespace Infrastructure\Service\Market\Binance\Cache;

use Domain\Dictionary\Cache\CacheKeyNamespaceDictionary;
use Infrastructure\Service\Cache\TarantoolCacheRepository;

class BinanceCommonDataCachingService
{
    private TarantoolCacheRepository $cacheRepository;
    private string $binanceSymbolsInfoComplexKey;

    /**
     * @param TarantoolCacheRepository $cacheRepository
     */
    public function __construct(TarantoolCacheRepository $cacheRepository)
    {
        $this->cacheRepository = $cacheRepository;
        $this->binanceSymbolsInfoComplexKey = $cacheRepository->formatComplexKey([
            CacheKeyNamespaceDictionary::BINANCE,
            CacheKeyNamespaceDictionary::EXCHANGE_PAIRS,
            CacheKeyNamespaceDictionary::PAIRS_INFO,
        ]);
    }

    /**
     * @return array|null
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function getSymbolsInfo(): ?array
    {
        $currencyDescriptionList = $this->cacheRepository->retrieve($this->binanceSymbolsInfoComplexKey);

        return $currencyDescriptionList ?? null;
    }

    /**
     * @param array $symbolsInfo
     *
     * @return bool
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function setSymbolsInfo(array $symbolsInfo): bool
    {
        return (bool) $this->cacheRepository->store(
            $this->binanceSymbolsInfoComplexKey,
            $symbolsInfo
        );
    }

    /**
     * @return bool
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function removeSymbolsInfo(): bool
    {
        return $this->cacheRepository->remove($this->binanceSymbolsInfoComplexKey);
    }
}
