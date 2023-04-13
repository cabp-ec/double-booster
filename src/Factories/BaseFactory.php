<?php

declare(strict_types=1);

namespace App\Factories;

use Countable;
use App\Core\Interfaces\FactoryInterface;

abstract class BaseFactory implements Countable, FactoryInterface
{
    /**
     * @inheritDoc
     */
    abstract public function get(string $name): ?object;

    /**
     * Create an object
     *
     * @param string $name
     * @param array $args
     * @return object|null
     */
    // TODO: change the return type after upgrading to php 8.2
    abstract protected function create(string $name, array $args): ?object;

    /**
     * Get the total amount of built objects
     *
     * @return int
     */
    abstract public function count(): int;
}
