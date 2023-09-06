<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Client\Binance;

use Application\Exceptions\BaseException;
use Infrastructure\Service\Market\Binance\DTO\BinanceRequestDTO;
use PHPUnit\Framework\TestCase;

class BinanceApiProtectionTest extends TestCase
{
    private BinanceHttpClientMock $binanceHttpClientMock;
    private BinanceApiDataCachingServiceMock $binanceApiDataCachingServiceMock;

    private array $lightRequestSettings
        = BinanceHttpClientMock::TEST_REQUEST_SETTINGS[BinanceHttpClientMock::LIGHT_REQUEST_LABEL];
    private array $mediumRequestSettings
        = BinanceHttpClientMock::TEST_REQUEST_SETTINGS[BinanceHttpClientMock::MEDIUM_REQUEST_LABEL];

    private BinanceRequestDTO $lightRequestDTO;
    private BinanceRequestDTO $mediumRequestDTO;
    private BinanceRequestDTO $heavyRequestDTO;

    /**
     * @return void
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->binanceHttpClientMock = app()->make(BinanceHttpClientMock::class);
        $this->binanceApiDataCachingServiceMock = app()->make(BinanceApiDataCachingServiceMock::class);
        $this->binanceApiDataCachingServiceMock->cleanTestCache();

        $this->lightRequestDTO = new BinanceRequestDTO(
            'test',
            'test',
            BinanceHttpClientMock::LIGHT_REQUEST_LABEL
        );
        $this->mediumRequestDTO = new BinanceRequestDTO(
            'test',
            'test',
            BinanceHttpClientMock::MEDIUM_REQUEST_LABEL
        );
        $this->heavyRequestDTO = new BinanceRequestDTO(
            'test',
            'test',
            BinanceHttpClientMock::HEAVY_REQUEST_LABEL
        );
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

        $this->binanceApiDataCachingServiceMock->cleanTestCache();
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

        self::assertTrue($firstResponse && $secondResponse, 'Request sending test');
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
            ->getLastRequestCallTimestamp(BinanceHttpClientMock::LIGHT_REQUEST_LABEL);

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
