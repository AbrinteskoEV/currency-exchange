<?php

declare(strict_types=1);

namespace Application\Http\Controllers;

use Infrastructure\Service\Market\Binance\BinanceExchangePairsInfoGettingService;

class MarketController
{
    /**
     * @param BinanceExchangePairsInfoGettingService $binanceExchangePairsInfoGettingService
     *
     * @return array
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function getExchangePairsInfo(
        BinanceExchangePairsInfoGettingService $binanceExchangePairsInfoGettingService
    ): array {
        return $binanceExchangePairsInfoGettingService->getExchangePairsInfo();
    }
}
