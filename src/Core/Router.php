<?php

namespace App\Core;

class Router
{
    private $routes = [];
    private $notFoundCallback;
    
    /**
     * Add a route to the routing table
     *
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $route  The route URL
     * @param callable $callback The callback function to run
     */
    public function addRoute($method, $route, $callback)
    {
        // Convert wildcards to regex patterns
        $route = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[a-zA-Z0-9_-]+)', $route);
        $route = "#^{$route}$#";
        
        $this->routes[$method][$route] = $callback;
    }
    
    /**
     * Add a GET route
     *
     * @param string $route The route URL
     * @param callable $callback The callback to run
     */
    public function get($route, $callback)
    {
        $this->addRoute('GET', $route, $callback);
    }
    
    /**
     * Add a POST route
     *
     * @param string $route The route URL
     * @param callable $callback The callback to run
     */
    public function post($route, $callback)
    {
        $this->addRoute('POST', $route, $callback);
    }
    
    /**
     * Add a PUT route
     *
     * @param string $route The route URL
     * @param callable $callback The callback to run
     */
    public function put($route, $callback)
    {
        $this->addRoute('PUT', $route, $callback);
    }
    
    /**
     * Add a DELETE route
     *
     * @param string $route The route URL
     * @param callable $callback The callback to run
     */
    public function delete($route, $callback)
    {
        $this->addRoute('DELETE', $route, $callback);
    }
    
    /**
     * Set a callback for when no route is found
     *
     * @param callable $callback The callback function
     */
    public function setNotFound($callback)
    {
        $this->notFoundCallback = $callback;
    }
    
    /**
     * Dispatch the route to the appropriate callback
     *
     * @param string $method The HTTP method
     * @param string $uri The URI to dispatch
     */
    public function dispatch($method, $uri)
    {
        $method = strtoupper($method);
        
        if (strstr($uri, '?')) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }
        
        $uri = rtrim($uri, '/');
        
        if (empty($uri) || $uri[0] !== '/') {
            $uri = '/' . $uri;
        }
        
        if (isset($this->routes[$method])) {
            
            foreach ($this->routes[$method] as $route => $callback) {
                if (preg_match($route, $uri, $matches)) {
                    $params = array_filter($matches, function ($key) {
                        return !is_numeric($key);
                    }, ARRAY_FILTER_USE_KEY);
                    
                    return call_user_func_array($callback, $params);
                }
            }
        }
        
        if ($this->notFoundCallback) {
            return call_user_func($this->notFoundCallback);
        }
        
        header("HTTP/1.0 404 Not Found");
        echo '404 - Page not found';
    }
} 