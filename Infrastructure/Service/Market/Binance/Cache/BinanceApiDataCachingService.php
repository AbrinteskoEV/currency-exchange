<?php

declare(strict_types=1);

namespace Infrastructure\Service\Market\Binance\Cache;

use Domain\Dictionary\Cache\CacheKeyNamespaceDictionary;
use Infrastructure\Service\Cache\TarantoolCacheRepository;

class BinanceApiDataCachingService
{
    private TarantoolCacheRepository $cacheRepository;
    private string $binanceApiNamespace;

    /**
     * @param TarantoolCacheRepository $cacheRepository
     */
    public function __construct(TarantoolCacheRepository $cacheRepository)
    {
        $this->cacheRepository = $cacheRepository;
        $this->binanceApiNamespace = $cacheRepository->formatComplexKey([
            CacheKeyNamespaceDictionary::BINANCE,
            CacheKeyNamespaceDictionary::API,
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
    public function setAvailableWeight(int $availableWeight): bool
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
    public function setUsedWeight(int $usedWeight): bool
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
     * @param string $requestCacheKey
     *
     * @return int|null
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function getLastRequestCallTimestamp(string $requestCacheKey): ?int
    {
        $currencyDescriptionList = $this->cacheRepository
            ->retrieve($this->getLastRequestCallComlexKey($requestCacheKey));

        return $currencyDescriptionList ?? null;
    }

    /**
     * @param string $requestCacheKey
     * @param int $timestamp
     *
     * @return bool
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function setLastRequestCallTimestamp(string $requestCacheKey, int $timestamp): bool
    {
        return (bool) $this->cacheRepository->store(
            $this->getLastRequestCallComlexKey($requestCacheKey),
            $timestamp
        );
    }

    /**
     * @param string $requestCacheKey
     *
     * @return bool
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function removeLastRequestCallTimestamp(string $requestCacheKey): bool
    {
        return $this->cacheRepository->remove($this->getLastRequestCallComlexKey($requestCacheKey));
    }

    /**
     * @return string
     */
    private function getAvailableWeightComplexKey(): string
    {
        return $this->cacheRepository->formatComplexKey([
            CacheKeyNamespaceDictionary::AVAILABLE_WEIGHT,
        ], $this->binanceApiNamespace);
    }

    /**
     * @return string
     */
    private function getUsedWeightComplexKey(): string
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
    private function getLastRequestCallComlexKey(string $requestCacheKey): string
    {
        return $this->cacheRepository->formatComplexKey([
            CacheKeyNamespaceDictionary::LAST_SEND,
            $requestCacheKey
        ], $this->binanceApiNamespace);
    }
}
