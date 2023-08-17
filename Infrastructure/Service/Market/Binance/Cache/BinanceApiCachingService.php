<?php

declare(strict_types=1);

namespace Infrastructure\Service\Market\Binance\Cache;


use Domain\Dictionary\Cache\CacheKeyNamespaceDictionary;
use Infrastructure\Service\Cache\TarantoolCacheRepository;

class BinanceApiCachingService
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
            CacheKeyNamespaceDictionary::DAILY,
            CacheKeyNamespaceDictionary::BINANCE,
            CacheKeyNamespaceDictionary::API_WEIGHT,
        ]);
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
     * @return string
     */
    private function getAvailableWeightComplexKey(): string
    {
        return $this->cacheRepository->formatComplexKey([
            CacheKeyNamespaceDictionary::AVAILABLE_WEIGHT
        ], $this->binanceApiNamespace);
    }

    /**
     * @return string
     */
    private function getUsedWeightComplexKey(): string
    {
        return $this->cacheRepository->formatComplexKey([
            CacheKeyNamespaceDictionary::USED_WEIGHT
        ], $this->binanceApiNamespace);
    }
}
