<?php

namespace Application\Service\Market;

interface PairsPriceGettingInterface
{
    /**
     * @return array
     */
    public function getPairsPriceList(): array;
}