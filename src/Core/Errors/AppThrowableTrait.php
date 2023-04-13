<?php

declare(strict_types=1);

namespace App\Core\Errors;

use App\Core\Environment;

trait AppThrowableTrait
{
    /**
     * Set default properties for the throwable
     *
     * @return void
     */
    private function setDefaults(): void
    {
        $this->vendor = Environment::get(self::KEY_VENDOR, 'CABP');
        $this->product = Environment::get(self::KEY_PRODUCT, 'WEBSITE');
        $this->version = Environment::get(self::KEY_VERSION, '1.0');
        $this->host = Environment::get(self::KEY_HOST, '');
        $this->format = Environment::get(self::KEY_LOG_FORMAT, 'JSON');
    }

    /**
     * Make a log entry
     *
     * @return string
     */
    private function makeLogEntry(): string
    {
        $data = [
            'vendor' => $this->vendor,
            'product' => $this->product,
            'version' => $this->version,
            'host' => $this->host,
            'type' => self::TYPE,
            'severity' => $this->getCode(),
            'timestamp' => time(),
            'message' => $this->getMessage(),
        ];

        if (str_starts_with($this->format, self::KEY_FLAT)) {
            return implode(substr($this->format, -1), $data);
        }

        return json_encode($data);
    }
}
