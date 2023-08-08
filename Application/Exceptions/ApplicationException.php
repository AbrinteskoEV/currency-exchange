<?php

declare(strict_types=1);

namespace Application\Exceptions;

class ApplicationException extends BaseException
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return static::class;
    }
}
