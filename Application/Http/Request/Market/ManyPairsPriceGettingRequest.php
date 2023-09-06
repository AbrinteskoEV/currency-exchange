<?php

declare(strict_types=1);

namespace Application\Http\Request\Market;

use Application\Http\Request\BaseRequest;

class ManyPairsPriceGettingRequest extends BaseRequest
{
    /**
     * @return \string[][]
     */
    public function rules(): array
    {
        return [
            'pairList' => [
                'array',
                'required',
            ],
            'pairList.*.fromAsset' => [
                'required',
                'string',
            ],
            'pairList.*.toAsset' => [
                'required',
                'string'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getPairList(): array
    {
        return $this->get('pairList');
    }
}
