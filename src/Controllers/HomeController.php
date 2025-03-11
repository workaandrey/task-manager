<?php
declare(strict_types=1);

namespace App\Controllers;

use Psr\Container\ContainerInterface;

/**
 * Home Controller
 * 
 * Handles the home page and dashboard functionality
 */
final class HomeController extends BaseController
{
    /**
     * Create a new home controller instance
     * 
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }
    
    /**
     * Display the home page/dashboard
     * 
     * @param array $currentUser Current logged in user
     * @param object $permissions User permissions
     * @return void
     */
    public function index(array $currentUser, object $permissions): void
    {
        $taskModel = $this->container->get(\App\Models\Task::class);
        $tasks = $taskModel->readAll($currentUser['id'], $permissions->isAdmin());
        
        // Get recent tasks (limit to 5)
        $recentTasks = array_slice($tasks, 0, 5);
        
        $this->render('dashboard', [
            'recentTasks' => $recentTasks,
            'currentUser' => $currentUser,
            'permissions' => $permissions
        ]);
    }
} 