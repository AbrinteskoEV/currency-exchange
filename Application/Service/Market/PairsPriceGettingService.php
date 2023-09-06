<?php

declare(strict_types=1);

namespace Application\Service\Market;

use Application\Exceptions\BaseException;
use Application\Service\Market\Binance\BinancePairsPriceGettingService;
use Domain\Dictionary\Market\MarketDictionary;
use Illuminate\Container\Container;

class PairsPriceGettingService
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
     * @param string $fromAsset
     * @param string $toAsset
     *
     * @return array
     *
     * @throws BaseException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getMinPairPrice(string $fromAsset, string $toAsset): array
    {
        $pairPriceInfoList = $this->getPairPrice($fromAsset, $toAsset);

        if (!$pairPriceInfoList) {
            return [];
        }

        $firstPair = array_shift($pairPriceInfoList);
        $minPrice = $firstPair['price'];
        $result = $firstPair;

        foreach ($pairPriceInfoList as $pairPriceInfo) {
            $pairPrice = $pairPriceInfo['price'];

            if ($pairPrice < $minPrice) {
                $minPrice = $pairPrice;
                $result = $pairPriceInfo;
            }
        }

        return $result;
    }

    /**
     * @param string $fromAsset
     * @param string $toAsset
     *
     * @return array
     *
     * @throws BaseException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getMaxPairPrice(string $fromAsset, string $toAsset): array
    {
        $pairPriceInfoList = $this->getPairPrice($fromAsset, $toAsset);

        if (!$pairPriceInfoList) {
            return [];
        }

        $firstPair = array_shift($pairPriceInfoList);
        $maxPrice = $firstPair['price'];
        $result = $firstPair;

        foreach ($pairPriceInfoList as $pairPriceInfo) {
            $pairPrice = $pairPriceInfo['price'];

            if ($pairPrice > $maxPrice) {
                $maxPrice = $pairPrice;
                $result = $pairPriceInfo;
            }
        }

        return $result;
    }

    /**
     * @param string $fromAsset
     * @param string $toAsset
     *
     * @return array
     *
     * @throws BaseException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getPairPrice(string $fromAsset, string $toAsset): array
    {
        $result = [];

        foreach ($this->getAllPairs() as $market => $marketPairsPriceInfo) {
            $price = $marketPairsPriceInfo[$fromAsset][$toAsset] ?? null;

            if ($price !== null) {
                $result[] = [
                    'market' => $market,
                    'price' => $price,
                    'fromAsset' => $fromAsset,
                    'toAsset' => $toAsset,
                ];
            }
        }

        return $result;
    }

    /**
     * @return array
     *
     * @throws BaseException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getAllPairs(): array
    {
        $result = [];

        foreach (MarketDictionary::MARKET_LIST as $market) {
            $result[$market] = $this->getAllPairsByMarket($market);
        }

        return $result;
    }

    /**
     * @param string $market
     *
     * @return array
     *
     * @throws BaseException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getAllPairsByMarket(string $market): array
    {
        $priceGettingService = match($market) {
            MarketDictionary::BINANCE_MARKET => $this->container->get(BinancePairsPriceGettingService::class),
            default =>  throw new BaseException("Not implemented market [$market]")
        };

        /** @var PairsPriceGettingInterface $priceGettingService */
        return $priceGettingService->getPairsPriceList();
    }
}
