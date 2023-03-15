<?php

declare(strict_types=1);

namespace App;

use App\Core\Session;
use App\Services\FlatFileCrmService;
use App\Services\FlatFileQuickMartService;
use Exception;
use App\Interfaces\ContainerInterface;
use App\Core\EncounterPayload;
use App\Core\ErrorHandler;
use App\Core\Modules;
use App\Core\Services;
use App\Core\Container;
use App\Core\Middleware;
use App\Core\Router;
use App\Services\FileStorageService;

final class Kernel
{
    private static ?Kernel $instance = null;
    private ContainerInterface $container;
    private Router $router;

    /**
     * The Kernel class, the start-up point for our framework
     *
     * @param array $serviceScaffold
     * @param array $settings
     * @param Session $session
     */
    private function __construct(
        private array   $serviceScaffold,
        private array   $settings,
        private Session $session
    )
    {
    }

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

    /**
     * Check if ship is ready to lift-off
     *
     * @return void
     */
    private function preflightChecks(): void
    {
        if (empty($this->settings)) {
            throw new \Error('Settings are empty');
        }

        // Perform other checks here...
    }

    /**
     * Setup shared services
     *
     * @return Services
     */
    private function fireUpServices(): Services
    {
        $fileStorageService = new FileStorageService($this->settings['resourcePath']);
        $fileCrmService = new FlatFileCrmService(
            $fileStorageService,
            $this->settings['resourcePath'],
            $this->settings['services']['FlatFileQuickMart']
        );

        $services = new Services();

        $services->set($fileStorageService)
            ->set(new FlatFileQuickMartService(
                $fileStorageService,
                $fileCrmService,
                $this->settings['resourcePath'],
                $this->settings['services']['FlatFileQuickMart']
            ))
            ->set($fileCrmService);

        return $services;
    }

    /**
     * Setup modules
     *
     * @return Modules
     */
    private function fireUpModules(): Modules
    {
        $modules = new Modules();
        return $modules;
    }

    /**
     * The Launch Phase
     *
     * @return void
     */
    private function launchPhase(): void
    {
        $this->preflightChecks();

        $this->container = new Container(
            new ErrorHandler(),
            $this->session,
            $this->fireUpServices(),
            $this->fireUpModules()
        );
    }

    /**
     * The Cruise Phase
     *
     * @return void
     */
    private function cruisePhase(): void
    {
        $this->router = new Router(
            $this->serviceScaffold['core']['router'],
            $this->settings['router'],
            new Middleware($this->serviceScaffold['middleware']),
            $this->container
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
     *
     * @param EncounterPayload $payload
     * @return void
     */
    private function extendedOperationsPhase(EncounterPayload $payload): void
    {
        $this->router->operate($payload);
    }

    /**
     * Run the Parallel Booster Framework
     */
    public function liftOf(): void
    {
        $this->launchPhase();
        $this->cruisePhase();
        $this->extendedOperationsPhase($this->encounterPhase());
    }

    /**
     * Get the only instance of this class
     *
     * Yes, it's a singleton, the only necessary one though...
     * Any other singleton would be up to developers' decision
     * when using a service container.
     *
     * @param array $serviceScaffold
     * @param array $settings
     * @param Session $session
     * @return Kernel
     */
    static public function instance(array $serviceScaffold, array $settings, Session $session): Kernel
    {
        if (null !== self::$instance) {
            return self::$instance;
        }

        self::$instance = new Kernel($serviceScaffold, $settings, $session);
        return self::$instance;
    }
}
