<?php

declare(strict_types=1);

namespace Application\Http\Controllers;

use Application\Exceptions\ApplicationException;
use Application\Http\Request\Market\AssetPriceGettingRequest;
use Application\Service\Market\PairsPriceGettingService;

class MarketController
{
    /**
     * @param AssetPriceGettingRequest $request
     * @param PairsPriceGettingService $pairsPriceGettingService
     *
     * @return array
     *
     * @throws ApplicationException
     * @throws \Application\Exceptions\BaseException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getExchangePairsInfo(
        AssetPriceGettingRequest $request,
        PairsPriceGettingService $pairsPriceGettingService
    ): array {
        $priceInfo = $pairsPriceGettingService->getPairPrice($request->getFromAsset(), $request->getToAsset());

        if (!$priceInfo) {
            throw new ApplicationException('Asset pair not found');
        }

        return $priceInfo;
    }
}
