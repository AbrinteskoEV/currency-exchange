<?php

declare(strict_types=1);

namespace Application\Http\Controllers;

use Application\Exceptions\ApplicationException;
use Application\Http\Request\Market\AssetPriceGettingRequest;
use Application\Service\Market\Binance\BinanceAssetPriceListRefreshingService;
use Infrastructure\Service\Market\Binance\Cache\BinancePairsInfoCachingService;

class MarketController
{
    /**
     * @param AssetPriceGettingRequest $request
     * @param BinanceAssetPriceListRefreshingService $binanceAssetPriceListRefreshingService
     * @param BinancePairsInfoCachingService $binancePairsInfoCachingService
     *
     * @return array
     *
     * @throws ApplicationException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function getExchangePairsInfo(
        AssetPriceGettingRequest $request,
        BinanceAssetPriceListRefreshingService $binanceAssetPriceListRefreshingService,
        BinancePairsInfoCachingService $binancePairsInfoCachingService
    ): array {
        $priceInfo = $binancePairsInfoCachingService->getPriceInfo();

        $priceInfo = $priceInfo[$request->getFromAsset()] ?? null;
        $assetPrice = $priceInfo[$request->getToAsset()] ?? null;

        if (!$assetPrice) {
            throw new ApplicationException('Asset pair not found');
        }

        return ['price' => $assetPrice];
    }
}
