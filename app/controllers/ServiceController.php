<?php
class ServiceController {
    
    public function __construct() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            exit;
        }
    }
    
    /**
     * Display service information page
     */
    public function index() {
        // Handle AJAX requests first
        if (isset($_GET['ajax'])) {
            $this->handleAjax();
            return;
        }
        
        // Handle GET action requests
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'clear_customer':
                    $this->clearCustomer();
                    return;
            }
        }
        
        // Handle POST requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->selectCustomer();
            return;
        }
        
        $user_data = $_SESSION['user_data'] ?? [];
        $user_name = $user_data['UserName'] ?? $user_data['Name'] ?? $user_data['user_name'] ?? $_SESSION['user_id'];
        
        // Get selected customer from session or request
        $selectedCustomer = $_SESSION['selected_customer'] ?? null;
        
        // Initialize ServiceModel
        $serviceModel = new ServiceModel();
        
        $data = [
            'user_id' => $_SESSION['user_id'],
            'user_name' => $user_name,
            'user_data' => $user_data,
            'selected_customer' => $selectedCustomer
        ];
        
        $this->renderView('service/index', $data);
    }
    
    /**
     * Handle AJAX requests
     */
    public function handleAjax() {
        if (isset($_GET['ajax'])) {
            switch ($_GET['ajax']) {
                case 'search_customer':
                    $this->searchCustomer();
                    break;
                case 'get_transactions':
                    $this->getCustomerTransactions();
                    break;
                case 'get_workorder':
                    $this->getWorkOrderDetails();
                    break;
                default:
                    http_response_code(404);
                    echo json_encode(['error' => 'AJAX action not found']);
                    exit;
            }
        }
    }
    
    /**
     * Handle AJAX requests for customer search
     */
    public function searchCustomer() {
        // Clear any previous output
        if (ob_get_level()) {
            ob_clean();
        }
        
        $searchTerm = $_GET['search'] ?? '';
        
        try {
            $serviceModel = new ServiceModel();
            $customers = $serviceModel->searchCustomers($searchTerm);
            
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($customers, JSON_UNESCAPED_UNICODE);
            exit;
        } catch (Exception $e) {
            error_log("ServiceController::searchCustomer error: " . $e->getMessage());
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Terjadi kesalahan saat mencari customer']);
            exit;
        }
    }
    
    /**
     * Get customer transactions
     */
    public function getCustomerTransactions() {
        // Clear any previous output
        if (ob_get_level()) {
            ob_clean();
        }
        
        $kodeCustomer = $_GET['customer'] ?? '';
        
        if (empty($kodeCustomer)) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Customer code is required']);
            exit;
        }
        
        try {
            $serviceModel = new ServiceModel();
            $transactions = $serviceModel->getCustomerTransactions($kodeCustomer);
            
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($transactions, JSON_UNESCAPED_UNICODE);
            exit;
        } catch (Exception $e) {
            error_log("ServiceController::getCustomerTransactions error: " . $e->getMessage());
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Terjadi kesalahan saat memuat transaksi']);
            exit;
        }
    }
    
    /**
     * Get work order details (service and parts)
     */
    public function getWorkOrderDetails() {
        // Clear any previous output
        if (ob_get_level()) {
            ob_clean();
        }
        
        $noOrder = $_GET['no_order'] ?? '';
        $kodeCustomer = $_GET['customer'] ?? '';
        
        if (empty($noOrder) || empty($kodeCustomer)) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Order number and customer code are required']);
            exit;
        }
        
        try {
            $serviceModel = new ServiceModel();
            $serviceData = $serviceModel->getServiceTransactions($noOrder, $kodeCustomer);
            $partsData = $serviceModel->getPartsTransactions($noOrder, $kodeCustomer);
            $workOrderInfo = $serviceModel->getWorkOrderInfo($noOrder, $kodeCustomer);
            
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'service' => $serviceData,
                'parts' => $partsData,
                'workOrderInfo' => $workOrderInfo
            ], JSON_UNESCAPED_UNICODE);
            exit;
        } catch (Exception $e) {
            error_log("ServiceController::getWorkOrderDetails error: " . $e->getMessage());
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Terjadi kesalahan saat memuat detail work order']);
            exit;
        }
    }
    
    /**
     * Select customer and redirect back
     */
    public function selectCustomer() {
        if (isset($_POST['customer_code']) && isset($_POST['customer_name'])) {
            $_SESSION['selected_customer'] = [
                'code' => $_POST['customer_code'],
                'name' => $_POST['customer_name'],
                'address' => $_POST['customer_address'] ?? '',
                'city' => $_POST['customer_city'] ?? '',
                'phone' => $_POST['customer_phone'] ?? '',
                'pic' => $_POST['customer_pic'] ?? ''
            ];
        }
        
        $this->redirect('/service');
    }
    
    /**
     * Clear selected customer
     */
    public function clearCustomer() {
        unset($_SESSION['selected_customer']);
        $this->redirect('/service');
    }
    
    /**
     * Render view template
     */
    private function renderView($view, $data = []) {
        $viewFile = BASE_PATH . '/app/views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            // Extract data array to variables
            extract($data);
            
            // Start output buffering
            ob_start();
            include $viewFile;
            $content = ob_get_clean();
            
            // Include layout if it exists
            $layoutFile = BASE_PATH . '/app/views/layouts/main.php';
            if (file_exists($layoutFile)) {
                include $layoutFile;
            } else {
                echo $content;
            }
        } else {
            echo "View not found: " . $view;
        }
    }
    
    /**
     * Redirect helper method
     */
    private function redirect($path) {
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        $fullPath = $basePath . $path;
        header('Location: ' . $fullPath);
    }
}
?>
