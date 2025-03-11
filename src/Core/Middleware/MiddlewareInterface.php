<?php

namespace App\Core\Middleware;

interface MiddlewareInterface
{
    /**
     * Process the request through the middleware
     *
     * @param callable $next The next middleware or controller
     * @return callable The processed middleware
     */
    public function process(callable $next);
} 