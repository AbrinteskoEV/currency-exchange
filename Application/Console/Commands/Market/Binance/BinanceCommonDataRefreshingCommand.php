<?php

declare(strict_types=1);

namespace Application\Console\Commands\Market\Binance;

use Illuminate\Console\Command;
use Infrastructure\Service\Market\Binance\BinanceCommonDataRefreshingService;

class BinanceCommonDataRefreshingCommand extends Command
{
    protected $signature = 'refresh_binance_common_data';

    /**
     * @param BinanceCommonDataRefreshingService $binanceCommonDataRefreshingService
     *
     * @return void
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function handle(
        BinanceCommonDataRefreshingService $binanceCommonDataRefreshingService
    ): void {
        $binanceCommonDataRefreshingService->refreshCommonData();
    }
}
