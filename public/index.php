<?php

declare(strict_types=1);

use App\Kernel;
use App\Core\Session;

require_once('../vendor/autoload.php');

/**
 * Bootstrapping for the Parallel Booster Framework
 *
 * NASA: "Interplanetary mission operations may be considered in four phases:
 * the Launch Phase, the Cruise Phase, the Encounter Phase, and,
 * depending on the state of spacecraft health and mission funding,
 * the Extended Operations Phase"
 */

$sessionId = str_replace('.', '', $_SERVER['REMOTE_ADDR']);
$session = Session::getInstance($sessionId);
//$session->destroy();
//exit;
$session->set('ip', $_SERVER['REMOTE_ADDR'])
    ->set('email', null)
    ->set('paymentType', 'CASH')
    ->set('totalItems', 0.0)
    ->set('subTotal', 0.0)
    ->set('taxes', 0.0)
    ->set('total', 0.0)
    ->set('change', 0.0)
    ->set('cart', $_SESSION['cart'] ?? []);

//echo '<pre>SESSION<br><br>';
//var_dump($session->data());
//var_dump($_SESSION);
//exit;

$basePath = dirname(__DIR__) . DIRECTORY_SEPARATOR;
$settings = include_once $basePath . 'config' . DIRECTORY_SEPARATOR . 'settings.php';
$serviceScaffold = include_once $basePath . 'config' . DIRECTORY_SEPARATOR . 'serviceScaffold.php';

$app = Kernel::instance($serviceScaffold, $settings, $session);
$app->liftOf();
