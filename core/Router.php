<?php

class Route {
    private static $routes = [];
    private static $currentGroup = '';

    // Group routes under a prefix
    public static function group($prefix, $callback)
    {
        $previousGroup = self::$currentGroup;
        self::$currentGroup = $previousGroup . $prefix; // stackable
        $callback();
        self::$currentGroup = $previousGroup; // reset after group
    }

    // Register GET route
    public static function get($uri, $action) {
        $uri = self::$currentGroup . $uri; // prepend group prefix if any
        self::$routes['GET'][$uri] = $action;
    }

    // Register POST route
    public static function post($uri, $action) {
        $uri = self::$currentGroup . $uri; // prepend group prefix if any
        self::$routes['POST'][$uri] = $action;
    }

    // Register a route for multiple HTTP methods
    public static function match(array $methods, $uri, $action)
    {
        $uri = self::$currentGroup . $uri;
        foreach ($methods as $method) {
            $method = strtoupper($method);
            self::$routes[$method][$uri] = $action;
        }
    }

    // Match current request and dispatch
    public static function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Remove base folder (/fyp_cherating if any)
        $base = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        $uri = '/' . trim(str_replace($base, '', $uri), '/');

        $routes = self::$routes[$method] ?? [];

        try {
            foreach ($routes as $route => $action) {
                $pattern = preg_replace('#\{[a-zA-Z0-9_]+\}#', '([a-zA-Z0-9_-]+)', $route);
                $pattern = "#^" . rtrim($pattern, '/') . "/?$#";

                if (preg_match($pattern, $uri, $matches)) {
                    array_shift($matches); // remove full match

                    if (is_callable($action)) {
                        return call_user_func_array($action, $matches);
                    }

                    if (is_string($action)) {
                        list($controllerName, $methodName) = explode('@', $action);
                        $controllerName = ucfirst($controllerName) . "Controller";

                        if (class_exists($controllerName)) {
                            $controller = new $controllerName();
                            if (method_exists($controller, $methodName)) {
                                return call_user_func_array([$controller, $methodName], $matches);
                            }
                        }
                    }
                }
            }

            // No route matched → forward to ErrorController@notFound
            (new ErrorController())->notFound($uri);

        } catch (Throwable $e) {
            // Any unhandled error → forward to ErrorController@serverError
            (new ErrorController())->serverError($e);
        }
    }
}