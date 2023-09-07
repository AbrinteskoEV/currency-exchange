<?php

declare(strict_types=1);

namespace Tests\Application\Service\Market;

use Application\Service\Market\PairsPriceGettingService;

class PairsPriceGettingServiceMock extends PairsPriceGettingService
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

    /**
     * @return array[]
     */
    public function getAllPairs(): array
    {
        return self::ALL_PAIRS_RESPONSE_MOCK;
    }
}
