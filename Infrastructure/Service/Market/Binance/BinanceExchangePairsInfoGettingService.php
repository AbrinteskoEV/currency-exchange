<?php

declare(strict_types=1);

namespace Infrastructure\Service\Market\Binance;

use Infrastructure\Client\BaseHttpClient;
use Infrastructure\Client\BinanceHttpClientFactory;

class BinanceExchangePairsInfoGettingService
{
    private const API_METHOD = 'GET';
    private const ENDPOINT = '/v3/ticker/price';
    private const API_WEIGHT = 2;

    private BaseHttpClient $binanceClient;

    /**
     * @param BinanceHttpClientFactory $binanceClientFactory
     */
    public function __construct(BinanceHttpClientFactory $binanceClientFactory)
    {
        $this->binanceClient = $binanceClientFactory->create();
    }

    /**
     * @return array
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function getExchangePairsInfo(): array
    {
        return $this->binanceClient->sendRequest(self::API_METHOD, self::ENDPOINT);
    }
}
