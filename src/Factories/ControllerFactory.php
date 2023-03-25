<?php

declare(strict_types=1);

namespace App\Factories;

use App\Core\ErrorHandler;
use App\Core\Errors\AppException;
use App\Core\Interfaces\ContainerInterface;
use App\Core\Interfaces\HttpControllerInterface;
use App\Http\Controllers\DefaultController;

class ControllerFactory extends BaseFactory
{
    /** @var HttpControllerInterface[] */
    private array $workers = [];
    private ErrorHandler $errorHandler;

    /**
     * Controller Factory
     *
     * @param ContainerInterface $container
     */
    public function __construct(private readonly ContainerInterface $container)
    {
        $this->errorHandler = $this->container->getErrorHandler();
    }

    /**
     * @inheritDoc
     */
    public function get(string $name): HttpControllerInterface
    {
        if (!isset($this->workers[$name])) {
            $this->workers[$name] = $this->create($name, [$this->container]);
        }

        return $this->workers[$name];
    }

    /**
     * @inheritDoc
     */
    protected function create(string $name, array $args): ?object
    {
        try {
            $worker = (new \ReflectionClass($name))->newInstanceArgs($args);
        }
        catch (\ReflectionException $e) {
            $worker = new DefaultController($this->container);
            $this->errorHandler->catch(new AppException($e->getMessage(), $e->getCode()));
        }
        finally {
            return $worker;
        }
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->workers);
    }
}
