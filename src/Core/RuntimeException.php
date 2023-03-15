<?php

declare(strict_types=1);

namespace App\Core;

use App\Interfaces\ExceptionInterface;

class RuntimeException extends \RuntimeException implements ExceptionInterface
{
}
