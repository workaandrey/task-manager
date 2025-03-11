<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Configuration manager class
 * 
 * Handles loading and retrieving configuration values
 */
final class Config
{
    /**
     * @var array Loaded configuration values
     */
    private static array $config = [];
    
    /**
     * @var bool Whether the configuration has been loaded
     */
    private static bool $loaded = false;
    
    /**
     * Load environment configuration
     * 
     * @param string $envFile Path to the .env file
     * @return void
     */
    public static function load(string $envFile = null): void
    {
        if (self::$loaded) {
            return;
        }
        
        if ($envFile === null) {
            $envFile = dirname(__DIR__, 2) . '/.env';
        }
        
        if (file_exists($envFile)) {
            self::parseEnvFile($envFile);
        }
        
        foreach ($_ENV as $key => $value) {
            self::$config[$key] = $value;
        }
        
        self::setDefaults();
        
        self::$loaded = true;
    }
    
    /**
     * Parse an environment file
     * 
     * @param string $envFile Path to the .env file
     * @return void
     */
    private static function parseEnvFile(string $envFile): void
    {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                if (strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) {
                    $value = substr($value, 1, -1);
                } elseif (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1) {
                    $value = substr($value, 1, -1);
                }
                
                self::$config[$key] = $value;
            }
        }
    }
    
    /**
     * Set default configuration values
     * 
     * @return void
     */
    private static function setDefaults(): void
    {
        $defaults = [
            'DB_HOST' => 'localhost',
            'DB_NAME' => 'task_manager',
            'DB_USER' => 'root',
            'DB_PASS' => '',
            'DB_CHARSET' => 'utf8mb4',
            'APP_ENV' => 'development',
            'APP_DEBUG' => 'true',
        ];
        
        foreach ($defaults as $key => $value) {
            if (!isset(self::$config[$key])) {
                self::$config[$key] = $value;
            }
        }
    }
    
    /**
     * Get a configuration value
     * 
     * @param string $key The configuration key
     * @param mixed $default Default value if the key doesn't exist
     * @return mixed The configuration value
     */
    public static function get(string $key, $default = null)
    {
        if (!self::$loaded) {
            self::load();
        }
        
        return self::$config[$key] ?? $default;
    }
    
    /**
     * Check if a configuration value exists
     * 
     * @param string $key The configuration key
     * @return bool Whether the key exists
     */
    public static function has(string $key): bool
    {
        if (!self::$loaded) {
            self::load();
        }
        
        return isset(self::$config[$key]);
    }
} 