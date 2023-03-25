<?php

declare(strict_types=1);

namespace App\Core;

use Exception;
use Dotenv\Dotenv;

final class Environment
{
    private Dotenv $dotEnv;

    /**
     * Constructor for the Environment class
     *
     * @param string $basePath
     */
    public function __construct(string $basePath)
    {
        $this->dotEnv = Dotenv::createImmutable($basePath);
        $this->dotEnv->load();
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
     * Get the value from an ENV var
     *
     * @param string $key
     * @param string|null $value
     * @return string
     */
    static public function get(string $key, ?string $value = null): string
    {
        return $_ENV[$key] ?? $value;
    }
}
