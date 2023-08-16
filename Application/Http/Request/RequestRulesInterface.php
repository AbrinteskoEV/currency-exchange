<?php

namespace Application\Http\Request;

interface RequestRulesInterface
{
    /**
     * @return array
     */
    public function rules(): array;
}
