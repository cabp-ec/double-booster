<?php

declare(strict_types=1);

namespace App\Core\Errors;

use Error;
use App\Core\Interfaces\LoggableInterface;

class AppError extends Error implements LoggableInterface
{
    use AppThrowableTrait;
    private const TYPE = 'ERROR';
    private const KEY_VENDOR = 'VENDOR';
    private const KEY_PRODUCT = 'PRODUCT';
    private const KEY_VERSION = 'VERSION';
    private const KEY_HOST = 'HOST';
    private const KEY_LOG_FORMAT = 'LOG_FORMAT';
    private const KEY_FLAT = 'FLAT';
    private string $vendor;
    private string $product;
    private string $version;
    private string $host;
    private string $format;
    public function __construct(string $message, int $code = 1)
    {
        parent::__construct($message, $code);
        $this->setDefaults();
    }

    /**
     * @inheritDoc
     */
    final public function getType(): string
    {
        return self::TYPE;
    }

    /**
     * @inheritDoc
     */
    final public function getLogEntry(): string
    {
        return $this->makeLogEntry();
    }
}
