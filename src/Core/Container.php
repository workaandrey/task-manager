<?php
declare(strict_types=1);

namespace App\Core;

use Psr\Container\ContainerInterface;
use Exception;
use PDO;
use App\Auth\Auth;
use App\Permissions\Permissions;
use App\Models\Task;
use App\Models\Role;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\TaskController;

class Container implements ContainerInterface
{
    private $services = [];
    private $instances = [];

    /**
     * Register a service with the container
     *
     * @param string $id Service identifier
     * @param callable|object $concrete The service factory or instance
     * @param bool $shared Whether to share the instance
     */
    public function set(string $id, $concrete, bool $shared = true): void
    {
        $this->services[$id] = [
            'concrete' => $concrete,
            'shared' => $shared,
        ];
    }

    /**
     * Find an entry of the container by its identifier and return it
     *
     * @param string $id Identifier of the entry to look for
     * @return mixed Entry
     * @throws Exception
     */
    public function get(string $id)
    {
        if (!$this->has($id)) {
            throw new Exception("Service '{$id}' not found in container");
        }

        if (isset($this->instances[$id]) && $this->services[$id]['shared']) {
            return $this->instances[$id];
        }

        $concrete = $this->services[$id]['concrete'];
        
        if (is_callable($concrete)) {
            $instance = $concrete($this);
        } else {
            $instance = $concrete;
        }
        
        if ($this->services[$id]['shared']) {
            $this->instances[$id] = $instance;
        }
        
        return $instance;
    }

    /**
     * Check if the container can return an entry for the given identifier
     *
     * @param string $id Identifier of the entry to look for
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }

    /**
     * Register all application services
     * 
     * @return void
     */
    public function registerServices(): void
    {
        // Load configuration
        Config::load();
        
        // Register the database connection
        $this->set(PDO::class, function ($container) {
            $database = new Database();
            return $database->getConnection();
        });
        
        // Register application services
        $this->set(Auth::class, function ($container) {
            return new Auth($container->get(PDO::class));
        });
        
        $this->set(Permissions::class, function ($container) {
            $auth = $container->get(Auth::class);
            $current_user = $auth->getCurrentUser();
            return new Permissions($container->get(PDO::class), $current_user);
        });
        
        // Register models
        $this->set(Task::class, function ($container) {
            return new Task($container->get(PDO::class));
        });

        $this->set(Role::class, function ($container) {
            return new Role($container->get(PDO::class));
        });
        
        // Register controllers
        $this->set(HomeController::class, function ($container) {
            return new HomeController($container);
        });
        
        $this->set(AuthController::class, function ($container) {
            return new AuthController($container);
        });
        
        $this->set(TaskController::class, function ($container) {
            return new TaskController($container);
        });

        // Register API controllers
        $this->set(App\Controllers\Api\TaskApiController::class, function ($container) {
            return new App\Controllers\Api\TaskApiController($container);
        });
    }
} 