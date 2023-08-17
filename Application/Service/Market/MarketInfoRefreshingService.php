<?php

declare(strict_types=1);

namespace Application\Service\Market;

use Application\Exceptions\BaseException;
use Application\Service\Market\Binance\BinanceMarketInfoRefreshingService;
use Domain\Dictionary\Market\MarketDictionary;
use Illuminate\Container\Container;

class MarketInfoRefreshingService
{
    private Container $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $market
     *
     * @return void
     *
     * @throws BaseException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function refreshByMarket(string $market): void
    {
        $refreshingService = match ($market) {
            MarketDictionary::BINANCE_MARKET => $this->container->get(BinanceMarketInfoRefreshingService::class),
            default => throw new BaseException("Not implemented market [$market]")
        };

        /** @var MarketInfoRefreshingInterface $refreshingService */
        $refreshingService->refreshAssetPriceList();
    }

    /**
     * @return void
     *
     * @throws BaseException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function refreshAllMarkets(): void
    {
        foreach (MarketDictionary::MARKET_LIST as $market) {
            $this->refreshByMarket($market);
        }
    }
}
