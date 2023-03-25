<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Interfaces\LoggerInterface;
use Exception;

final class FileLogger implements LoggerInterface
{
    private const KEY_EXCEPTION = 'EXCEPTION';
    private const KEY_WARNING = 'WARNING';
    private const KEY_ERROR = 'ERROR';

    private string $logPathError;
    private string $logPathWarning;
    private string $logPathMessage;
    private string $logPathException;

    /**
     * Constructor for the FileLogger class
     *
     * @param string $logPath
     */
    public function __construct(string $logPath)
    {
        $this->logPathError = $logPath . 'error.log';
        $this->logPathWarning = $logPath . 'warning.log';
        $this->logPathMessage = $logPath . 'message.log';
        $this->logPathException = $logPath . 'exception.log';
    }

    /**
     * Clone this object
     *
     * @return void
     */
    protected function __clone()
    {
    }

    /**
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception('Can\'t wake up');
    }

    /**
     * @inheritDoc
     * TODO: upgrade and use ENUMS for the match expression
     */
    public function log(string $message, string $key): bool
    {
        return error_log("$message\n", 3, match ($key) {
            self::KEY_EXCEPTION => $this->logPathException,
            self::KEY_WARNING => $this->logPathWarning,
            self::KEY_ERROR => $this->logPathError,
            default => $this->logPathMessage,
        });
    }
}
