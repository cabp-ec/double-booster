<?php

declare(strict_types=1);

namespace App\Core\Errors;

class ErrorOutput
{
    public function __construct(
        private string $title,
        private string $body,
        private ?string $caption = null,
    )
    {
    }
}
