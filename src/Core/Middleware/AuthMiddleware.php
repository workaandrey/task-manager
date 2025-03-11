<?php
declare(strict_types=1);

namespace App\Core\Middleware;

use App\Auth\Auth;
use App\Permissions\Permissions;
use Psr\Container\ContainerInterface;

/**
 * Authentication middleware
 * 
 * Verifies that the user is authenticated before processing the request
 */
final class AuthMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface Dependency container
     */
    private ContainerInterface $container;
    
    /**
     * @var string Redirect URL for unauthenticated users
     */
    private string $redirect;

    /**
     * Create a new auth middleware instance
     * 
     * @param ContainerInterface $container The dependency container
     * @param string $redirect URL to redirect unauthenticated users
     */
    public function __construct(ContainerInterface $container, string $redirect = '/login')
    {
        $this->container = $container;
        $this->redirect = $redirect;
    }

    /**
     * Process the request through the middleware
     * 
     * Verifies authentication status before passing request to next handler
     * 
     * @param callable $next The next middleware or controller
     * @return mixed The result of the next handler
     */
    public function process(callable $next): mixed
    {
        // Get the authentication service
        $auth = $this->container->get(Auth::class);
        
        // Check if user is logged in
        if (!$auth->isLoggedIn()) {
            header('Location: ' . $this->redirect);
            exit;
        }
        
        // Get current user and permissions
        $currentUser = $auth->getCurrentUser();
        $permissions = $this->container->get(Permissions::class);
        
        // Additional check for null user (shouldn't happen but safety first)
        if ($currentUser === null) {
            header('Location: ' . $this->redirect);
            exit;
        }
        
        // If the next handler is an array (controller and method)
        if (is_array($next) && count($next) === 2) {
            $controller = $next[0];
            $method = $next[1];
            
            // Check if controller is a string that needs to be instantiated
            if (is_string($controller) && class_exists($controller)) {
                $controller = $this->container->get($controller);
            }
            
            // Execute the controller method with auth data as first params
            return call_user_func([$controller, $method], $currentUser, $permissions);
        }
        
        // If next is a direct callable function
        return call_user_func($next, $currentUser, $permissions);
    }
} 