<?php
declare(strict_types=1);

namespace App\Models;

use PDO;
use PDOException;

/**
 * Task model class
 * 
 * Handles database operations for tasks
 */
final class Task 
{
    /**
     * @var PDO Database connection
     */
    private PDO $conn;
    
    /**
     * @var string Database table name
     */
    private string $tableName = "tasks";
    
    /**
     * @var int|null Task ID
     */
    public ?int $id = null;
    
    /**
     * @var string|null Task title
     */
    public ?string $title = null;
    
    /**
     * @var string|null Task description
     */
    public ?string $description = null;
    
    /**
     * @var string|null Task status
     */
    public ?string $status = null;
    
    /**
     * @var int|null User ID who created the task
     */
    public ?int $createdBy = null;
    
    /**
     * @var int|null User ID the task is assigned to
     */
    public ?int $assignedTo = null;
    
    /**
     * @var string|null Task creation date
     */
    public ?string $createdAt = null;
    
    /**
     * @var string|null Task last update date
     */
    public ?string $updatedAt = null;
    
    /**
     * Constructor
     * 
     * @param PDO $db Database connection
     */
    public function __construct(PDO $db) 
    {
        $this->conn = $db;
    }
    
    /**
     * Create a new task
     * 
     * @return int|false The newly created task ID or false on failure
     */
    public function create() 
    {
        try {
            $query = "INSERT INTO " . $this->tableName . " 
                      (title, description, status, created_by, assigned_to) 
                      VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($query);
            
            // Clean data
            $this->title = htmlspecialchars(strip_tags($this->title ?? ''));
            $this->description = htmlspecialchars(strip_tags($this->description ?? ''));
            $this->status = htmlspecialchars(strip_tags($this->status ?? ''));
            
            // Execute query
            $stmt->execute([
                $this->title,
                $this->description,
                $this->status,
                $this->createdBy,
                $this->assignedTo
            ]);
            
            return (int)$this->conn->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Read a single task
     * 
     * @param int $id Task ID
     * @return array|false Task data or false on failure
     */
    public function read(int $id) 
    {
        try {
            $query = "SELECT t.*, 
                      c.username as creator_name, 
                      a.username as assignee_name 
                      FROM " . $this->tableName . " t
                      JOIN users c ON t.created_by = c.id 
                      LEFT JOIN users a ON t.assigned_to = a.id 
                      WHERE t.id = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Update an existing task
     * 
     * @return bool Success status
     */
    public function update(): bool
    {
        try {
            $query = "UPDATE " . $this->tableName . " SET
                      title = ?,
                      description = ?,
                      status = ?,
                      assigned_to = ?
                      WHERE id = ?";
            
            $stmt = $this->conn->prepare($query);
            
            // Clean data
            $this->title = htmlspecialchars(strip_tags($this->title ?? ''));
            $this->description = htmlspecialchars(strip_tags($this->description ?? ''));
            $this->status = htmlspecialchars(strip_tags($this->status ?? ''));
            
            // Execute query
            return $stmt->execute([
                $this->title,
                $this->description,
                $this->status,
                $this->assignedTo,
                $this->id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Delete a task
     * 
     * @param int $id Task ID
     * @return bool Success status
     */
    public function delete(int $id): bool
    {
        try {
            $query = "DELETE FROM " . $this->tableName . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Read all tasks, optionally filtered by user permissions
     * 
     * @param int|null $userId User ID to filter tasks by
     * @param bool $isAdmin Whether the user is an admin
     * @return array List of tasks
     */
    public function readAll(?int $userId = null, bool $isAdmin = false): array
    {
        try {
            $query = "SELECT t.*, 
                      c.username as creator_name, 
                      a.username as assignee_name 
                      FROM " . $this->tableName . " t
                      JOIN users c ON t.created_by = c.id 
                      LEFT JOIN users a ON t.assigned_to = a.id ";
            
            // If not admin, limit to tasks created by or assigned to the user
            if (!$isAdmin && $userId) {
                $query .= "WHERE t.created_by = ? OR t.assigned_to = ? ";
                $params = [$userId, $userId];
            } else {
                $params = [];
            }
            
            $query .= "ORDER BY t.created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
} 