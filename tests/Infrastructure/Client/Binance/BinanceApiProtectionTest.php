<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Client\Binance;

use Application\Exceptions\BaseException;
use Infrastructure\Client\BaseHttpClient;
use Infrastructure\Client\BinanceHttpClient;
use Infrastructure\Service\Cache\TarantoolCacheRepository;
use Infrastructure\Service\Market\Binance\Cache\BinanceApiDataCachingService;
use Infrastructure\Service\Market\Binance\DTO\BinanceRequestDTO;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class BinanceApiProtectionTest extends TestCase
{
    private const TEST_CACHE_KEY = 'BINANCE:API:TEST';
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
            'weight' => 999,
            'minInterval' => 30,
            'cacheKey' => 'heavy_test_request',
        ],
    ];

    private array $lightRequestSettings
        = self::TEST_REQUEST_SETTINGS[self::LIGHT_REQUEST_LABEL];
    private array $mediumRequestSettings
        = self::TEST_REQUEST_SETTINGS[self::MEDIUM_REQUEST_LABEL];

    private BinanceRequestDTO $lightRequestDTO;
    private BinanceRequestDTO $mediumRequestDTO;
    private BinanceRequestDTO $heavyRequestDTO;

    private BinanceHttpClient $binanceHttpClientMock;
    private BinanceApiDataCachingService $binanceApiDataCachingServiceMock;

    /**
     * @return void
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $binanceApiDataCachingServiceMock = $this->getMockBuilder(BinanceApiDataCachingService::class)
            ->onlyMethods([])
            ->setConstructorArgs([app()->make(TarantoolCacheRepository::class)])
            ->getMock();
        $reflection = new ReflectionClass(BinanceApiDataCachingService::class);
        $reflectionProperty = $reflection->getProperty('binanceApiNamespace');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($binanceApiDataCachingServiceMock, self::TEST_CACHE_KEY);

        $baseHttpClientDummy = $this->createStub(BaseHttpClient::class);
        $binanceHttpClientMock = $this->getMockBuilder(BinanceHttpClient::class)
            ->onlyMethods([])
            ->setConstructorArgs([
                $baseHttpClientDummy,
                $binanceApiDataCachingServiceMock,
                self::TEST_REQUEST_SETTINGS
            ])
            ->getMock();

        $this->binanceHttpClientMock = $binanceHttpClientMock;
        $this->binanceApiDataCachingServiceMock = $binanceApiDataCachingServiceMock;

        $this->lightRequestDTO = new BinanceRequestDTO(
            'test',
            'test',
            self::LIGHT_REQUEST_LABEL
        );
        $this->mediumRequestDTO = new BinanceRequestDTO(
            'test',
            'test',
            self::MEDIUM_REQUEST_LABEL
        );
        $this->heavyRequestDTO = new BinanceRequestDTO(
            'test',
            'test',
            self::HEAVY_REQUEST_LABEL
        );

        $this->cleanTestCache();
    }

    /**
     * @return void
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->cleanTestCache();
    }

    /**
     * @return void
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    private function cleanTestCache(): void
    {
        $this->binanceApiDataCachingServiceMock->removeUsedWeight();
        $this->binanceApiDataCachingServiceMock->removeAvailableWeight();

        foreach (self::TEST_REQUEST_LIST as $requestLabel) {
            $this->binanceApiDataCachingServiceMock->removeLastRequestCallTimestamp($requestLabel);
        }
    }

    /**
     * @return void
     *
     * @throws \Application\Exceptions\BaseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function testPositiveRequestSending(): void
    {
        $firstResponse = $this->binanceHttpClientMock->sendRequest($this->lightRequestDTO);
        $secondResponse = $this->binanceHttpClientMock->sendRequest($this->mediumRequestDTO);

        self::assertTrue($firstResponse === [] && $secondResponse === [], 'Request sending test');
    }

    /**
     * @return void
     *
     * @throws \Application\Exceptions\BaseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function testApiWeightCaching(): void
    {
        $this->binanceHttpClientMock->sendRequest($this->lightRequestDTO);
        $this->binanceHttpClientMock->sendRequest($this->mediumRequestDTO);
        $totalUsedWeight = $this->binanceApiDataCachingServiceMock->getUsedWeight();

        $lightRequestWeight = $this->lightRequestSettings['weight'];
        $mediumRequestWeight = $this->mediumRequestSettings['weight'];
        $expectedTotalUsedWeight = $lightRequestWeight + $mediumRequestWeight;

        self::assertEquals($expectedTotalUsedWeight, $totalUsedWeight, 'Api weight caching test');
    }

    /**
     * @return void
     *
     * @throws \Application\Exceptions\BaseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function testRequestLastUsedCaching(): void
    {
        $this->binanceHttpClientMock->sendRequest($this->lightRequestDTO);
        $lastUsed = $this->binanceApiDataCachingServiceMock
            ->getLastRequestCallTimestamp(self::LIGHT_REQUEST_LABEL);

        self::assertTrue((bool) $lastUsed, 'Request last used timestamp caching test');
    }

    /**
     * @return void
     *
     * @throws BaseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function testRejectByWeight(): void
    {
        $this->expectException(BaseException::class);
        $this->binanceHttpClientMock->sendRequest($this->heavyRequestDTO);
    }

    /**
     * @return void
     *
     * @throws BaseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function testRejectByLastUsed(): void
    {
        $this->binanceHttpClientMock->sendRequest($this->mediumRequestDTO);
        $this->expectException(BaseException::class);
        $this->binanceHttpClientMock->sendRequest($this->mediumRequestDTO);
    }
}
