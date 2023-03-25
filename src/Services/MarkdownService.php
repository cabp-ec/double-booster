<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Interfaces\ServiceInterface;

class MarkdownService implements ServiceInterface
{
    /**
     * @inheritDoc
     */
    public function healthCheck(): bool
    {
        // TODO: Implement healthCheck() method.
        return false;
    }
}
