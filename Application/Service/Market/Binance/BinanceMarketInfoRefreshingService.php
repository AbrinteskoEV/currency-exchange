<?php

declare(strict_types=1);

namespace Application\Service\Market\Binance;

use Application\Service\Market\Cache\BinancePairsPriceCachingServiceFactory;
use Application\Service\Market\Cache\PairsPriceCachingService;
use Application\Service\Market\MarketInfoRefreshingInterface;
use Infrastructure\Service\Market\Binance\BinanceCommonDataRefreshingService;
use Infrastructure\Service\Market\Binance\BinanceExchangePairsPriceGettingService;
use Infrastructure\Service\Market\Binance\Cache\BinanceCommonDataCachingService;

class BinanceMarketInfoRefreshingService implements MarketInfoRefreshingInterface
{
    private BinanceExchangePairsPriceGettingService $binanceExchangePairsInfoGettingService;
    private PairsPriceCachingService $binancePairsInfoCachingService;
    private BinanceCommonDataCachingService $binanceCommonDataCachingService;
    private BinanceCommonDataRefreshingService $binanceCommonDataRefreshingService;

    /**
     * @param BinanceExchangePairsPriceGettingService $binanceExchangePairsInfoGettingService
     * @param BinancePairsPriceCachingServiceFactory $binancePairsPriceCachingServiceFactory
     * @param BinanceCommonDataCachingService $binanceCommonDataCachingService
     * @param BinanceCommonDataRefreshingService $binanceCommonDataRefreshingService
     */
    public function __construct(
        BinanceExchangePairsPriceGettingService $binanceExchangePairsInfoGettingService,
        BinancePairsPriceCachingServiceFactory $binancePairsPriceCachingServiceFactory,
        BinanceCommonDataCachingService $binanceCommonDataCachingService,
        BinanceCommonDataRefreshingService $binanceCommonDataRefreshingService
    ) {
        $this->binanceExchangePairsInfoGettingService = $binanceExchangePairsInfoGettingService;
        $this->binancePairsInfoCachingService = $binancePairsPriceCachingServiceFactory->create();
        $this->binanceCommonDataCachingService = $binanceCommonDataCachingService;
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
        $binanceSymbolsInfoList = $this->binanceCommonDataCachingService->getSymbolsInfo();

        if ($binanceSymbolsInfoList === null) {
            $this->binanceCommonDataRefreshingService->refreshCommonData();
            $binanceSymbolsInfoList = $this->binanceCommonDataCachingService->getSymbolsInfo();
        }

        $binanceExchangePairsInfoList = $this->binanceExchangePairsInfoGettingService->getExchangePairsPrice();

        $formattedPairsPriceList = [];

        foreach ($binanceExchangePairsInfoList as $pairInfo) {
            $symbol = $pairInfo['symbol'];
            $price = $pairInfo['price'];
            $assetCodes = $binanceSymbolsInfoList[$symbol] ?? null;

            if (!$assetCodes) {
                continue;
            }

            $fromAsset = $assetCodes['fromAsset'];
            $toAsset = $assetCodes['toAsset'];

            $formattedPairsPriceList[$fromAsset][$toAsset] = $price;
            $formattedPairsPriceList[$toAsset][$fromAsset] = 1 / $price;
        }

        $this->binancePairsInfoCachingService->setPriceInfo($formattedPairsPriceList);
    }
}
