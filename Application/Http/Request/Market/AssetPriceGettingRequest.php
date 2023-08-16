<?php

declare(strict_types=1);

namespace Application\Http\Request\Market;

use Application\Http\Request\BaseRequest;

class AssetPriceGettingRequest extends BaseRequest
{
    /**
     * @return \string[][]
     */
    public function rules(): array
    {
        return [
            'fromAsset' => [
                'required',
                'string',
            ],
            'toAsset' => [
                'required',
                'string'
            ]
        ];
    }

    /**
     * @return string
     */
    public function getFromAsset(): string
    {
        return $this->get('fromAsset');
    }

    /**
     * @return string
     */
    public function getToAsset(): string
    {
        return $this->get('toAsset');
    }
}
