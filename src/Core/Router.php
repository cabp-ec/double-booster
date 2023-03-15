<?php

declare(strict_types=1);

namespace App\Core;

use App\Interfaces\HttpControllerInterface;
use App\Interfaces\RouteHandlerInterface;

final class Router extends ConfigReceptor implements RouteHandlerInterface
{
    private const SEGMENT_API = 'api';
    private const SEGMENT_WEB = 'web';
    private const KEY_OPEN_CHAR = 'paramOpenChar';
    private const KEY_CLOSE_CHAR = 'paramCloseChar';
    private const ACTION_SUFFIX = 'Action';
    private const CONTROLLER_SUFFIX = 'Controller';
    private const CONTROLLER_CLASS_SIGNATURE = 'App\Http\Controllers';
    private const API_VERSIONS_AVAIL = [1, 2]; // TODO: use a .env file for this
    private const ROUTE_DEFINITION_404 = [
        'module' => 'Core',
        'method' => [
            'GET' => [
                'protected' => false,
                'controller' => 'Index',
                'action' => 'notFound',
                'middleware' => 'ROUTE',
            ],
            'POST' => [
                'protected' => false,
                'controller' => 'Index',
                'action' => 'notFound',
                'middleware' => 'ROUTE',
            ],
        ],
    ];
    public const DEFAULT_ACCEPT_HEADER = 'text/html';

    private array $routes = [];
    private array $workersFree = [];
    private array $workersOccupied = [];

    /**
     * The Router class
     *
     * @param array $routes
     * @param array $config
     * @param Middleware $middlewarePool
     * @param Container $container
     */
    public function __construct(
        array              $routes,
        private array      $config,
        private Middleware $middlewarePool,
        private Container  $container
    )
    {
        $this->configCleanUp($routes, $this->routes);
    }

    /**
     * Clean-up a single route definition
     *
     * @param array $definition
     * @return array
     */
    private function cleanUpRouteDefinition(array $definition): array
    {
        // TODO: update so as we can grab a distinguish core from module

        // 1. Find controller and method
        $signatureTpl = 'App\Http\Controllers\%sController';
        $methods = array_keys($definition['method']);

        foreach ($methods as $methodKey) {
            $methodDefinition = $definition['method'][strtoupper($methodKey)];
            $controllerSignature = sprintf($signatureTpl, $methodDefinition['controller']);
            $methodName = $methodDefinition['action'] . $this->config['actionSuffix'];

            // 2. Set to 404 if class or method are not found
            if (!class_exists($controllerSignature) || !method_exists($controllerSignature, $methodName)) {
                $definition['method'][$methodKey] = self::ROUTE_DEFINITION_404['method'][$methodKey];
            }
        }

        return $definition;
    }

