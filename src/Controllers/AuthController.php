<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Auth\Auth;
use App\Models\Role;
use Psr\Container\ContainerInterface;

/**
 * Authentication Controller
 * 
 * Handles user authentication, registration, and logout
 */
final class AuthController extends BaseController
{
    /**
     * Create a new auth controller instance
     * 
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }
    
    /**
     * Show login form
     * 
     * @return void
     */
    public function showLogin(): void
    {
        if ($this->container->get(Auth::class)->isLoggedIn()) {
            $this->redirect('/');
            return;
        }
        
        $this->render('auth/login');
    }
    
    /**
     * Process login
     * 
     * @return void
     */
    public function login(): void
    {
        $validationError = $this->validate($_POST, ['username', 'password']);
        if ($validationError) {
            $this->redirect('/login', $validationError, 'error');
            return;
        }
        
        $auth = $this->container->get(Auth::class);
        $result = $auth->login($_POST['username'], $_POST['password']);
        
        if (is_array($result)) {
            // Success - redirect to dashboard
            $this->redirect('/', 'Login successful', 'success');
        } else {
            // Error - back to login
            $this->redirect('/login', $result, 'error');
        }
    }
    
    /**
     * Show registration form
     * 
     * @return void
     */
    public function showRegister(): void
    {
        if ($this->container->get(Auth::class)->isLoggedIn()) {
            $this->redirect('/');
            return;
        }
        
        $this->render('auth/register');
    }
    
    /**
     * Process registration
     * 
     * @return void
     */
    public function register(): void
    {
        $validationError = $this->validate($_POST, [
            'username', 
            'email', 
            'password', 
            'confirm_password'
        ]);
        
        if ($validationError) {
            $this->redirect('/register', $validationError, 'error');
            return;
        }
        
        $auth = $this->container->get(Auth::class);

        $roleId = $this->container->get(Role::class)->getRoleId('user');

        $result = $auth->register(
            $_POST['username'],
            $_POST['email'],
            $_POST['password'],
            $_POST['confirm_password'],
            $roleId
        );
        
        if (is_array($result)) {
            $this->redirect('/', 'Registration successful', 'success');
        } else {
            $this->redirect('/register', $result, 'error');
        }
    }
    
    /**
     * Process logout
     * 
     * @return void
     */
    public function logout(): void
    {
        $auth = $this->container->get(Auth::class);
        $auth->logout();
        
        $this->redirect('/login', 'You have been logged out', 'success');
    }
} 