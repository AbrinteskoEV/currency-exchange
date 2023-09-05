<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Client\Binance;

use Application\Exceptions\BaseException;
use Infrastructure\Service\Market\Binance\Cache\BinanceApiDataCachingService;

class BinanceHttpClientMock
{
    private const LOCKED_AVAILABLE_WEIGHT_PERCENT = 10;
    private const DEFAULT_AVAILABLE_WEIGHT = 1200;

    public const LIGHT_REQUEST_LABEL = 'light_test_request';
    public const MEDIUM_REQUEST_LABEL = 'medium_test_request';
    public const HEAVY_REQUEST_LABEL = 'heavy_test_request';

    private array $requestSettings = [
        self::LIGHT_REQUEST_LABEL => [
            'weight' => 10,
            'minInterval' => 5,
            'cacheKey' => 'light_test_request',
        ],
        self::MEDIUM_REQUEST_LABEL => [
            'weight' => 2,
            'minInterval' => 1,
            'cacheKey' => 'medium_test_request',
        ],
        self::HEAVY_REQUEST_LABEL => [
            'weight' => 99,
            'minInterval' => 1,
            'cacheKey' => 'heavy_test_request',
        ],
    ];

    private BinanceApiDataCachingServiceMock $binanceApiDataCachingService;

    /**
     * @param BinanceApiDataCachingServiceMock $binanceApiDataCachingService
     */
    public function __construct(BinanceApiDataCachingServiceMock $binanceApiDataCachingService) {
        $this->binanceApiDataCachingService = $binanceApiDataCachingService;
    }

    /**
     * @param string $requestLabel
     *
     * @return void
     *
     * @throws BaseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function sendRequest(string $requestLabel): void
    {
        $this->handleBeforeSending($requestLabel);
        $this->handleAfterSending($requestLabel);
    }

    /**
     * @param string $requestLabel
     *
     * @return void
     *
     * @throws BaseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    private function handleBeforeSending(string $requestLabel): void
    {
        $requestSettings = $this->requestSettings[$requestLabel] ?? null;

        if (!$requestSettings) {
            throw new BaseException("Not implemented binance request with label [$requestLabel]");
        }

        try {
            $requestWeight = $requestSettings['weight'];
            $this->handleRequestWeight($requestWeight);

            $requestMinInterval = $requestSettings['minInterval'];
            $requestCacheKey = $requestSettings['cacheKey'];
            $this->handleRequestSendingInterval($requestCacheKey, $requestMinInterval);
        } catch (BaseException $exception) {
            throw new BaseException(
                "Binance request was canceled. Reason: " . $exception->getMessage(),
                [
                    'requestLabel' => $requestLabel,
                    'context' => $exception->getContext()
                ]
            );
        }
    }

    /**
     * @param int $requestWeight
     *
     * @return void
     *
     * @throws BaseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    private function handleRequestWeight(int $requestWeight): void
    {
        $usedWeight = $this->binanceApiDataCachingService->getUsedWeight();
        $availableWeight = $this->binanceApiDataCachingService->getAvailableWeight();

        if (!$availableWeight) {
            $availableWeight = self::DEFAULT_AVAILABLE_WEIGHT;
        }
        $lockedWeight = $availableWeight * self::LOCKED_AVAILABLE_WEIGHT_PERCENT / 100;

        $availableWeightAfterLock = $availableWeight - $lockedWeight;
        $usedWeightAfterRequest = $usedWeight + $requestWeight;

        if ($usedWeightAfterRequest > $availableWeightAfterLock) {
            throw new BaseException("Not enough available api weight", [
                'usedWeight' => $usedWeight,
                'availableWeight' => $availableWeightAfterLock,
            ]);
        }
    }

    /**
     * @param string $requestCacheKey
     * @param int $requestMinInterval
     *
     * @return void
     *
     * @throws BaseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    private function handleRequestSendingInterval(string $requestCacheKey, int $requestMinInterval): void
    {
        $lastSendingTimestamp = $this->binanceApiDataCachingService->getLastRequestCallTimestamp($requestCacheKey);

        if ($lastSendingTimestamp === null) {
            return;
        }

        $currentTimestamp = time();

        $timeFromTheLastSending = $currentTimestamp - $lastSendingTimestamp;

        if ($timeFromTheLastSending < $requestMinInterval) {
            throw new BaseException("Too frequently request sending", [
                'timeFromTheLastSending' => $timeFromTheLastSending,
                'minInterval' => $requestMinInterval,
            ]);
        }
    }

    /**
     * @param string $requestLabel
     *
     * @return void
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    private function handleAfterSending(string $requestLabel): void
    {
        $requestSettings = $this->requestSettings[$requestLabel];
        $requestWeight = $requestSettings['weight'];

        $usedWeight = $this->binanceApiDataCachingService->getUsedWeight();
        $usedWeight += $requestWeight;
        $this->binanceApiDataCachingService->setUsedWeight($usedWeight);

        $requestCacheKey = $requestSettings['cacheKey'];
        $currentTimeStamp = time();
        $this->binanceApiDataCachingService->setLastRequestCallTimestamp($requestCacheKey, $currentTimeStamp);
    }
}
