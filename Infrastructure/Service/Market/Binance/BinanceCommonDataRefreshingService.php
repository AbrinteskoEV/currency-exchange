<?php

declare(strict_types=1);

namespace Infrastructure\Service\Market\Binance;

use Infrastructure\Service\Market\Binance\Cache\BinanceApiDataCachingService;
use Infrastructure\Service\Market\Binance\Cache\BinanceCommonDataCachingService;

class BinanceCommonDataRefreshingService
{
    private BinanceCommonDataGettingService $binanceExchangeInfoGettingService;
    private BinanceCommonDataCachingService $binanceCommonDataCachingService;

    /**
     * @param BinanceCommonDataGettingService $binanceExchangeInfoGettingService
     * @param BinanceCommonDataCachingService $binanceCommonDataCachingService
     * @param BinanceApiDataCachingService $binanceApiDataCachingService
     */
    public function __construct(
        BinanceCommonDataGettingService $binanceExchangeInfoGettingService,
        BinanceCommonDataCachingService $binanceCommonDataCachingService,
        BinanceApiDataCachingService $binanceApiDataCachingService,
    ) {
        $this->binanceExchangeInfoGettingService = $binanceExchangeInfoGettingService;
        $this->binanceCommonDataCachingService = $binanceCommonDataCachingService;
        $this->binanceApiDataCachingService = $binanceApiDataCachingService;
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
        $this->binanceApiDataCachingService->setAvailableWeight($binanceCommonDataDTO->getMinuteApiWeightLimit());
        $this->binanceCommonDataCachingService->setSymbolsInfo($binanceCommonDataDTO->getSymbolInfoList());
    }
}
