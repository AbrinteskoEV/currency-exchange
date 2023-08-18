<?php

declare(strict_types=1);

namespace Infrastructure\Service\Market\Binance;

use Application\Exceptions\BaseException;
use Domain\Dictionary\Market\Binance\BinanceRequestLabelDictionary;
use Infrastructure\Client\BinanceHttpClient;
use Infrastructure\Client\BinanceHttpClientFactory;
use Infrastructure\Service\Market\Binance\DTO\BinanceRequestDTO;

class BinanceExchangePairsPriceGettingService
{
    private const API_METHOD = 'GET';
    private const ENDPOINT = '/v3/ticker/price';

    private BinanceHttpClient $binanceClient;

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
     * @throws BaseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function getExchangePairsPrice(): array
    {
        $requestDTO = new BinanceRequestDTO(
            self::API_METHOD,
            self::ENDPOINT,
            BinanceRequestLabelDictionary::PAIRS_PRICE_GETTING_LABEL
        );

        return $this->binanceClient->sendRequest($requestDTO);
    }
}
