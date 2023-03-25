<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Interfaces\HttpControllerInterface;
use App\Core\Interfaces\HttpResponseAdapterInterface;
use App\Core\Traits\ContentTypeNegotiationTrait;
use App\Factories\ControllerFactory;
use Laminas\Diactoros\ServerRequestFactory;

final class Router extends ConfigReceptor
{
    use ContentTypeNegotiationTrait;

    private const ACTION_SUFFIX = 'actionSuffix';
    private const KEY_REST_ACTIONS = 'restActions';
    private const VERSION_URI = '/{version}';
    private const HTTP_METHOD_GET = 'GET';
    private const HTTP_METHOD_POST = 'POST';
    private const HTTP_METHOD_PUT = 'PUT';
    private const HTTP_METHOD_DELETE = 'DELETE';
    private const REST_OP_C = 'create';
    private const REST_OP_R = 'read';
    private const REST_OP_U = 'update';
    private const REST_OP_D = 'delete';
    private const REST_OP_A = 'all';
    private const REST_OP_F = 'filter';
    private const CONTROLLER_SIGNATURE_BASE = 'App\Http\Controllers\\';
    private const CONTROLLER_SIGNATURE_SUFFIX = '\%sController';
    private const DEFAULT_CONTROLLER_CLASS = 'App\Http\Controllers\DefaultController';
    private const NOT_FOUND_ACTION = 'notFoundAction';

    private const REST_OPS_METHODS = [
        self::REST_OP_C => self::HTTP_METHOD_POST,
        self::REST_OP_R => self::HTTP_METHOD_GET,
        self::REST_OP_U => self::HTTP_METHOD_PUT,
        self::REST_OP_D => self::HTTP_METHOD_DELETE,
        self::REST_OP_A => self::HTTP_METHOD_GET,
        self::REST_OP_F => self::HTTP_METHOD_POST,
    ];

    private const ROUTE_DEFINITION_404 = [
        'protected' => false,
        'controller' => 'Index',
        'action' => 'notFound',
        'pipeline' => 'ROUTE',
    ];
    public const ERROR_DETAIL_404 = [
        'title' => 'Not Found',
        'caption' => null,
        'message' => 'The resource you\'re looking for does not exist',
    ];

    public const CONTENT_TYPE = 'Content-Type';
    public const CONTENT_TYPE_HTML = 'text/html';
    public const CONTENT_TYPE_XML = 'application/xhtml+xml';
    public const CONTENT_TYPE_JSON = 'application/json';

    public const SEGMENT_WEB = 'web';
    public const SEGMENT_REST = 'rest';
    public const SEGMENT_RPC = 'rpc';

    public const SEGMENT_WEB_CONTENT_TYPE = self::CONTENT_TYPE_HTML;
    public const SEGMENT_REST_CONTENT_TYPE = self::CONTENT_TYPE_JSON;
    public const SEGMENT_RPC_CONTENT_TYPE = self::CONTENT_TYPE_JSON;

    public const SEGMENTS_CONTENT_TYPES = [
        Router::SEGMENT_WEB => Router::SEGMENT_WEB_CONTENT_TYPE,
        Router::SEGMENT_REST => Router::SEGMENT_REST_CONTENT_TYPE,
        Router::SEGMENT_RPC => Router::SEGMENT_RPC_CONTENT_TYPE,
    ];

    public const SEGMENTS_CONTENT_NEGOTIABLE = [
        Router::SEGMENT_WEB => false,
        Router::SEGMENT_REST => false,
        Router::SEGMENT_RPC => false,
    ];

    public const ACCEPTABLE_CONTENT_TYPES = [
        'text/html',
        'application/xhtml+xml',
        'application/xml',
        self::CONTENT_TYPE_JSON,
    ];

    public const SEGMENT_API = 'api';
    public const DEFAULT_ACCEPT_HEADER = 'text/html';

    private array $config;
    private array $routes = [];
    private MiddlewareHandler $middlewareHandler;

    /**
     * Constructor for the Router class
     *
     * @param array $config
     * @param array $routes
     * @param array $middlewarePipelines
     * @param ControllerFactory $controllerFactory
     */
    public function __construct(
        array                              $config,
        array                              $routes,
        array                              $middlewarePipelines,
        private readonly ControllerFactory $controllerFactory
    )
    {
        $this->config = $config;
        $this->configCleanUp($routes, $this->routes);
        $this->middlewareHandler = new MiddlewareHandler($middlewarePipelines);
    }

