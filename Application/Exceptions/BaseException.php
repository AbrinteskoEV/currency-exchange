<?php

declare(strict_types=1);

namespace Application\Exceptions;

class BaseException extends \Exception
{
    private array $context;

    public function __construct(
        string $message = "",
        array $context = [],
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        $this->context = $context;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }
}
