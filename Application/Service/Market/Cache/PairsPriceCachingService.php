<?php

declare(strict_types=1);

namespace Application\Service\Market\Cache;

use Domain\Dictionary\Cache\CacheKeyNamespaceDictionary;
use Infrastructure\Service\Cache\TarantoolCacheRepository;

class PairsPriceCachingService
{
    private TarantoolCacheRepository $cacheRepository;
    private string $marketKeyNamespace;

    /**
     * @param TarantoolCacheRepository $cacheRepository
     * @param string $marketKeyNamespace
     */
    public function __construct(
        TarantoolCacheRepository $cacheRepository,
        string $marketKeyNamespace
    ) {
        $this->cacheRepository = $cacheRepository;
        $this->marketKeyNamespace = $marketKeyNamespace;
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
    public function setPriceInfo(array $priceInfo): bool
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
     * @return string
     */
    private function getPriceInfoComplexKey(): string
    {
        return $this->cacheRepository->formatComplexKey([
            CacheKeyNamespaceDictionary::EXCHANGE_PAIRS,
            CacheKeyNamespaceDictionary::PAIRS_PRICE,
        ], $this->marketKeyNamespace);
    }
}
