<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Task;
use Psr\Container\ContainerInterface;

/**
 * Task Controller
 * 
 * Handles task management functionality
 */
final class TaskController extends BaseController
{
    /**
     * Create a new task controller instance
     * 
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }
    
    /**
     * Display all tasks
     * 
     * @param array $currentUser Current logged in user
     * @param object $permissions User permissions
     * @return void
     */
    public function index(array $currentUser, object $permissions): void
    {
        $taskModel = $this->container->get(Task::class);
        $tasks = $taskModel->readAll($currentUser['id'], $permissions->isAdmin());
        
        $this->render('tasks/index', [
            'tasks' => $tasks,
            'currentUser' => $currentUser,
            'permissions' => $permissions
        ]);
    }
    
    /**
     * Show task creation form
     * 
     * @param array $currentUser Current logged in user
     * @param object $permissions User permissions
     * @return void
     */
    public function create(array $currentUser, object $permissions): void
    {
        $this->render('tasks/create', [
            'currentUser' => $currentUser,
            'permissions' => $permissions
        ]);
    }
    
    /**
     * Store a new task
     * 
     * @param array $currentUser Current logged in user
     * @param object $permissions User permissions
     * @return void
     */
    public function store(array $currentUser, object $permissions): void
    {
        $validationError = $this->validate($_POST, ['title']);
        if ($validationError) {
            $this->redirect('/tasks/create', $validationError, 'error');
            return;
        }
        
        $taskModel = $this->container->get(Task::class);
        
        $taskModel->title = $_POST['title'];
        $taskModel->description = $_POST['description'] ?? '';
        $taskModel->status = $_POST['status'] ?? 'pending';
        $taskModel->createdBy = $currentUser['id'];
        $taskModel->assignedTo = !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null;
        
        $result = $taskModel->create();
        
        if ($result) {
            $this->redirect('/tasks', 'Task created successfully');
        } else {
            $this->redirect('/tasks/create', 'Failed to create task', 'error');
        }
    }
    
    /**
     * Show task edit form
     * 
     * @param array $currentUser Current logged in user
     * @param object $permissions User permissions
     * @param int $id Task ID
     * @return void
     */
    public function edit(array $currentUser, object $permissions, int $id): void
    {
        if (!$permissions->canEditTask($id)) {
            $this->redirect('/tasks', 'You don\'t have permission to edit this task', 'error');
            return;
        }
        
        $taskModel = $this->container->get(Task::class);
        $task = $taskModel->read($id);
        
        if (!$task) {
            $this->redirect('/tasks', 'Task not found', 'error');
            return;
        }
        
        $this->render('tasks/edit', [
            'task' => $task,
            'currentUser' => $currentUser,
            'permissions' => $permissions
        ]);
    }
    
    /**
     * Update an existing task
     * 
     * @param array $currentUser Current logged in user
     * @param object $permissions User permissions
     * @return void
     */
    public function update(array $currentUser, object $permissions, int $id): void
    {        
        if (!$permissions->canEditTask($id)) {
            $this->redirect('/tasks', 'You don\'t have permission to edit this task', 'error');
            return;
        }
        
        $validationError = $this->validate($_POST, ['title']);
        if ($validationError) {
            $this->redirect("/tasks/{$id}/edit", $validationError, 'error');
            return;
        }
        
        $taskModel = $this->container->get(Task::class);
        
        $taskModel->id = $id;
        $taskModel->title = $_POST['title'];
        $taskModel->description = $_POST['description'] ?? '';
        $taskModel->status = $_POST['status'] ?? 'pending';
        $taskModel->assignedTo = !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null;
        
        $result = $taskModel->update();
        
        if ($result) {
            $this->redirect('/tasks', 'Task updated successfully');
        } else {
            $this->redirect("/tasks/{$id}/edit", 'Failed to update task', 'error');
        }
    }
} 