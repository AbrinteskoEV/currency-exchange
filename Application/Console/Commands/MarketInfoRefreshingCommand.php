<?php

declare(strict_types=1);

namespace Application\Console\Commands;

use Application\Exceptions\BaseException;
use Application\Service\Market\MarketInfoRefreshingService;
use Illuminate\Console\Command;

class MarketInfoRefreshingCommand extends Command
{
    private const SLEEP_TIME = 30;

    protected $signature = 'refresh_market_info';

    /**
     * @param MarketInfoRefreshingService $marketInfoRefreshingService
     *
     * @return void
     *
     * @throws BaseException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function handle(MarketInfoRefreshingService $marketInfoRefreshingService): void
    {
        $marketInfoRefreshingService->refreshAllMarkets();

        sleep(self::SLEEP_TIME);

        $marketInfoRefreshingService->refreshAllMarkets();
    }
}
