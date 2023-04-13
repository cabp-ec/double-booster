<?php

declare(strict_types=1);

namespace App\Factories;

use App\Core\ErrorHandler;
use App\Core\Errors\ServiceError;
use App\Core\Interfaces\ServiceInterface;

class ServiceFactory extends BaseFactory
{
    private const SERVICES_NAMESPACE = '\App\Services\\';
//    private array $lazyServices;

    /** @var ServiceInterface[] */
    private array $servicePool = [];

    private array $serviceDefinitions = [];

    /**
     * The Service Factory
     *
     * @param array $services
     * @param ErrorHandler $errorHandler
     */
    public function __construct(array $services, private readonly ErrorHandler $errorHandler)
    {
        $this->serviceDefinitions = $services;

        foreach ($services['start'] as $name => $args) {
            $this->get($name);
        }
    }

    private function getServiceParams(string $name): array
    {
        if (in_array($name, array_keys($this->serviceDefinitions['start']))) {
            return $this->serviceDefinitions['start'][$name];
        }

        if (in_array($name, array_keys($this->serviceDefinitions['lazy']))) {
            return $this->serviceDefinitions['lazy'][$name];
        }

        return [];
    }

    private function isServiceRegistered(string $name): bool
    {
        if (in_array($name, array_keys($this->serviceDefinitions['start']))) {
            return true;
        }

        if (in_array($name, array_keys($this->serviceDefinitions['lazy']))) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function get(string $name): ?ServiceInterface
    {
        $name = $name . 'Service';

        if (!str_starts_with($name, self::SERVICES_NAMESPACE)) {
            $name = self::SERVICES_NAMESPACE . $name;
        }

        if (!isset($this->servicePool[$name])) {
            $this->servicePool[$name] = $this->create($name, $this->getServiceParams($name));
        }

        return $this->servicePool[$name];
    }

    /**
     * @inheritDoc
     */
    protected function create(string $name, array $args): ?object
    {
        $service = null;

        if (!$this->isServiceRegistered($name)) {
            // TODO: throw an exception here
            return null;
        }

        try {
            $service = (new \ReflectionClass($name))->newInstanceArgs($args);
        } catch (\ReflectionException $e) {
            throw new ServiceError($e->getMessage(), $e->getCode());
        } catch (ServiceError $e) {
            $this->errorHandler->catch($e);
        } finally {
            return $service;
        }
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->servicePool);
    }
}
