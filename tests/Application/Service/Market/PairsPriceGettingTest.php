<?php

declare(strict_types=1);

namespace Tests\Application\Service\Market;

use Application\Exceptions\BaseException;
use Application\Service\Market\PairsPriceGettingService;
use Monolog\Test\TestCase;

class PairsPriceGettingTest extends TestCase
{
    public const BTC = 'BTC';
    public const DOGE = 'DOGE';
    public const RUB = 'RUB';

    public const LOW_PRICE_MARKET_NAME = 'low_price_market';
    public const MEDIUM_PRICE_MARKET_NAME = 'medium_price_market';
    public const HIGH_PRICE_MARKET_NAME = 'high_price_market';

    private const ALL_PAIRS_RESPONSE_MOCK = [
        self::LOW_PRICE_MARKET_NAME => [
            self::BTC => [
                self::RUB => 100,
            ],
        ],
        self::MEDIUM_PRICE_MARKET_NAME => [
            self::BTC => [
                self::RUB => 200,
                self::DOGE => 30
            ],
        ],
        self::HIGH_PRICE_MARKET_NAME => [
            self::BTC => [
                self::RUB => 300,
            ],
        ],
    ];

    private PairsPriceGettingService $pairsPriceGettingServiceMock;

    /**
     * @return void
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $pairsPriceGettingServiceMock = $this->getMockBuilder(PairsPriceGettingService::class)
            ->onlyMethods(['getAllPairs'])
            ->disableOriginalConstructor()
            ->getMock();
        $pairsPriceGettingServiceMock->method('getAllPairs')->willReturn(self::ALL_PAIRS_RESPONSE_MOCK);

        $this->pairsPriceGettingServiceMock = $pairsPriceGettingServiceMock;
    }

    /**
     * @return void
     *
     * @throws BaseException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testExistPairPriceGetting(): void
    {
        $result = $this->pairsPriceGettingServiceMock->getPairPrice(
            self::BTC,
            self::RUB,
        );

        self::assertEquals($result, $this->getExistPairPriceGettingExpectedResult());
    }

    /**
     * @return void
     *
     * @throws BaseException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testNotExistPairPriceGetting(): void
    {
        $result = $this->pairsPriceGettingServiceMock->getPairPrice(
            self::RUB,
            self::BTC,
        );

        self::assertEquals($result, []);
    }

    /**
     * @return void
     *
     * @throws BaseException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testUniquePairPriceGetting(): void
    {
        $result = $this->pairsPriceGettingServiceMock->getPairPrice(
            self::BTC,
            self::DOGE,
        );

        self::assertEquals($result, $this->getUniquePairPriceGettingExpectedResult());
    }

    /**
     * @return void
     *
     * @throws BaseException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testMinPairPriceGetting(): void
    {
        $result = $this->pairsPriceGettingServiceMock->getMinPairPrice(
            self::BTC,
            self::RUB,
        );

        self::assertEquals($result, $this->getMinPairPriceGettingExpectedResult());
    }

    /**
     * @return void
     *
     * @throws BaseException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testMaxPairPriceGetting(): void
    {
        $result = $this->pairsPriceGettingServiceMock->getMaxPairPrice(
            self::BTC,
            self::RUB,
        );

        self::assertEquals($result, $this->getMaxPairPriceGettingExpectedResult());
    }

    /**
     * @return array[]
     */
    private function getExistPairPriceGettingExpectedResult(): array
    {
        return [
            [
                "market" => self::LOW_PRICE_MARKET_NAME,
                "price" => 100,
                "fromAsset" => self::BTC,
                "toAsset" => self::RUB,
            ],
            [
                "market" => self::MEDIUM_PRICE_MARKET_NAME,
                "price" => 200,
                "fromAsset" => self::BTC,
                "toAsset" => self::RUB,
            ],
            [
                "market" => self::HIGH_PRICE_MARKET_NAME,
                "price" => 300,
                "fromAsset" => self::BTC,
                "toAsset" => self::RUB,
            ],
        ];
    }

    /**
     * @return array[]
     */
    private function getUniquePairPriceGettingExpectedResult(): array
    {
        return [
            [
                "market" => self::MEDIUM_PRICE_MARKET_NAME,
                "price" => 30,
                "fromAsset" => self::BTC,
                "toAsset" => self::DOGE,
            ],
        ];
    }

    /**
     * @return array[]
     */
    private function getMinPairPriceGettingExpectedResult(): array
    {
        return [
            "market" => self::LOW_PRICE_MARKET_NAME,
            "price" => 100,
            "fromAsset" => self::BTC,
            "toAsset" => self::RUB,
        ];
    }

    /**
     * @return array[]
     */
    private function getMaxPairPriceGettingExpectedResult(): array
    {
        return [
            "market" => self::HIGH_PRICE_MARKET_NAME,
            "price" => 300,
            "fromAsset" => self::BTC,
            "toAsset" => self::RUB,
        ];
    }
}
