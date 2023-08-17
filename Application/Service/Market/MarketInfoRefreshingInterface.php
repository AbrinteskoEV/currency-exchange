<?php

declare(strict_types=1);

namespace Application\Service\Market;

interface MarketInfoRefreshingInterface
{
    public function refreshAssetPriceList(): void;
}
