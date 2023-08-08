<?php

declare(strict_types=1);

namespace Infrastructure\Service\Market\Binance;

use Infrastructure\Client\BinanceClient;

class BinanceExchangePairsInfoGettingService
{
    private const API_METHOD = 'GET';
    private const ENDPOINT = '/v3/ticker/price';

    private BinanceClient $client;

    /**
     * @param BinanceClient $client
     */
    public function __construct(BinanceClient $client)
    {
        $this->client = $client;
    }

    /**
     * @return array
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function getExchangePairsInfo(): array
    {
        return $this->client->sendRequest(self::API_METHOD, self::ENDPOINT);
    }
}
