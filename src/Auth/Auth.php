<?php
declare(strict_types=1);

namespace App\Auth;

use PDO;
use PDOException;

/**
 * Auth class
 * 
 * Handles user authentication, registration, and session management
 */
final class Auth
{
    /**
     * @var PDO Database connection
     */
    private PDO $conn;
    
    /**
     * Create a new Auth instance
     * 
     * @param PDO $db Database connection
     */
    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }
    
    /**
     * Check if a user is currently logged in
     * 
     * @return bool True if user is logged in, false otherwise
     */
    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Get the current logged-in user
     * 
     * @return array|null User data or null if not logged in
     */
    public function getCurrentUser(): ?array
    {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        try {
            $query = "SELECT id, username, email, role_id, created_at 
                      FROM users 
                      WHERE id = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$_SESSION['user_id']]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Attempt to log in a user
     * 
     * @param string $username Username or email
     * @param string $password Plain text password
     * @return array|string User data on success, error message on failure
     */
    public function login(string $username, string $password)
    {
        try {
            $query = "SELECT id, username, password, role 
                      FROM users 
                      WHERE username = ? OR email = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$username, $username]);
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                return "Invalid username or password";
            }
            
            if (!password_verify($password, $user['password'])) {
                return "Invalid username or password";
            }
            
            $_SESSION['user_id'] = $user['id'];
            
            unset($user['password']);
            return $user;
        } catch (PDOException $e) {
            return "Database error: " . $e->getMessage();
        }
    }
    
    /**
     * Register a new user
     * 
     * @param string $username Username
     * @param string $email Email address
     * @param string $password Plain text password
     * @param string $confirmPassword Password confirmation
     * @param int $roleId Role ID
     * @return array|string User data on success, error message on failure
     */
    public function register(string $username, string $email, string $password, string $confirmPassword, int $roleId)
    {
        // Validate input
        if (empty($username) || empty($email) || empty($password)) {
            return "All fields are required";
        }
        
        if ($password !== $confirmPassword) {
            return "Passwords do not match";
        }
        
        if (strlen($password) < 6) {
            return "Password must be at least 6 characters long";
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Invalid email format";
        }
        
        try {
            $query = "SELECT id FROM users WHERE username = ? OR email = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$username, $email]);
            
            if ($stmt->rowCount() > 0) {
                return "Username or email already exists";
            }
            
            $query = "INSERT INTO users (username, email, password, role_id) 
                      VALUES (?, ?, ?, ?)";
            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$username, $email, $hashedPassword, $roleId]);
            
            $userId = (int) $this->conn->lastInsertId();
            
            // Set session
            $_SESSION['user_id'] = $userId;
            
            // Return user data
            return [
                'id' => $userId,
                'username' => $username,
                'email' => $email,
                'role_id' => $roleId
            ];
        } catch (PDOException $e) {
            return "Database error: " . $e->getMessage();
        }
    }
    
    /**
     * Log out the current user
     * 
     * @return void
     */
    public function logout(): void
    {
        if (isset($_SESSION['user_id'])) {
            unset($_SESSION['user_id']);
        }
        
        session_destroy();
    }
    
    /**
     * Check if a user has admin role
     * 
     * @return bool True if user is admin, false otherwise
     */
    public function isAdmin(): bool
    {
        $user = $this->getCurrentUser();
        return isset($user['role']) && $user['role'] === 'admin';
    }
} 