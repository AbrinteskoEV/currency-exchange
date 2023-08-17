<?php

declare(strict_types=1);

namespace Application\Service\Market\Binance;

use Application\Service\Market\Cache\BinancePairsPriceCachingServiceFactory;
use Application\Service\Market\Cache\PairsPriceCachingService;
use Application\Service\Market\PairsPriceGettingInterface;

class BinancePairsPriceGettingService implements PairsPriceGettingInterface
{
    private PairsPriceCachingService $binancePairsPriceCachingService;
    private BinanceMarketInfoRefreshingService $binanceMarketInfoRefreshingService;

    /**
     * @param BinancePairsPriceCachingServiceFactory $binancePairsPriceCachingServiceFactory
     * @param BinanceMarketInfoRefreshingService $binanceMarketInfoRefreshingService
     */
    public function __construct(
        BinancePairsPriceCachingServiceFactory $binancePairsPriceCachingServiceFactory,
        BinanceMarketInfoRefreshingService $binanceMarketInfoRefreshingService
    ) {
        $this->binancePairsPriceCachingService = $binancePairsPriceCachingServiceFactory->create();
        $this->binanceMarketInfoRefreshingService = $binanceMarketInfoRefreshingService;
    }

    /**
     * @return array
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function getPairsPriceList(): array
    {
        $pairsPriceList = $this->binancePairsPriceCachingService->getPriceInfo();

        if (!$pairsPriceList) {
            $this->binanceMarketInfoRefreshingService->refreshAssetPriceList();
            $pairsPriceList = $this->binancePairsPriceCachingService->getPriceInfo();
        }

        return $pairsPriceList;
    }
}
