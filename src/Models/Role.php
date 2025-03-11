<?php
declare(strict_types=1);

namespace App\Models;

use PDO;
use PDOException;

/**
 * Role Model
 *
 * Handles role management, CRUD operations, and database interactions
 */
final class Role
{
    /**
     * @var PDO Database connection
     */
    private PDO $conn;
    
    /**
     * Create a new Role instance
     *
     * @param PDO $db Database connection
     */
    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }
    
    /**
     * Create a new role
     *
     * @param string $name Role name
     * @param string|null $description Role description
     * @return int|bool Role ID on success, false on failure
     */
    public function create(string $name, ?string $description = null)
    {
        try {
            $query = "INSERT INTO roles (name, description) VALUES (?, ?)";
            $stmt = $this->conn->prepare($query);
            $success = $stmt->execute([$name, $description]);
            
            if ($success) {
                return (int) $this->conn->lastInsertId();
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Error creating role: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get role by ID
     *
     * @param int $id Role ID
     * @return array|null Role data or null if not found
     */
    public function read(int $id): ?array
    {
        try {
            $query = "SELECT * FROM roles WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            
            $role = $stmt->fetch(PDO::FETCH_ASSOC);
            return $role ?: null;
        } catch (PDOException $e) {
            error_log("Error reading role: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get all roles
     *
     * @return array List of roles
     */
    public function readAll(): array
    {
        try {
            $query = "SELECT * FROM roles ORDER BY name";
            $stmt = $this->conn->query($query);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error reading roles: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Update role
     *
     * @param int $id Role ID
     * @param array $data Role data to update
     * @return bool True on success, false on failure
     */
    public function update(int $id, array $data): bool
    {
        try {
            $fields = [];
            $values = [];
            
            foreach ($data as $key => $value) {
                if (in_array($key, ['name', 'description'])) {
                    $fields[] = "$key = ?";
                    $values[] = $value;
                }
            }
            
            if (empty($fields)) {
                return false;
            }
            
            $values[] = $id;
            
            $query = "UPDATE roles SET " . implode(', ', $fields) . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            
            return $stmt->execute($values);
        } catch (PDOException $e) {
            error_log("Error updating role: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete role
     *
     * @param int $id Role ID
     * @return bool True on success, false on failure
     */
    public function delete(int $id): bool
    {
        try {
            $query = "DELETE FROM roles WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error deleting role: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get role ID by name
     *
     * @param string $roleName Role name
     * @return int|bool Role ID on success, false on failure
     */
    public function getRoleId(string $roleName)
    {
        try {
            $query = "SELECT id FROM roles WHERE name = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$roleName]);
            
            $role = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$role) {
                // If the role doesn't exist, create it
                return $this->create($roleName);
            }
            
            return (int) $role['id'];
        } catch (PDOException $e) {
            // If the table doesn't exist yet, create it and try again
            if ($e->getCode() == '42S02') { // Table doesn't exist error code
                $this->createRolesTable();
                return $this->getRoleId($roleName);
            }
            
            error_log("Error getting role ID: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get role name by ID
     *
     * @param int $roleId Role ID
     * @return string|null Role name or null if not found
     */
    public function getRoleName(int $roleId): ?string
    {
        try {
            $query = "SELECT name FROM roles WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$roleId]);
            
            $role = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $role ? $role['name'] : null;
        } catch (PDOException $e) {
            error_log("Error getting role name: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Check if a role exists
     *
     * @param string $roleName Role name
     * @return bool True if role exists, false otherwise
     */
    public function roleExists(string $roleName): bool
    {
        try {
            $query = "SELECT COUNT(*) FROM roles WHERE name = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$roleName]);
            
            return (int) $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error checking if role exists: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create the roles table if it doesn't exist
     *
     * @return bool True on success, false on failure
     */
    private function createRolesTable(): bool
    {
        try {
            $query = "CREATE TABLE IF NOT EXISTS roles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(50) NOT NULL UNIQUE,
                description TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            
            $this->conn->exec($query);
            
            // Insert default roles
            return $this->insertDefaultRoles();
        } catch (PDOException $e) {
            error_log("Error creating roles table: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Insert default roles
     *
     * @return bool True on success, false on failure
     */
    private function insertDefaultRoles(): bool
    {
        $defaultRoles = [
            ['name' => 'admin', 'description' => 'Administrator with full access'],
            ['name' => 'user', 'description' => 'Regular user with limited access']
        ];
        
        try {
            $query = "INSERT IGNORE INTO roles (name, description) VALUES (?, ?)";
            $stmt = $this->conn->prepare($query);
            
            $success = true;
            foreach ($defaultRoles as $role) {
                $result = $stmt->execute([$role['name'], $role['description']]);
                $success = $success && $result;
            }
            
            return $success;
        } catch (PDOException $e) {
            // Ignore duplicate entry errors, but log other errors
            if ($e->getCode() != 23000) { // 23000 is duplicate entry error code
                error_log("Error inserting default roles: " . $e->getMessage());
            }
            return false;
        }
    }
    
    /**
     * Get users with a specific role
     *
     * @param int $roleId Role ID
     * @return array List of users
     */
    public function getUsersByRole(int $roleId): array
    {
        try {
            $query = "SELECT id, username, email, created_at 
                      FROM users 
                      WHERE role_id = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$roleId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting users by role: " . $e->getMessage());
            return [];
        }
    }
} 