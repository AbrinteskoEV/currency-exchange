<?php

use Domain\Dictionary\Market\Binance\BinanceRequestLabelDictionary;

return [
    'apiUrl' => 'https://api.binance.com/api',
    'requestSettings' => [
        BinanceRequestLabelDictionary::COMMON_DATA_GETTING_LABEL => [
            'weight' => 10,
            'minInterval' => 5,
            'cacheKey' => 'CommonData',
        ],
        BinanceRequestLabelDictionary::PAIRS_PRICE_GETTING_LABEL => [
            'weight' => 2,
            'minInterval' => 1,
            'cacheKey' => 'PairsPrice',
        ],
    ]
];
