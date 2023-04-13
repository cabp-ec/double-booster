<?php

declare(strict_types=1);

namespace App\Core;

use App\Factories\ControllerFactory;
use App\Factories\ServiceFactory;
use Exception;
use App\Core\Interfaces\LoggableInterface;
use App\Factories\LoggerFactory;

final class Kernel
{
    private string $basePath;
    private Environment $environment;
    private LoggerFactory $loggerFactory;
    private ErrorHandler $errorHandler;
    private Router $router;
    private static ?Kernel $instance = null;

    /**
     * Launch Phase, the start-up point for the framework
     */
    public function __construct()
    {
        $ds = DIRECTORY_SEPARATOR;
        $this->basePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR;
        $configPath = $this->basePath . "config$ds";
        $routesPath = $configPath . "routes$ds";
        $viewsPath = $this->basePath . "resources$ds" . "views$ds";
        $servicesPath = $configPath . Environment::get('SETTINGS_FILE', 'services') . '.php';
        $settings = include_once $configPath . Environment::get('SETTINGS_FILE', 'settings') . '.php';

        $this->environment = new Environment($this->basePath);
        $this->loggerFactory = new LoggerFactory($this->basePath . "logs$ds");
        $this->errorHandler = new ErrorHandler($this->loggerFactory->createFileLogger());
        $this->cruisePhase($configPath, $routesPath, $servicesPath, $viewsPath, $settings);
        unset($ds);
    }

    /**
     * The Cruise Phase
     *
     * @param string $configPath
     * @param string $routesPath
     * @param string $servicesPath
     * @param string $viewsPath
     * @param array $settings
     * @return void
     */
    private function cruisePhase(string $configPath, string $routesPath, string $servicesPath, string $viewsPath, array $settings): void
    {
        $this->router = new Router(
            $settings['router'],
            [
                Router::SEGMENT_WEB => include_once $routesPath . 'web.php',
                Router::SEGMENT_RPC => include_once $routesPath . 'rpc.php',
                Router::SEGMENT_REST => include_once $routesPath . 'rest.php',
            ],
            include_once $configPath . 'middleware.php',
            new ControllerFactory(new Container(
                $this->errorHandler,
                new SessionHandler(),
                new ServiceFactory(include_once $servicesPath, $this->errorHandler),
                new ViewsHandler($viewsPath)
            )),
        );
    }

    /**
     * The Encounter Phase
     *
     * @return EncounterPayload
     */
    private function encounterPhase(): EncounterPayload
    {
        $postedData = json_decode(file_get_contents('php://input'), true);
        $headers = [
            'Accept' => $_SERVER['HTTP_ACCEPT'] ?? Router::DEFAULT_ACCEPT_HEADER,
        ];

        return new EncounterPayload(
            $_GET['path'] ?? '/',
            strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'),
            $postedData ?? [],
            $headers
        );
    }

    /**
     * The Extended Operations Phase
     */
    public function liftOf(): void
    {
        try {
            $response = $this->router->operate($this->encounterPhase());
            $response->send();
        } catch (LoggableInterface $e) {
            $this->errorHandler->catch($e);
        }
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
}
