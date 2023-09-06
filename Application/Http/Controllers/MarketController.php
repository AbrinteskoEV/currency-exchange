<?php

declare(strict_types=1);

namespace Application\Http\Controllers;

use Application\Exceptions\ApplicationException;
use Application\Exceptions\BaseException;
use Application\Http\Request\Market\ManyPairsPriceGettingRequest;
use Application\Http\Request\Market\PairPriceGettingRequest;
use Application\Service\Market\PairsPriceGettingService;

class MarketController
{
    /**
     * @param PairPriceGettingRequest $request
     * @param PairsPriceGettingService $pairsPriceGettingService
     *
     * @return array
     *
     * @throws ApplicationException
     * @throws BaseException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getPairPrice(
        PairPriceGettingRequest $request,
        PairsPriceGettingService $pairsPriceGettingService
    ): array {
        $priceInfo = $pairsPriceGettingService->getPairPrice($request->getFromAsset(), $request->getToAsset());

        if (!$priceInfo) {
            throw new ApplicationException('Asset pair not found');
        }

        return $priceInfo;
    }

    /**
     * @param PairPriceGettingRequest $request
     * @param PairsPriceGettingService $pairsPriceGettingService
     *
     * @return array
     *
     * @throws ApplicationException
     * @throws BaseException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getMinPairPrice(
        PairPriceGettingRequest $request,
        PairsPriceGettingService $pairsPriceGettingService
    ): array {
        $priceInfo = $pairsPriceGettingService->getMinPairPrice($request->getFromAsset(), $request->getToAsset());

        if (!$priceInfo) {
            throw new ApplicationException('Asset pair not found');
        }

        return $priceInfo;
    }

    /**
     * @param PairPriceGettingRequest $request
     * @param PairsPriceGettingService $pairsPriceGettingService
     *
     * @return array
     *
     * @throws ApplicationException
     * @throws BaseException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getMaxPairPrice(
        PairPriceGettingRequest $request,
        PairsPriceGettingService $pairsPriceGettingService
    ): array {
        $priceInfo = $pairsPriceGettingService->getMaxPairPrice($request->getFromAsset(), $request->getToAsset());

        if (!$priceInfo) {
            throw new ApplicationException('Asset pair not found');
        }

        return $priceInfo;
    }

    /**
     * @param ManyPairsPriceGettingRequest $request
     * @param PairsPriceGettingService $pairsPriceGettingService
     *
     * @return array
     *
     * @throws ApplicationException
     * @throws BaseException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getManyPairPrice(
        ManyPairsPriceGettingRequest $request,
        PairsPriceGettingService $pairsPriceGettingService
    ): array {
        $response = [];

        foreach ($request->getPairList() as $pair) {
            $pairPriceInfo = $pairsPriceGettingService->getPairPrice(
                $pair['fromAsset'],
                $pair['toAsset'],
            );

            if ($pairPriceInfo) {
                $response[] = $pairPriceInfo;
            }
        }

        if (!$response) {
            throw new ApplicationException('No one pair not found');
        }

        return $response;
    }

    /**
     * @param ManyPairsPriceGettingRequest $request
     * @param PairsPriceGettingService $pairsPriceGettingService
     *
     * @return array
     *
     * @throws ApplicationException
     * @throws BaseException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getManyMinPairPrice(
        ManyPairsPriceGettingRequest $request,
        PairsPriceGettingService $pairsPriceGettingService
    ): array {
        $response = [];

        foreach ($request->getPairList() as $pair) {
            $pairPriceInfo = $pairsPriceGettingService->getMinPairPrice(
                $pair['fromAsset'],
                $pair['toAsset'],
            );

            if ($pairPriceInfo) {
                $response[] = $pairPriceInfo;
            }
        }

        if (!$response) {
            throw new ApplicationException('No one pair not found');
        }

        return $response;
    }

    /**
     * @param ManyPairsPriceGettingRequest $request
     * @param PairsPriceGettingService $pairsPriceGettingService
     *
     * @return array
     *
     * @throws ApplicationException
     * @throws BaseException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getManyMaxPairPrice(
        ManyPairsPriceGettingRequest $request,
        PairsPriceGettingService $pairsPriceGettingService
    ): array {
        $response = [];

        foreach ($request->getPairList() as $pair) {
            $pairPriceInfo = $pairsPriceGettingService->getMaxPairPrice(
                $pair['fromAsset'],
                $pair['toAsset'],
            );

            if ($pairPriceInfo) {
                $response[] = $pairPriceInfo;
            }
        }

        if (!$response) {
            throw new ApplicationException('No one pair not found');
        }

        return $response;
    }
}
