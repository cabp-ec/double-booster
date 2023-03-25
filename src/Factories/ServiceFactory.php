<?php

declare(strict_types=1);

namespace App\Factories;

use App\Core\ErrorHandler;
use App\Core\Errors\ServiceError;
use App\Core\Interfaces\ServiceInterface;

class ServiceFactory extends BaseFactory
{
    private const SERVICES_NAMESPACE = '\App\Services\\';
    private array $lazyServices;

    /** @var ServiceInterface[] */
    private array $servicePool = [];

    /**
     * The Service Factory
     *
     * @param array $services
     * @param ErrorHandler $errorHandler
     */
    public function __construct(array $services, private readonly ErrorHandler $errorHandler)
    {
        $this->lazyServices = $services['lazy'];

        foreach ($services['start'] as $name => $args) {
            $this->get($name);
        }
    }

    /**
     * @inheritDoc
     */
    public function get(string $name): ServiceInterface
    {
        if (!str_starts_with($name, self::SERVICES_NAMESPACE)) {
            $name = self::SERVICES_NAMESPACE . $name;
        }

        if (!isset($this->servicePool[$name])) {
            $this->servicePool[$name] = $this->create($name, $this->lazyServices[$name]);
        }

        return $this->servicePool[$name];
    }

    /**
     * @inheritDoc
     */
    protected function create(string $name, array $args): ?object
    {
        $service = null;

        if (!in_array($name, $this->lazyServices)) {
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
