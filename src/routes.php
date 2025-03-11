<?php

use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\TaskController;
use App\Controllers\Api\TaskApiController;
use App\Core\Middleware\AuthMiddleware;
use App\Core\Auth\Auth;
use App\Core\Auth\Permissions;

return function ($router, $container) {
    $authMiddleware = new AuthMiddleware($container);
    
    $router->get('/', function () use ($container, $authMiddleware) {
        $controller = $container->get(HomeController::class);
        return $authMiddleware->process([$controller, 'index']);
    });
    
    $router->get('/login', function () use ($container) {
        $controller = $container->get(AuthController::class);
        return call_user_func([$controller, 'showLogin']);
    });
    
    $router->post('/login', function () use ($container) {
        $controller = $container->get(AuthController::class);
        return call_user_func([$controller, 'login']);
    });
    
    $router->get('/register', function () use ($container) {
        $controller = $container->get(AuthController::class);
        return call_user_func([$controller, 'showRegister']);
    });
    
    $router->post('/register', function () use ($container) {
        $controller = $container->get(AuthController::class);
        return call_user_func([$controller, 'register']);
    });
    
    $router->get('/logout', function () use ($container) {
        $controller = $container->get(AuthController::class);
        return call_user_func([$controller, 'logout']);
    });
    
    $router->get('/tasks', function () use ($container, $authMiddleware) {
        $controller = $container->get(TaskController::class);
        return $authMiddleware->process([$controller, 'index']);
    });
    
    $router->get('/tasks/create', function () use ($container, $authMiddleware) {
        $controller = $container->get(TaskController::class);
        return $authMiddleware->process([$controller, 'create']);
    });
    
    $router->post('/tasks/create', function () use ($container, $authMiddleware) {
        $controller = $container->get(TaskController::class);
        return $authMiddleware->process([$controller, 'store']);
    });
    
    $router->get('/tasks/{id}/edit', function ($id) use ($container, $authMiddleware) {
        $controller = $container->get(TaskController::class);
        return $authMiddleware->process(function ($currentUser, $permissions) use ($controller, $id) {
            return $controller->edit($currentUser, $permissions, (int)$id);
        });
    });
    
    $router->post('/tasks/{id}/edit', function ($id) use ($container, $authMiddleware) {
        $controller = $container->get(TaskController::class);
        return $authMiddleware->process(function ($currentUser, $permissions) use ($controller, $id) {
            return $controller->update($currentUser, $permissions, (int)$id);
        });
    });
    
    $router->get('/api/tasks', function () use ($container) {
        header('Content-Type: application/json');
        
        try {
            $taskModel = $container->get(App\Models\Task::class);
            
            $tasks = $taskModel->readAll(null, true);
            
            echo json_encode([
                'status' => 'success',
                'data' => $tasks,
                'count' => count($tasks)
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to fetch tasks',
                'error' => $e->getMessage()
            ]);
        }
        
        return;
    });
    
    $router->setNotFound(function () use ($container) {
        if (!isset($_SERVER['HTTP_ACCEPT']) || strpos($_SERVER['HTTP_ACCEPT'], 'application/json') === false) {
            header("HTTP/1.0 404 Not Found");
            include_once '../templates/header.php';
            include_once '../templates/404.php';
            include_once '../templates/footer.php';
            return;
        }
        
        header("HTTP/1.0 404 Not Found");
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => 'Endpoint not found'
        ]);
    });
}; 