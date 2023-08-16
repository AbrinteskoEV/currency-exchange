<?php

declare(strict_types=1);

namespace Infrastructure\Service\Market\Binance\DTO;

class BinanceCommonDataDTO
{
    private int $minuteApiWeightLimit;
    private array $symbolInfoList;

    /**
     * @param int $minuteApiWeightLimit
     * @param array $symbolInfoList
     */
    public function __construct(
        int $minuteApiWeightLimit,
        array $symbolInfoList
    ) {
        $this->minuteApiWeightLimit = $minuteApiWeightLimit;
        $this->symbolInfoList = $symbolInfoList;
    }

    /**
     * @return int
     */
    public function getMinuteApiWeightLimit(): int
    {
        return $this->minuteApiWeightLimit;
    }

    /**
     * @return array
     */
    public function getSymbolInfoList(): array
    {
        return $this->symbolInfoList;
    }
}
