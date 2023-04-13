<?php

declare(strict_types=1);

use App\Core\Kernel;

require_once('../vendor/autoload.php');

/**
 * Bootstrapping for the Parallel Booster Framework
 *
 * NASA: "Interplanetary mission operations may be considered in four phases:
 * the Launch Phase, the Cruise Phase, the Encounter Phase, and,
 * depending on the state of spacecraft health and mission funding,
 * the Extended Operations Phase"
 */

$app = new Kernel();
$app->liftOf();
