<?php

declare(strict_types=1);

namespace Infrastructure\Service\Market\Binance\DTO;

class BinanceRequestDTO
{
    private string $apiMethod;
    private string $endpoint;
    private string $label;
    private array $data = [];

    /**
     * @param string $apiMethod
     * @param string $endpoint
     * @param string $label
     */
    public function __construct(
        string $apiMethod,
        string $endpoint,
        string $label
    ) {
        $this->apiMethod = $apiMethod;
        $this->endpoint = $endpoint;
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getApiMethod(): string
    {
        return $this->apiMethod;
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return self
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }
}
