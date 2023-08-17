<?php

declare(strict_types=1);

namespace Application\Service\Market\Cache;

use Domain\Dictionary\Cache\CacheKeyNamespaceDictionary;
use Infrastructure\Service\Cache\TarantoolCacheRepository;

class BinancePairsPriceCachingServiceFactory
{
    private TarantoolCacheRepository $cachingRepository;

    /**
     * @param TarantoolCacheRepository $cachingRepository
     */
    public function __construct(TarantoolCacheRepository $cachingRepository)
    {
        $this->cachingRepository = $cachingRepository;
    }

    /**
     * @return PairsPriceCachingService
     */
    public function create(): PairsPriceCachingService
    {
        return new PairsPriceCachingService($this->cachingRepository, CacheKeyNamespaceDictionary::BINANCE);
    }
}
