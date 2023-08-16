<?php

declare(strict_types=1);

namespace Infrastructure\Service\Market\Binance;

use Infrastructure\Service\Market\Binance\Cache\BinanceApiCachingService;
use Infrastructure\Service\Market\Binance\Cache\BinancePairsInfoCachingService;

class BinanceCommonDataRefreshingService
{
    private BinanceCommonDataGettingService $binanceExchangeInfoGettingService;
    private BinanceApiCachingService $binanceApiCachingService;
    private BinancePairsInfoCachingService $binancePairsInfoCachingService;

    /**
     * @param BinanceCommonDataGettingService $binanceExchangeInfoGettingService
     * @param BinanceApiCachingService $binanceApiCachingService
     * @param BinancePairsInfoCachingService $binancePairsInfoCachingService
     */
    public function __construct(
        BinanceCommonDataGettingService $binanceExchangeInfoGettingService,
        BinanceApiCachingService $binanceApiCachingService,
        BinancePairsInfoCachingService $binancePairsInfoCachingService
    ) {
        $this->binanceExchangeInfoGettingService = $binanceExchangeInfoGettingService;
        $this->binanceApiCachingService = $binanceApiCachingService;
        $this->binancePairsInfoCachingService = $binancePairsInfoCachingService;
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
        $this->binancePairsInfoCachingService->storeSymbolsInfo($binanceCommonDataDTO->getSymbolInfoList());
    }
}
