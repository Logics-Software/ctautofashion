<?php
class VehicleController {
    
    public function __construct() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            exit;
        }
    }
    
    /**
     * Display vehicle information page
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
                case 'clear_vehicle':
                    $this->clearVehicle();
                    return;
            }
        }
        
        // Handle POST requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->selectVehicle();
            return;
        }
        
        $user_data = $_SESSION['user_data'] ?? [];
        $user_name = $user_data['UserName'] ?? $user_data['Name'] ?? $user_data['user_name'] ?? $_SESSION['user_id'];
        
        // Get selected vehicle from session
        $selectedVehicle = $_SESSION['selected_vehicle'] ?? null;
        
        // Initialize VehicleModel
        $vehicleModel = new VehicleModel();
        
        $data = [
            'user_id' => $_SESSION['user_id'],
            'user_name' => $user_name,
            'user_data' => $user_data,
            'selected_vehicle' => $selectedVehicle
        ];
        
        $this->renderView('vehicle/index', $data);
    }
    
    /**
     * Handle AJAX requests
     */
    public function handleAjax() {
        if (isset($_GET['ajax'])) {
            switch ($_GET['ajax']) {
                case 'search_vehicle':
                    $this->searchVehicle();
                    break;
                case 'get_vehicle_transactions':
                    $this->getVehicleTransactions();
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
     * Handle AJAX requests for vehicle search
     */
    public function searchVehicle() {
        // Clear any previous output
        if (ob_get_level()) {
            ob_clean();
        }
        
        $searchTerm = $_GET['search'] ?? '';
        
        try {
            $vehicleModel = new VehicleModel();
            $vehicles = $vehicleModel->searchVehicles($searchTerm);
            
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($vehicles, JSON_UNESCAPED_UNICODE);
            exit;
        } catch (Exception $e) {
            error_log("VehicleController::searchVehicle error: " . $e->getMessage());
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Terjadi kesalahan saat mencari kendaraan']);
            exit;
        }
    }
    
    /**
     * Get vehicle transactions
     */
    public function getVehicleTransactions() {
        // Clear any previous output
        if (ob_get_level()) {
            ob_clean();
        }
        
        $kodeKendaraan = $_GET['vehicle'] ?? '';
        
        if (empty($kodeKendaraan)) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Vehicle code is required']);
            exit;
        }
        
        try {
            $vehicleModel = new VehicleModel();
            $transactions = $vehicleModel->getVehicleTransactions($kodeKendaraan);
            
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($transactions, JSON_UNESCAPED_UNICODE);
            exit;
        } catch (Exception $e) {
            error_log("VehicleController::getVehicleTransactions error: " . $e->getMessage());
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
        $kodeKendaraan = $_GET['vehicle'] ?? '';
        
        if (empty($noOrder) || empty($kodeKendaraan)) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Order number and vehicle code are required']);
            exit;
        }
        
        try {
            $vehicleModel = new VehicleModel();
            $serviceData = $vehicleModel->getServiceTransactions($noOrder, $kodeKendaraan);
            $partsData = $vehicleModel->getPartsTransactions($noOrder, $kodeKendaraan);
            $workOrderInfo = $vehicleModel->getWorkOrderInfo($noOrder, $kodeKendaraan);
            
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'service' => $serviceData,
                'parts' => $partsData,
                'workOrderInfo' => $workOrderInfo
            ], JSON_UNESCAPED_UNICODE);
            exit;
        } catch (Exception $e) {
            error_log("VehicleController::getWorkOrderDetails error: " . $e->getMessage());
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Terjadi kesalahan saat memuat detail work order']);
            exit;
        }
    }
    
    /**
     * Select vehicle and redirect back
     */
    public function selectVehicle() {
        if (isset($_POST['vehicle_code']) && isset($_POST['vehicle_name'])) {
            $_SESSION['selected_vehicle'] = [
                'code' => $_POST['vehicle_code'],
                'name' => $_POST['vehicle_name'],
                'no_polisi' => $_POST['vehicle_no_polisi'] ?? '',
                'merek' => $_POST['vehicle_merek'] ?? '',
                'customer' => $_POST['vehicle_customer'] ?? ''
            ];
        }
        
        $this->redirect('/vehicle');
    }
    
    /**
     * Clear selected vehicle
     */
    public function clearVehicle() {
        unset($_SESSION['selected_vehicle']);
        $this->redirect('/vehicle');
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
