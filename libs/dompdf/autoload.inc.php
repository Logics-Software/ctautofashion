<?php
/**
 * Dompdf Simple Autoloader
 * 
 * This autoloader loads Dompdf classes without composer
 */

// Define Dompdf root directory
if (!defined('DOMPDF_DIR')) {
    define('DOMPDF_DIR', __DIR__);
}

// Simple PSR-4 compatible autoloader for Dompdf
spl_autoload_register(function ($class) {
    // Check if this is a Dompdf class
    if (strpos($class, 'Dompdf\\') === 0) {
        // Convert namespace to file path
        $classPath = str_replace('Dompdf\\', '', $class);
        $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $classPath);
        
        // Try to load from src directory
        $file = DOMPDF_DIR . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $classPath . '.php';
        
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    
    // Check if this is a FontLib class (dependency)
    if (strpos($class, 'FontLib\\') === 0) {
        $classPath = str_replace('FontLib\\', '', $class);
        $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $classPath);
        
        $file = DOMPDF_DIR . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'php-font-lib' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'FontLib' . DIRECTORY_SEPARATOR . $classPath . '.php';
        
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    
    // Check if this is a Svg class (dependency)
    if (strpos($class, 'Svg\\') === 0) {
        $classPath = str_replace('Svg\\', '', $class);
        $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $classPath);
        
        $file = DOMPDF_DIR . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'php-svg-lib' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Svg' . DIRECTORY_SEPARATOR . $classPath . '.php';
        
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    
    // Check if this is a Masterminds class (HTML5 parser)
    if (strpos($class, 'Masterminds\\') === 0) {
        $classPath = str_replace('Masterminds\\', '', $class);
        $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $classPath);
        
        $file = DOMPDF_DIR . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'html5-php' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $classPath . '.php';
        
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    
    // Check if this is a Sabberworm class (CSS parser)
    if (strpos($class, 'Sabberworm\\') === 0) {
        $classPath = str_replace('Sabberworm\\CSS\\', '', $class);
        $classPath = str_replace('Sabberworm\\', '', $classPath);
        $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $classPath);
        
        $file = DOMPDF_DIR . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'php-css-parser' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $classPath . '.php';
        
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    
    return false;
});

// Load Cpdf (legacy class)
if (file_exists(DOMPDF_DIR . '/lib/Cpdf.php')) {
    require_once DOMPDF_DIR . '/lib/Cpdf.php';
}

// All classes loaded via autoloader
return true;

