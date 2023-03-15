<?php

declare(strict_types=1);

namespace App\Interfaces;

interface TransactionInterface
{
    /**
     * Calculate total values and taxes for this transaction
     *
     * @return void
     */
    public function calculate(): void;

    /**
     * Return a raw version of transaction values
     *
     * @return array
     */
    public function data(): array;

    /**
     * Return a formatted version of transaction values
     *
     * @return string
     */
    public function __toString(): string;
}
