<?php

namespace App\Core;

abstract class ConfigReceptor
{
    /**
     * Clean-up dirty config values for this class
     *
     * @param array $dirty
     * @param array $clean
     * @return void
     */
    abstract protected function configCleanUp(array $dirty, array &$clean): void;
}
