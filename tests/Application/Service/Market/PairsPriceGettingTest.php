<?php

declare(strict_types=1);

namespace Tests\Application\Service\Market;

use Application\Exceptions\BaseException;
use Monolog\Test\TestCase;

class PairsPriceGettingTest extends TestCase
{
    private PairsPriceGettingServiceMock $pairsPriceGettingServiceMock;

    /**
     * @return void
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->pairsPriceGettingServiceMock = app()->make(PairsPriceGettingServiceMock::class);
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
            PairsPriceGettingServiceMock::BTC,
            PairsPriceGettingServiceMock::RUB,
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
            PairsPriceGettingServiceMock::RUB,
            PairsPriceGettingServiceMock::BTC,
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
            PairsPriceGettingServiceMock::BTC,
            PairsPriceGettingServiceMock::DOGE,
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
            PairsPriceGettingServiceMock::BTC,
            PairsPriceGettingServiceMock::RUB,
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
            PairsPriceGettingServiceMock::BTC,
            PairsPriceGettingServiceMock::RUB,
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
                "market" => PairsPriceGettingServiceMock::LOW_PRICE_MARKET_NAME,
                "price" => 100,
                "fromAsset" => PairsPriceGettingServiceMock::BTC,
                "toAsset" => PairsPriceGettingServiceMock::RUB,
            ],
            [
                "market" => PairsPriceGettingServiceMock::MEDIUM_PRICE_MARKET_NAME,
                "price" => 200,
                "fromAsset" => PairsPriceGettingServiceMock::BTC,
                "toAsset" => PairsPriceGettingServiceMock::RUB,
            ],
            [
                "market" => PairsPriceGettingServiceMock::HIGH_PRICE_MARKET_NAME,
                "price" => 300,
                "fromAsset" => PairsPriceGettingServiceMock::BTC,
                "toAsset" => PairsPriceGettingServiceMock::RUB,
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
                "market" => PairsPriceGettingServiceMock::MEDIUM_PRICE_MARKET_NAME,
                "price" => 30,
                "fromAsset" => PairsPriceGettingServiceMock::BTC,
                "toAsset" => PairsPriceGettingServiceMock::DOGE,
            ],
        ];
    }

    /**
     * @return array[]
     */
    private function getMinPairPriceGettingExpectedResult(): array
    {
        return [
            "market" => PairsPriceGettingServiceMock::LOW_PRICE_MARKET_NAME,
            "price" => 100,
            "fromAsset" => PairsPriceGettingServiceMock::BTC,
            "toAsset" => PairsPriceGettingServiceMock::RUB,
        ];
    }

    /**
     * @return array[]
     */
    private function getMaxPairPriceGettingExpectedResult(): array
    {
        return [
            "market" => PairsPriceGettingServiceMock::HIGH_PRICE_MARKET_NAME,
            "price" => 300,
            "fromAsset" => PairsPriceGettingServiceMock::BTC,
            "toAsset" => PairsPriceGettingServiceMock::RUB,
        ];
    }
}
