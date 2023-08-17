<?php

declare(strict_types=1);

namespace Infrastructure\Service\Market\Binance;

use Infrastructure\Service\Market\Binance\Cache\BinanceCommonDataCachingService;

class BinanceCommonDataRefreshingService
{
    private BinanceCommonDataGettingService $binanceExchangeInfoGettingService;
    private BinanceCommonDataCachingService $binanceApiCachingService;

    /**
     * @param BinanceCommonDataGettingService $binanceExchangeInfoGettingService
     * @param BinanceCommonDataCachingService $binanceApiCachingService
     */
    public function __construct(
        BinanceCommonDataGettingService $binanceExchangeInfoGettingService,
        BinanceCommonDataCachingService $binanceApiCachingService,
    ) {
        $this->binanceExchangeInfoGettingService = $binanceExchangeInfoGettingService;
        $this->binanceApiCachingService = $binanceApiCachingService;
    }

    /**
     * @return void
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function refreshCommonData(): void
    {
        $binanceCommonDataDTO = $this->binanceExchangeInfoGettingService->getCommonData();
        $this->binanceApiCachingService->storeAvailableWeight($binanceCommonDataDTO->getMinuteApiWeightLimit());
        $this->binanceApiCachingService->storeSymbolsInfo($binanceCommonDataDTO->getSymbolInfoList());
    }
}