    /**
     * Clean-up a default route definition
     *
     * @param string $segment
     * @param array $definitions
     * @return array
     */
    private function cleanUpDefaultRouteDefinition(string $segment, array $definitions): array
    {
        return $definitions;
    }

    /**
     * Clean-up a REST route definition
     *
     * @param string $segment
     * @param string $uri
     * @param array $definition
     * @param array $clean
     * @return void
     */
    private function cleanUpRestRouteDefinition(string $segment, string $uri, array $definition, array &$clean): void
    {
        // 1. Find controller and method
        $signatureTpl = self::CONTROLLER_SIGNATURE_BASE . $segment . self::CONTROLLER_SIGNATURE_SUFFIX;

        if ($uri === self::VERSION_URI) {
            $clean[$uri] = $definition;
        } else {
            $controllerName = ucfirst(strtolower($definition['controller']));
            $controllerSignature = sprintf($signatureTpl, $controllerName);
            $operationActions = [
                self::REST_OP_C => $this->config[self::KEY_REST_ACTIONS]['C'] . $this->config[self::ACTION_SUFFIX],
//                self::REST_OP_R => $this->config[self::KEY_REST_ACTIONS]['R'] . $this->config[self::ACTION_SUFFIX],
                self::REST_OP_U => $this->config[self::KEY_REST_ACTIONS]['U'] . $this->config[self::ACTION_SUFFIX],
                self::REST_OP_D => $this->config[self::KEY_REST_ACTIONS]['D'] . $this->config[self::ACTION_SUFFIX],
                self::REST_OP_A => $this->config[self::KEY_REST_ACTIONS]['A'] . $this->config[self::ACTION_SUFFIX],
                self::REST_OP_F => $this->config[self::KEY_REST_ACTIONS]['F'] . $this->config[self::ACTION_SUFFIX],
            ];

            $definition['action'] = $operationActions[self::REST_OP_C];
            $clean[$uri] = [self::HTTP_METHOD_GET => $definition];

            foreach ($operationActions as $opKeyword => $operationAction) {
                $key = "$uri/$opKeyword";
                $operationMethod = self::REST_OPS_METHODS[$opKeyword];
                $actionMethod = ucfirst(strtolower($operationAction)) . $this->config[self::ACTION_SUFFIX];

                // 2. Set to 404 if class or method is not found
                if (!class_exists($controllerSignature) || !method_exists($controllerSignature, $actionMethod)) {
                    $definition = self::ROUTE_DEFINITION_404;
                } else {
                    $definition['action'] = $actionMethod;
                }

                $clean[$key] = [$operationMethod => $definition];
            }
        }
    }

    /**
     * Clean-up all routes
     *
     * @param string $key
     * @param array $dirty
     * @return array
     */
    private function routesCleanUp(string $key, array $dirty): array
    {
        $clean = [];
        $segment = ucfirst($key);

        foreach ($dirty as $uri => $dirtyDef) {
            if ($key === self::SEGMENT_REST) {
                $this->cleanUpRestRouteDefinition($segment, $uri, $dirtyDef, $clean);
            } else {
                $clean[$uri] = $this->cleanUpDefaultRouteDefinition($segment, $dirtyDef);
            }
        }

        return $clean;
    }

    /**
     * @inheritDoc
     */
    protected function configCleanUp(array $dirty, array &$clean): void
    {
        $clean = [
            self::SEGMENT_WEB => $this->routesCleanUp(self::SEGMENT_WEB, $dirty[self::SEGMENT_WEB]),
            self::SEGMENT_REST => $this->routesCleanUp(self::SEGMENT_REST, $dirty[self::SEGMENT_REST]),
            self::SEGMENT_RPC => $this->routesCleanUp(self::SEGMENT_RPC, $dirty[self::SEGMENT_RPC]),
        ];
    }

