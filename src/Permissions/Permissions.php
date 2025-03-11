<?php

namespace App\Permissions;

use PDO;
use PDOException;

class Permissions 
{
    private $conn;
    private $user;
    
    public function __construct($db, $user) 
    {
        $this->conn = $db;
        $this->user = $user;
    }
    
    public function isAdmin(): bool
    {
        if (!$this->user) {
            return false;
        }
        
        // Get role name based on role_id
        $query = "SELECT r.name FROM roles r 
                  JOIN users u ON r.id = u.role_id 
                  WHERE u.id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->user['id']]);
        $role = $stmt->fetchColumn();
        
        return $role === 'admin';
    }
    
    public function canCreateTask() 
    {
        return true;
    }
    
    public function canViewTask($task_id) 
    {
        if ($this->isAdmin()) {
            return true;
        }
        
        try {
            $query = "SELECT id FROM tasks 
                      WHERE id = ? AND (created_by = ? OR assigned_to = ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$task_id, $this->user['id'], $this->user['id']]);
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function canEditTask($task_id) 
    {
        try {
            $query = "SELECT id FROM tasks WHERE id = ? AND created_by = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$task_id, $this->user['id']]);
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function canDeleteTask($task_id) 
    {
        // Only admins can delete tasks
        return $this->isAdmin();
    }
} 