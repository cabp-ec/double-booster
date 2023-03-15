<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Session;
use App\Interfaces\ServiceInterface;

class BaseService implements ServiceInterface
{
    protected Session $session;

    public function __construct(
        protected array $config = []
    )
    {
        $sessionId = str_replace('.', '', $_SERVER['REMOTE_ADDR']);
        $this->session = Session::getInstance($sessionId);
    }

    /**
     * @inheritDoc
     */
    public function healthCheck(): bool
    {
        // TODO: implement
        return false;
    }
}