    /**
     * Find the route definition for the request
     *
     * @param string $groupKey
     * @param string $uri
     * @param bool $routeFound
     * @return array
     */
    private function findRouteDefinition(string $groupKey, string $uri, bool &$routeFound): array
    {
        $uriKeys = array_keys($this->routes[$groupKey]);
        $groupKeyFirst = substr($groupKey, 0, 1);

        if (in_array($uri, $uriKeys)) { // found exact match
            return $this->routes[$groupKey][$uri];
        }

        $segments = explode('/', strtolower($uri));
        $apiSegment = '/' . self::SEGMENT_API . '/';
        $apiStart = $apiSegment . $groupKeyFirst . '/';
        $uriRest = $uri;
        $isApi = str_starts_with($uri, $apiSegment);

        if ($isApi) {
            if (is_numeric($segments[3])) {
                $uriRest = substr($uri, strlen($apiStart) - 1);
            } else {
                $routeFound = false;
                return self::ROUTE_DEFINITION_404;
                // 404, segments[2] MUST be numeric because it's the api {version}
            }
        }

        $matchPool = [];

        foreach ($uriKeys as $index => $uriKey) {
            $uriKey = str_replace('{version}', '_API_VERSION_', $uriKey);
            $paramPattern = ['/_API_VERSION_/', '/\\\{(.*?)\}/'];
            $paramReplacement = ['(\d+)', '([a-zA-Z0-9\-\_]+)'];
            $regexKeyUri = '@^' . preg_replace($paramPattern, $paramReplacement, preg_quote($uriKey)) . '$@D';
            preg_match($regexKeyUri, $uriRest, $matches);
            array_shift($matches);

            if (empty($matches)) {
                continue;
            }

            $matchPool[] = $uriKeys[$index];
        }

        if (empty($matchPool)) {
            $routeFound = false;
            return self::ROUTE_DEFINITION_404;
        }

        $firstMatch = $this->routes[$groupKey][$matchPool[0]];
        $firstMatchKeys = array_keys($firstMatch);
        $definitionDetail = $firstMatch[$firstMatchKeys[0]];

        return array_merge_recursive(
            [
                'uriRest' => $uriRest,
                'uriDefinition' => $matchPool[0],
                'groupKey' => $groupKey,
            ],
            $definitionDetail
        );
    }

    /**
     * Get the query string as params
     *
     * @param string $uriDefinition
     * @param string $uriRest
     * @return array
     */
    private function getUriParams(string $uriDefinition, string $uriRest): array
    {
        $defSegments = explode('/', $uriDefinition);
        $restSegments = explode('/', $uriRest);
        $params = [];

        foreach ($defSegments as $index => $defSegment) {
            if (str_starts_with($defSegment, '{') && str_ends_with($defSegment, '}')) {
                $defSegment = str_replace(['{', '}'], '', $defSegment);
                $params[$defSegment] = $restSegments[$index];
            }
        }

        return $params;
    }

    /**
     * Get the proper controller to handle the request
     *
     * @param string $groupKey
     * @param array $routeDefinition
     * @return HttpControllerInterface
     */
    private function getController(string $groupKey, array $routeDefinition): HttpControllerInterface
    {
        $segment = ucfirst($groupKey) . '\\';
        $className = self::CONTROLLER_SIGNATURE_BASE . $segment . $routeDefinition['controller'] . 'Controller';

        return $this->controllerFactory->get($className);
    }

    /**
     * Get the proper action name for the given controller
     *
     * @param HttpControllerInterface $controller
     * @param string $actionName
     * @param bool $routeFound
     * @return string
     */
    private function getControllerAction(HttpControllerInterface $controller, string $actionName, bool $routeFound): string
    {
        if (!$routeFound) {
            return self::NOT_FOUND_ACTION;
        }

        $action = $actionName . 'Action';

        if (method_exists($controller, $action)) {
            return $action;
        }

        return self::NOT_FOUND_ACTION;
    }

    /**
     * Operate a route with the given payload
     *
     * @param EncounterPayload $payload
     * @return HttpResponseAdapterInterface
     */
    public function operate(EncounterPayload $payload): HttpResponseAdapterInterface
    {
        $uri = $payload->getUri();
        $groupKey = $this->getRouteKeySegment($uri);
        $routeFound = true;
        $routeDefinition = $this->findRouteDefinition($groupKey, $uri, $routeFound);
        $arguments = array_merge(
            ['keySegment' => $groupKey],
            $routeFound
                ? $this->getUriParams($routeDefinition['uriDefinition'], $routeDefinition['uriRest'])
                : []
        );
        $serverRequest = ServerRequestFactory::fromGlobals([], $arguments, $payload->getPostedData(), [], []);
        $controller = $this->getController($groupKey, $routeDefinition);
        $controllerAction = $this->getControllerAction($controller, $routeDefinition['action'], $routeFound);
        $response = $this->middlewareHandler->run($routeDefinition['pipeline'], $serverRequest);

//        echo '<pre>';
//        echo 'Router<br>';
//        var_dump($controllerAction);
//        var_dump($controller);
//        exit;

        return $controller->$controllerAction($serverRequest, $response);
    }
}