    /**
     * @inheritDoc
     */
    protected function configCleanUp(array $dirty, array &$clean): void
    {
        $clean = [];

        foreach ($dirty as $group => $routeDefs) {
            if (!array_key_exists($group, $clean)) {
                $clean[$group] = [];
            }

            foreach ($routeDefs as $uri => $dirtyDef) {
                $clean[$group][$uri] = $this->cleanUpRouteDefinition($dirtyDef);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function get(string $uri, array $definition): HttpControllerInterface
    {
        // TODO: update this method so as we can grab a controller from a module
        $methodKeys = array_keys($definition['method']);
        $methodDefinition = $definition['method'][$methodKeys[0]];

        /** @var HttpControllerInterface $worker */
        $worker = null;
        $classSignature = self::CONTROLLER_CLASS_SIGNATURE . '\\' . $methodDefinition['controller'] . self::CONTROLLER_SUFFIX;

        if (count($this->workersFree) === 0) {
            try {
                $worker = (new \ReflectionClass($classSignature))->newInstanceArgs([$this->container]);
            } catch (\ReflectionException $e) {
                // TODO: catch this exception with our own error/exception handler
            }
        } else {
            $worker = array_pop($this->workersFree);
        }

        $this->workersOccupied[spl_object_hash($worker)] = $worker;

        return $worker;
    }

    /**
     * @inheritDoc
     */
    public function dispose(HttpControllerInterface $worker): void
    {
        $hash = spl_object_hash($worker);

        if (isset($this->workersOccupied[$hash])) {
            unset($this->workersOccupied[$hash]);
            $this->workersFree[$hash] = $worker;
        }
    }

    private function findMatch(
        string $gKey,
        array  $gSegments,
        array  $segments,
        array  &$gKeyCount,
        array  &$gKeyCountValues
    ): bool
    {
        $values = [];

        foreach ($gSegments as $index => $gSegment) {
            if ($segments[$index] === $gSegment) {
                $values[] = true;
            }
        }

        $gKeyCount[$gKey] = count($values);
        $gKeyCountValues[$gKey] = $gKey;

        return false;
    }

    private function extractArgs(string $uri, string $refUri, string $apiVersion): array
    {
        $segments = explode('/', $uri);
        $reference = explode('/', $refUri);
        $values = [];

        foreach ($reference as $index => $refSegment) {
            if ($refSegment === '{version}') {
                $values['version'] = $apiVersion;
                continue;
            }

            $start = str_starts_with($refSegment, '{');
            $end = str_ends_with($refSegment, '}');

            if ($start && $end) {
                $key = str_replace(['{', '}'], '', $refSegment);
//                echo $key . ' - ' . $segments[$index] . '<br>';
                $values[$key] = $segments[$index];
            }
        }

        return $values;
    }

    /**
     * @inheritDoc
     */
    public function operate(EncounterPayload $payload): HttpResponse
    {
        // TODO: separate steps into methods

        // 1. Check if it's a web or api route
        $uri = $payload->getUri();
        $segments = explode('/', $uri);
        $isApi = (isset($segments[1]) && isset($segments[2])) && $segments[1] === self::SEGMENT_API;
        $apiVersion = $isApi ? ($segments[2] ?? null) : null;
        $groupKey = $isApi ? self::SEGMENT_API : self::SEGMENT_WEB;

        // 2. Extract parameters from the rest of segments
        // TODO: transform segments into arguments
        $groupDefinitions = $this->routes[$groupKey];
        $groupKeys = array_keys($this->routes[$groupKey]);
        $groupSegments = [];

        if ($isApi) {
            $segments = array_splice($segments, 1);
            $segments[0] = '';
            $segments[1] = '{version}';
        }

        $keyUri = implode('/', $segments);
        $totalSegments = count($segments);
        $gKeyCount = [];
        $gKeyCountValues = [];

        foreach ($groupKeys as $index => $gKey) {
            $gSegments = explode('/', $gKey);

            if ($keyUri === $gKey) {
//                echo $keyUri . ' === ' . $gKey . '<br>';
                $groupSegments[$gKey] = $gSegments;
                break;
            } else if (count($gSegments) === $totalSegments) {
                $this->findMatch($gKey, $gSegments, $segments, $gKeyCount, $gKeyCountValues);
            }
        }

        $definitionKey = $keyUri;

        if (empty($groupSegments)) {
            $k = array_keys($gKeyCount);
            $v = array_values($gKeyCount);
            $x = max($v);
            $i = array_search($x, $v);
            $definitionKey = $k[$i];
        }

        $routeDefinition = $this->routes[$groupKey][$definitionKey];
        $params = array_merge_recursive(
            $this->extractArgs($keyUri, $definitionKey, $apiVersion),
            $payload->getPostedData()
        );

//        echo '<pre>';
//        var_dump(json_encode($params));
//        exit;

        // 3. Create a RequestInterface using $params
        $request = new HttpRequest(
            $uri,
            $payload->getRequestMethod(),
            json_encode($params),
            $payload->getRequestHeaders()
        );

        // 4. TODO: Run middleware pipeline

        // 5. Get the worker
        $worker = $this->get($uri, $routeDefinition);

        // 6. Call worker method (a.k.a. action) using RequestInterface as parameter
        $methodKeys = array_keys($routeDefinition['method']);
        $methodDefinition = $routeDefinition['method'][$methodKeys[0]];
        $methodName = $methodDefinition['action'] . self::ACTION_SUFFIX;

        // 7. Dispose the worker
        $this->dispose($worker);

        return $worker->$methodName($request);
    }
}
