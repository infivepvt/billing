<?php

class Route {
    private static $routes = [];

    public static function add($uri, $callback) {
        $uri = trim($uri, '/');
        self::$routes[$uri] = $callback;
    }

    public static function submit() {
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $base_path = 'project'; // Adjust base path
        //print $uri.'<br>'.$base_path.'<br>';
        if (strpos($uri, $base_path) === 0) {
            $uri = substr($uri, strlen($base_path));
            $uri = trim($uri, '/');
        }

        foreach (self::$routes as $route => $callback) {
            $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_]+)', $route);
            if (preg_match('#^' . $pattern . '$#', $uri, $matches)) {
                array_shift($matches);
                return call_user_func_array($callback, $matches);
            }
        }

        http_response_code(404);
        echo '404 Not Found';
    }
}
