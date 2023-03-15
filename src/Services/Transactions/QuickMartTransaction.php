<?php

declare(strict_types=1);

namespace App\Services\Transactions;

use App\Interfaces\TransactionInterface;

class QuickMartTransaction implements TransactionInterface
{
    public const PAYMENT_TYPE_CASH = 'CASH';

    public function __construct(
        public int    $totalItems = 0,
        public float  $subTotal = 0.0,
        public float  $taxDeductible = 6.5,
        public float  $total = 0.0,
        public string $paymentType = self::PAYMENT_TYPE_CASH,
        public float  $paymentValue = 0.0,
        public float  $paymentChange = 0.0,
        public int    $starLineLimit = 20
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function calculate(): void
    {
        $subTotal = 0; // i.e. before taxes
    }

    /**
     * @inheritDoc
     */
    public function data(): array
    {
        // TODO: Implement data() method.
        return [];
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        // TODO: Implement __toString() method.
    }
}
