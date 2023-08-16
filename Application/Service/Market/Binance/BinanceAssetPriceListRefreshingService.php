<?php

declare(strict_types=1);

namespace Application\Service\Market\Binance;

use Infrastructure\Service\Market\Binance\BinanceCommonDataRefreshingService;
use Infrastructure\Service\Market\Binance\BinanceExchangePairsInfoGettingService;
use Infrastructure\Service\Market\Binance\Cache\BinancePairsInfoCachingService;

class BinanceAssetPriceListRefreshingService
{
    private BinanceExchangePairsInfoGettingService $binanceExchangePairsInfoGettingService;
    private BinancePairsInfoCachingService $binancePairsInfoCachingService;
    private BinanceCommonDataRefreshingService $binanceCommonDataRefreshingService;

    /**
     * @param BinanceExchangePairsInfoGettingService $binanceExchangePairsInfoGettingService
     * @param BinancePairsInfoCachingService $binancePairsInfoCachingService
     * @param BinanceCommonDataRefreshingService $binanceCommonDataRefreshingService
     */
    public function __construct(
        BinanceExchangePairsInfoGettingService $binanceExchangePairsInfoGettingService,
        BinancePairsInfoCachingService $binancePairsInfoCachingService,
        BinanceCommonDataRefreshingService $binanceCommonDataRefreshingService
    ) {
        $this->binanceExchangePairsInfoGettingService = $binanceExchangePairsInfoGettingService;
        $this->binancePairsInfoCachingService = $binancePairsInfoCachingService;
        $this->binanceCommonDataRefreshingService = $binanceCommonDataRefreshingService;
    }

    /**
     * @return void
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function refreshAssetPriceList(): void
    {
        $binaceSymbolsInfoList = $this->binancePairsInfoCachingService->getSymbolsInfo();

        if ($binaceSymbolsInfoList === null) {
            $this->binanceCommonDataRefreshingService->refreshCommonData();
            $binaceSymbolsInfoList = $this->binancePairsInfoCachingService->getSymbolsInfo();
        }

        $binanceExchangePairsInfoList = $this->binanceExchangePairsInfoGettingService->getExchangePairsInfo();

        $formattedPairsPriceList = [];

        foreach ($binanceExchangePairsInfoList as $pairInfo) {
            $symbol = $pairInfo['symbol'];
            $price = $pairInfo['price'];
            $assetCodes = $binaceSymbolsInfoList[$symbol] ?? null;

            if (!$assetCodes) {
                continue;
            }

            $fromAsset = $assetCodes['fromAsset'];
            $toAsset = $assetCodes['toAsset'];

            $formattedPairsPriceList[$fromAsset][$toAsset] = $price;
            $formattedPairsPriceList[$toAsset][$fromAsset] = 1 / $price;
        }

        $this->binancePairsInfoCachingService->storePriceInfo($formattedPairsPriceList);
    }
}
