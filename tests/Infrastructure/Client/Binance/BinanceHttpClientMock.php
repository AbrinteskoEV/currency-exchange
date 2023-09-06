<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Client\Binance;

use Infrastructure\Client\BinanceHttpClient;
use Infrastructure\Service\Market\Binance\Cache\BinanceApiDataCachingService;
use Tests\Infrastructure\Client\BaseHttpClientMock;

class BinanceHttpClientMock extends BinanceHttpClient
{
    public const LIGHT_REQUEST_LABEL = 'light_test_request';
    public const MEDIUM_REQUEST_LABEL = 'medium_test_request';
    public const HEAVY_REQUEST_LABEL = 'heavy_test_request';

    public const TEST_REQUEST_LIST = [
        self::LIGHT_REQUEST_LABEL,
        self::MEDIUM_REQUEST_LABEL,
        self::HEAVY_REQUEST_LABEL,
    ];

    public const TEST_REQUEST_SETTINGS = [
        self::LIGHT_REQUEST_LABEL => [
            'weight' => 10,
            'minInterval' => 0,
            'cacheKey' => 'light_test_request',
        ],
        self::MEDIUM_REQUEST_LABEL => [
            'weight' => 50,
            'minInterval' => 10,
            'cacheKey' => 'medium_test_request',
        ],
        self::HEAVY_REQUEST_LABEL => [
            'weight' => parent::DEFAULT_AVAILABLE_WEIGHT - 1,
            'minInterval' => 30,
            'cacheKey' => 'heavy_test_request',
        ],
    ];

    private array $requestSettings;

    private BaseHttpClientMock $httpClient;
    private BinanceApiDataCachingService $binanceApiDataCachingService;

    /**
     * @param BaseHttpClientMock $httpClient
     * @param BinanceApiDataCachingServiceMock $binanceApiDataCachingService
     */
    public function __construct(
        BaseHttpClientMock $httpClient,
        BinanceApiDataCachingServiceMock $binanceApiDataCachingService
    ) {
        parent::__construct($httpClient, $binanceApiDataCachingService, self::TEST_REQUEST_SETTINGS);
    }
}
