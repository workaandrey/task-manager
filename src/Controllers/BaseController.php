<?php
declare(strict_types=1);

namespace App\Controllers;

use Psr\Container\ContainerInterface;

/**
 * Base Controller
 * 
 * Provides common functionality for all controllers
 */
abstract class BaseController
{
    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;
    
    /**
     * Create a new controller instance
     * 
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    /**
     * Helper method to render templates
     * 
     * @param string $template Template path
     * @param array $data Data to pass to the template
     * @return void
     */
    protected function render(string $template, array $data = []): void
    {
        // Extract data to make variables available in template
        extract($data);
        
        // Include header
        include_once '../templates/header.php';
        
        // Include the requested template
        include_once "../templates/{$template}.php";
        
        // Include footer
        include_once '../templates/footer.php';
    }
    
    /**
     * Helper method to redirect with a flash message
     * 
     * @param string $url URL to redirect to
     * @param string $message Message to display
     * @param string $type Message type (success, error)
     * @return void
     */
    protected function redirect(string $url, string $message = '', string $type = 'success'): void
    {
        if (!empty($message)) {
            $_SESSION["{$type}"] = $message;
        }
        
        header("Location: {$url}");
        exit;
    }
    
    /**
     * Helper method to validate request data
     * 
     * @param array $data Data to validate
     * @param array $required Required fields
     * @return string|null Error message or null if valid
     */
    protected function validate(array $data, array $required): ?string
    {
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return "The {$field} field is required";
            }
        }
        
        return null;
    }
} 