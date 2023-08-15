<?php

declare(strict_types=1);

namespace Infrastructure\Service\Market\Binance\Cache;


use Domain\Dictionary\Cache\CacheKeyNamespaceDictionary;
use Infrastructure\Service\Cache\TarantoolCacheRepository;

class BinancePairsInfoCachingService
{
    private TarantoolCacheRepository $cacheRepository;
    private string $binanceApiNamespace;

    /**
     * @param TarantoolCacheRepository $cacheRepository
     */
    public function __construct(TarantoolCacheRepository $cacheRepository)
    {
        $this->cacheRepository = $cacheRepository;
        $this->binanceApiNamespace = $this->cacheRepository->formatComplexKey([
            CacheKeyNamespaceDictionary::THIRTY_SECONDS,
            CacheKeyNamespaceDictionary::BINANCE,
            CacheKeyNamespaceDictionary::EXCHANGE_PAIRS,
        ]);
    }

    /**
     * @return array|null
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function getPriceInfo(): ?array
    {
        $currencyDescriptionList = $this->cacheRepository->retrieve($this->getPriceInfoComplexKey());

        return $currencyDescriptionList ?? null;
    }

    /**
     * @param array $priceInfo
     *
     * @return bool
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function storePriceInfo(array $priceInfo): bool
    {
        return (bool) $this->cacheRepository->store(
            $this->getPriceInfoComplexKey(),
            $priceInfo
        );
    }

    /**
     * @return bool
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function removePriceInfo(): bool
    {
        return $this->cacheRepository->remove($this->getPriceInfoComplexKey());
    }

    /**
     * @return array|null
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function getSymbolsInfo(): ?array
    {
        $currencyDescriptionList = $this->cacheRepository->retrieve($this->getSymbolsInfoComplexKey());

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
    public function storeSymbolsInfo(array $symbolsInfo): bool
    {
        return (bool) $this->cacheRepository->store(
            $this->getSymbolsInfoComplexKey(),
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
        return $this->cacheRepository->remove($this->getSymbolsInfoComplexKey());
    }

    /**
     * @return string
     */
    private function getPriceInfoComplexKey(): string
    {
        return $this->cacheRepository->formatComplexKey([
            CacheKeyNamespaceDictionary::PAIRS_PRICE
        ], $this->binanceApiNamespace);
    }

    /**
     * @return string
     */
    private function getSymbolsInfoComplexKey(): string
    {
        return $this->cacheRepository->formatComplexKey([
            CacheKeyNamespaceDictionary::PAIRS_INFO
        ], $this->binanceApiNamespace);
    }
}
