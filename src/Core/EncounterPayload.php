<?php

declare(strict_types=1);

namespace App\Core;

final class EncounterPayload
{
    public function __construct(
        private string $uri,
        private string $requestMethod,
        private array  $postedData,
        private array  $requestHeaders
    )
    {
    }

    /**
     * Get the uri property
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Get the requestMethod property
     * @return string
     */
    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }

    /**
     * Get the postedData property
     * @return array
     */
    public function getPostedData(): array
    {
        return $this->postedData;
    }

    /**
     * Get the requestHeaders property
     * @return array
     */
    public function getRequestHeaders(): array
    {
        return $this->requestHeaders;
    }
}
