<?php

declare(strict_types=1);

namespace Application\Console\Commands\Market\Binance;

use Illuminate\Console\Command;
use Infrastructure\Service\Market\Binance\Cache\BinanceApiDataCachingService;

class BinanceUsedApiWeightRefreshingCommand extends Command
{
    protected $signature = 'refresh_binance_used_weight';

    /**
     * @param BinanceApiDataCachingService $binanceApiDataCachingService
     *
     * @return void
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function handle(BinanceApiDataCachingService $binanceApiDataCachingService): void
    {
        $binanceApiDataCachingService->removeUsedWeight();
    }
}
