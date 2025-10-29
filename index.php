<?php
// Simple MVC Framework
// Disable error display to prevent breaking JSON responses
ini_set('display_errors', 0);
error_reporting(0);

session_start();

// Define base path
define('BASE_PATH', __DIR__);

// Autoload classes
spl_autoload_register(function ($class) {
    $directories = [
        BASE_PATH . '/app/controllers/',
        BASE_PATH . '/app/models/',
        BASE_PATH . '/app/core/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Include database configuration
require_once BASE_PATH . '/app/config/database.php';

// Simple Router
class Router {
    private $routes = [];
    
    public function addRoute($method, $path, $controller, $action) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'controller' => $controller,
            'action' => $action
        ];
    }
    
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove base path if running in subfolder
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        if ($basePath !== '/') {
            $path = substr($path, strlen($basePath));
        }
        
        $path = rtrim($path, '/') ?: '/';
        
        // Debug information
        if (isset($_GET['debug'])) {
            echo "<pre>";
            echo "Method: " . $method . "\n";
            echo "Path: " . $path . "\n";
            echo "Base Path: " . $basePath . "\n";
            echo "Request URI: " . $_SERVER['REQUEST_URI'] . "\n";
            echo "Script Name: " . $_SERVER['SCRIPT_NAME'] . "\n";
            echo "</pre>";
        }
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $path) {
                $controller = new $route['controller']();
                $action = $route['action'];
                
                if (method_exists($controller, $action)) {
                    $controller->$action();
                    return;
                }
            }
        }
        
        // Default route
        if ($path === '/') {
            if (isset($_SESSION['user_id'])) {
                $this->redirect('/dashboard');
                exit;
            } else {
                $this->redirect('/login');
                exit;
            }
        }
        
        // 404 Not Found
        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1>";
        echo "<p>The requested page was not found.</p>";
        echo "<p><a href='/'>Go to Home</a></p>";
    }
    
    private function redirect($path) {
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        $fullPath = $basePath . $path;
        header('Location: ' . $fullPath);
    }
}

// Initialize router
$router = new Router();

// Define routes
$router->addRoute('GET', '/login', 'AuthController', 'login');
$router->addRoute('POST', '/login', 'AuthController', 'processLogin');
$router->addRoute('GET', '/dashboard', 'DashboardController', 'index');
$router->addRoute('POST', '/dashboard/getOrderStats', 'DashboardController', 'getOrderStats');
$router->addRoute('GET', '/logout', 'AuthController', 'logout');

// Profile routes
$router->addRoute('GET', '/profile', 'ProfileController', 'index');
$router->addRoute('GET', '/profile/password', 'ProfileController', 'password');
$router->addRoute('POST', '/profile/update', 'ProfileController', 'update');
$router->addRoute('POST', '/profile/change-password', 'ProfileController', 'changePassword');
$router->addRoute('POST', '/profile/upload-photo', 'ProfileController', 'uploadPhoto');
$router->addRoute('POST', '/profile/delete-photo', 'ProfileController', 'deletePhoto');

// Product routes
$router->addRoute('GET', '/products', 'ProductController', 'index');

// Service routes
$router->addRoute('GET', '/service', 'ServiceController', 'index');
$router->addRoute('POST', '/service', 'ServiceController', 'selectCustomer');
$router->addRoute('GET', '/service/clear', 'ServiceController', 'clearCustomer');

// Vehicle routes
$router->addRoute('GET', '/vehicle', 'VehicleController', 'index');
$router->addRoute('POST', '/vehicle', 'VehicleController', 'selectVehicle');

// Work Order routes
$router->addRoute('GET', '/workorder', 'WorkOrderController', 'index');

// Process Work Order routes
$router->addRoute('GET', '/processworkorder', 'ProcessWorkOrderController', 'index');
$router->addRoute('POST', '/processworkorder', 'ProcessWorkOrderController', 'index');

// Transaksi Work Order routes
$router->addRoute('GET', '/transaksi-work-order', 'TransaksiWorkOrderController', 'index');
$router->addRoute('POST', '/transaksi-work-order', 'TransaksiWorkOrderController', 'index');
$router->addRoute('GET', '/transaksi-work-order/search-customers', 'TransaksiWorkOrderController', 'searchCustomers');
$router->addRoute('GET', '/transaksi-work-order/get-customer', 'TransaksiWorkOrderController', 'getCustomer');
$router->addRoute('GET', '/transaksi-work-order/search-vehicles', 'TransaksiWorkOrderController', 'searchVehicles');
$router->addRoute('GET', '/transaksi-work-order/get-vehicle', 'TransaksiWorkOrderController', 'getVehicle');
$router->addRoute('GET', '/transaksi-work-order/get-vehicle-by-customer', 'TransaksiWorkOrderController', 'getVehicleByCustomer');
$router->addRoute('GET', '/transaksi-work-order/search-jasa', 'TransaksiWorkOrderController', 'searchJasa');
$router->addRoute('GET', '/transaksi-work-order/get-jasa', 'TransaksiWorkOrderController', 'getJasa');
$router->addRoute('GET', '/transaksi-work-order/search-barang', 'TransaksiWorkOrderController', 'searchBarang');
$router->addRoute('GET', '/transaksi-work-order/get-barang', 'TransaksiWorkOrderController', 'getBarang');
$router->addRoute('GET', '/transaksi-work-order/get-stok-barang', 'TransaksiWorkOrderController', 'getStokBarang');
$router->addRoute('GET', '/transaksi-work-order/search-montir', 'TransaksiWorkOrderController', 'searchMontir');
$router->addRoute('GET', '/transaksi-work-order/get-montir', 'TransaksiWorkOrderController', 'getMontir');
$router->addRoute('GET', '/transaksi-work-order/search-picker', 'TransaksiWorkOrderController', 'searchPicker');
$router->addRoute('GET', '/transaksi-work-order/get-picker', 'TransaksiWorkOrderController', 'getPicker');
$router->addRoute('GET', '/transaksi-work-order/get-detail', 'TransaksiWorkOrderController', 'getDetail');
$router->addRoute('GET', '/transaksi-work-order/get-data-for-edit', 'TransaksiWorkOrderController', 'getDataForEdit');
$router->addRoute('POST', '/transaksi-work-order/save', 'TransaksiWorkOrderController', 'save');
$router->addRoute('POST', '/transaksi-work-order/update', 'TransaksiWorkOrderController', 'update');
$router->addRoute('GET', '/transaksi-work-order/get-kota-list', 'TransaksiWorkOrderController', 'getKotaList');
$router->addRoute('POST', '/transaksi-work-order/save-customer', 'TransaksiWorkOrderController', 'saveCustomer');
$router->addRoute('GET', '/transaksi-work-order/get-merek-list', 'TransaksiWorkOrderController', 'getMerekList');
$router->addRoute('GET', '/transaksi-work-order/get-model-list', 'TransaksiWorkOrderController', 'getModelList');
$router->addRoute('POST', '/transaksi-work-order/save-vehicle', 'TransaksiWorkOrderController', 'saveVehicle');
$router->addRoute('GET', '/transaksi-work-order/download-pdf', 'TransaksiWorkOrderController', 'downloadPDF');

// Dispatch the request
$router->dispatch();
