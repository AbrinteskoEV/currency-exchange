<?php

declare(strict_types=1);

namespace Infrastructure\Service\Market\Binance;

use http\Exception\RuntimeException;
use Infrastructure\Client\BaseHttpClient;
use Infrastructure\Client\BinanceHttpClientFactory;
use Infrastructure\Service\Market\Binance\DTO\BinanceCommonDataDTO;

class BinanceCommonDataGettingService
{
    private const API_METHOD = 'GET';
    private const ENDPOINT = '/v3/exchangeInfo';
    private const API_WEIGHT = 10;

    private const REQUEST_WEIGHT_LIMIT_TYPE = 'REQUEST_WEIGHT';
    private const MINUTE_INTERVAL = 'MINUTE';


    private BaseHttpClient $binanceClient;

    /**
     * @param BinanceHttpClientFactory $binanceClientFactory
     */
    public function __construct(BinanceHttpClientFactory $binanceClientFactory)
    {
        $this->binanceClient = $binanceClientFactory->create();
    }

    /**
     * @return BinanceCommonDataDTO
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function getCommonData(): BinanceCommonDataDTO
    {
        $response = $this->binanceClient->sendRequest(self::API_METHOD, self::ENDPOINT);
        $rateLimitInfoList = $response['rateLimits'];

        foreach ($rateLimitInfoList as $rateLimitInfo) {
            $rateLimitType = $rateLimitInfo['rateLimitType'];
            $rateLimitInterval = $rateLimitInfo['interval'];
            if ($rateLimitType === self::REQUEST_WEIGHT_LIMIT_TYPE && $rateLimitInterval === self::MINUTE_INTERVAL) {
                $requestWeightLimit = $rateLimitInfo['limit'];
                break;
            }
        }

        if (!$requestWeightLimit) {
            throw new RuntimeException('Binance request rate limits is not exists');
        }

        $symbolInfoList = $response['symbols'];
        $formattedSymbolInfoList = [];

        foreach ($symbolInfoList as $symbolInfo) {
            $symbol = $symbolInfo['symbol'];
            $firstAsset = $symbolInfo['baseAsset'];
            $secondAsset = $symbolInfo['quoteAsset'];

            $formattedSymbolInfoList[$symbol] = ['fromAsset' => $firstAsset, 'toAsset' => $secondAsset];
        }

        return new BinanceCommonDataDTO($requestWeightLimit, $formattedSymbolInfoList);
    }
}
