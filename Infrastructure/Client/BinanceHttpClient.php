<?php

declare(strict_types=1);

namespace Infrastructure\Client;

use Application\Exceptions\BaseException;
use Infrastructure\Service\Market\Binance\Cache\BinanceApiDataCachingService;
use Infrastructure\Service\Market\Binance\DTO\BinanceRequestDTO;

class BinanceHttpClient
{
    private const LOCKED_AVAILABLE_WEIGHT_PERCENT = 10;
    private const DEFAULT_AVAILABLE_WEIGHT = 100;

    private BaseHttpClient $httpClient;
    private BinanceApiDataCachingService $binanceApiDataCachingService;
    private array $requestSettings;

    /**
     * @param BaseHttpClient $httpClient
     * @param BinanceApiDataCachingService $binanceApiDataCachingService
     * @param array $requestSettings
     */
    public function __construct(
        BaseHttpClient $httpClient,
        BinanceApiDataCachingService $binanceApiDataCachingService,
        array $requestSettings
    ) {
        $this->httpClient = $httpClient;
        $this->binanceApiDataCachingService = $binanceApiDataCachingService;
        $this->requestSettings = $requestSettings;
    }

    /**
     * @param BinanceRequestDTO $requestDTO
     *
     * @return array
     *
     * @throws BaseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function sendRequest(BinanceRequestDTO $requestDTO): array
    {
        $requestLabel =$requestDTO->getLabel();
        $this->handleBeforeSending($requestLabel);

        $response =  $this->httpClient->sendRequest(
            $requestDTO->getApiMethod(),
            $requestDTO->getEndpoint(),
            $requestDTO->getData());

        $this->handleAfterSending($requestLabel);

        return $response;
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
