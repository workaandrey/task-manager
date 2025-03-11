<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use App\Models\Task;
use App\Auth\Auth;
use App\Permissions\Permissions;
use App\Controllers\BaseController;
use Psr\Container\ContainerInterface;

/**
 * Task API Controller
 * 
 * Handles REST API endpoints for tasks
 */
final class TaskApiController extends BaseController
{
    /**
     * Create a new TaskApiController instance
     * 
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    /**
     * Get all tasks (with permission filtering)
     * 
     * @param array $currentUser Current authenticated user
     * @param Permissions $permissions User permissions
     * @return void
     */
    public function index(array $currentUser, Permissions $permissions): void
    {
        header('Content-Type: application/json');
        
        try {
            $taskModel = $this->container->get(Task::class);
            
            // Get tasks filtered by user permissions
            $tasks = $taskModel->readAll($currentUser['id'], $permissions->isAdmin());
            
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
    }

    /**
     * Get a specific task by ID
     * 
     * @param array $currentUser Current authenticated user
     * @param Permissions $permissions User permissions
     * @param int $id Task ID
     * @return void
     */
    public function show(array $currentUser, Permissions $permissions, int $id): void
    {
        header('Content-Type: application/json');
        
        if (!$permissions->canViewTask($id)) {
            http_response_code(403);
            echo json_encode([
                'status' => 'error',
                'message' => 'You do not have permission to view this task'
            ]);
            return;
        }
        
        try {
            $taskModel = $this->container->get(Task::class);
            $task = $taskModel->read($id);
            
            if (!$task) {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Task not found'
                ]);
                return;
            }
            
            echo json_encode([
                'status' => 'success',
                'data' => $task
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to fetch task',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Create a new task
     * 
     * @param array $currentUser Current authenticated user
     * @param Permissions $permissions User permissions
     * @return void
     */
    public function store(array $currentUser, Permissions $permissions): void
    {
        header('Content-Type: application/json');
        
        if (!$permissions->canCreateTask()) {
            http_response_code(403);
            echo json_encode([
                'status' => 'error',
                'message' => 'You do not have permission to create tasks'
            ]);
            return;
        }
        
        try {
            // Get JSON input
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['title']) || empty($data['title'])) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Title is required'
                ]);
                return;
            }
            
            $taskModel = $this->container->get(Task::class);
            
            $taskModel->title = $data['title'];
            $taskModel->description = $data['description'] ?? '';
            $taskModel->status = $data['status'] ?? 'pending';
            $taskModel->createdBy = $currentUser['id'];
            $taskModel->assignedTo = isset($data['assigned_to']) ? (int)$data['assigned_to'] : null;
            
            $result = $taskModel->create();
            
            if (!$result) {
                http_response_code(500);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to create task'
                ]);
                return;
            }
            
            $newTask = $taskModel->read((int)$result);
            
            http_response_code(201); // Created
            echo json_encode([
                'status' => 'success',
                'message' => 'Task created successfully',
                'data' => $newTask
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to create task',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update an existing task
     * 
     * @param array $currentUser Current authenticated user
     * @param Permissions $permissions User permissions
     * @param int $id Task ID
     * @return void
     */
    public function update(array $currentUser, Permissions $permissions, int $id): void
    {
        header('Content-Type: application/json');
        
        if (!$permissions->canEditTask($id)) {
            http_response_code(403);
            echo json_encode([
                'status' => 'error',
                'message' => 'You do not have permission to edit this task'
            ]);
            return;
        }
        
        try {
            // Get JSON input
            $data = json_decode(file_get_contents('php://input'), true);
            
            $taskModel = $this->container->get(Task::class);
            
            // Get existing task
            $task = $taskModel->read($id);
            if (!$task) {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error', 
                    'message' => 'Task not found'
                ]);
                return;
            }
            
            // Update task properties
            $taskModel->id = $id;
            $taskModel->title = $data['title'] ?? $task['title'];
            $taskModel->description = $data['description'] ?? $task['description'];
            $taskModel->status = $data['status'] ?? $task['status'];
            $taskModel->assignedTo = $data['assigned_to'] ?? $task['assigned_to'];
            
            $success = $taskModel->update();
            
            if (!$success) {
                http_response_code(500);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to update task'
                ]);
                return;
            }
            
            // Get the updated task
            $updatedTask = $taskModel->read($id);
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Task updated successfully',
                'data' => $updatedTask
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to update task',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Delete a task
     * 
     * @param array $currentUser Current authenticated user
     * @param Permissions $permissions User permissions
     * @param int $id Task ID
     * @return void
     */
    public function destroy(array $currentUser, Permissions $permissions, int $id): void
    {
        header('Content-Type: application/json');
        
        if (!$permissions->canDeleteTask($id)) {
            http_response_code(403);
            echo json_encode([
                'status' => 'error',
                'message' => 'You do not have permission to delete this task'
            ]);
            return;
        }
        
        try {
            $taskModel = $this->container->get(Task::class);
            
            // Check if task exists
            $task = $taskModel->read($id);
            if (!$task) {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Task not found'
                ]);
                return;
            }
            
            $success = $taskModel->delete($id);
            
            if (!$success) {
                http_response_code(500);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to delete task'
                ]);
                return;
            }
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Task deleted successfully'
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to delete task',
                'error' => $e->getMessage()
            ]);
        }
    }
} 