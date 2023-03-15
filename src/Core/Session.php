<?php

declare(strict_types=1);

namespace App\Core;

/**
 * @property string $email
 * @property mixed|null $cart
 */
class Session
{
    const SESSION_STARTED = true;
    const SESSION_NOT_STARTED = false;

    private bool $sessionState = self::SESSION_NOT_STARTED;
    private static $instance;

    private function __construct()
    {
    }

    /**
     * Get an instance of this class
     *
     * @param string $sessionId
     * @return Session
     */
    public static function getInstance(string $sessionId): Session
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }

        self::$instance->startSession($sessionId);
        return self::$instance;
    }

    /**
     * Start or re-start a session
     * TODO: debug this method
     *
     * @param string $sessionId
     * @return bool
     */
    public function startSession(string $sessionId): bool
    {
//        session_id($sessionId);
//        session_start();

        if ($this->sessionState == self::SESSION_NOT_STARTED) {
            $this->sessionState = session_start();
        }

        return $this->sessionState;
    }

    /**
     * Store data in the session
     * Example: $instance->foo = 'bar';
     *
     * @param $name
     * @param $value
     * @return void
     */
    public function __set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * Gets data from the session
     * Example: echo $instance->foo;
     *
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }

        return null;
    }

    public function __isset($name)
    {
        return isset($_SESSION[$name]);
    }

    public function __unset($name)
    {
        unset($_SESSION[$name]);
    }

    public function data(): array
    {
        return $_SESSION ?? [];
    }

    public function set($name, $value): static
    {
        $_SESSION[$name] = $value;
        return $this;
    }

    /**
     * Destroy the current session
     *
     * @return $this
     */
    public function destroy(): static
    {
        if ($this->sessionState === self::SESSION_STARTED) {
            $this->sessionState = !session_destroy();

            if (isset($_SESSION)) {
                unset($_SESSION);
            }
        }

        return $this;
    }
}
