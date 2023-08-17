<?php

declare(strict_types=1);

namespace Infrastructure\Service\Market\Binance\Cache;

use Domain\Dictionary\Cache\CacheKeyNamespaceDictionary;
use Infrastructure\Service\Cache\TarantoolCacheRepository;

class BinanceCommonDataCachingService
{
    private TarantoolCacheRepository $cacheRepository;
    private string $binanceNamespace;

    /**
     * @param TarantoolCacheRepository $cacheRepository
     */
    public function __construct(TarantoolCacheRepository $cacheRepository)
    {
        $this->cacheRepository = $cacheRepository;
        $this->binanceNamespace = CacheKeyNamespaceDictionary::BINANCE;
    }

    /**
     * @return int|null
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function getAvailableWeight(): ?int
    {
        $currencyDescriptionList = $this->cacheRepository->retrieve($this->getAvailableWeightComplexKey());

        return $currencyDescriptionList ?? null;
    }

    /**
     * @param int $availableWeight
     *
     * @return bool
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function storeAvailableWeight(int $availableWeight): bool
    {
        return (bool) $this->cacheRepository->store(
            $this->getAvailableWeightComplexKey(),
            $availableWeight
        );
    }

    /**
     * @return bool
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function removeAvailableWeight(): bool
    {
        return $this->cacheRepository->remove($this->getAvailableWeightComplexKey());
    }

    /**
     * @return array|null
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function getUsedWeight(): int
    {
        $currencyDescriptionList = $this->cacheRepository->retrieve($this->getUsedWeightComplexKey());

        return $currencyDescriptionList ?? 0;
    }

    /**
     * @param int $usedWeight
     *
     * @return bool
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function storeUsedWeight(int $usedWeight): bool
    {
        return (bool) $this->cacheRepository->store(
            $this->getUsedWeightComplexKey(),
            $usedWeight
        );
    }

    /**
     * @return bool
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function removeUsedWeight(): bool
    {
        return $this->cacheRepository->remove($this->getUsedWeightComplexKey());
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
    private function getAvailableWeightComplexKey(): string
    {
        return $this->cacheRepository->formatComplexKey([
            CacheKeyNamespaceDictionary::API_WEIGHT,
            CacheKeyNamespaceDictionary::AVAILABLE_WEIGHT,
        ], $this->binanceNamespace);
    }

    /**
     * @return string
     */
    private function getUsedWeightComplexKey(): string
    {
        return $this->cacheRepository->formatComplexKey([
            CacheKeyNamespaceDictionary::API_WEIGHT,
            CacheKeyNamespaceDictionary::USED_WEIGHT,
        ], $this->binanceNamespace);
    }

    /**
     * @return string
     */
    private function getSymbolsInfoComplexKey(): string
    {
        return $this->cacheRepository->formatComplexKey([
            CacheKeyNamespaceDictionary::EXCHANGE_PAIRS,
            CacheKeyNamespaceDictionary::PAIRS_INFO,
        ], $this->binanceNamespace);
    }
}
