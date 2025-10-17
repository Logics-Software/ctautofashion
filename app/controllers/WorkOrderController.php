<?php
class WorkOrderController {
    
    public function __construct() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            exit;
        }
    }
    
    /**
     * Display work order information page
     */
    public function index() {
        // Handle AJAX requests first
        if (isset($_GET['ajax'])) {
            $this->handleAjax();
            return;
        }
        
        $user_data = $_SESSION['user_data'] ?? [];
        $user_name = $user_data['UserName'] ?? $user_data['Name'] ?? $user_data['user_name'] ?? $_SESSION['user_id'];
        
        // Get filter parameters
        $filters = [
            'start_date' => $_GET['start_date'] ?? date('Y-m-d'),
            'end_date' => $_GET['end_date'] ?? date('Y-m-d'),
            'status' => $_GET['status'] ?? '',
            'customer' => $_GET['customer'] ?? '',
            'no_polisi' => $_GET['no_polisi'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];
        
        // Pagination parameters
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = (int)($_GET['limit'] ?? 25);
        $offset = ($page - 1) * $limit;
        
        // Initialize WorkOrderModel
        $workOrderModel = new WorkOrderModel();
        
        // Get work orders with filters
        $workOrders = $workOrderModel->getWorkOrders($filters, $limit, $offset);
        $totalWorkOrders = $workOrderModel->getTotalWorkOrders($filters);
        
        // Get filter options
        $customers = $workOrderModel->getCustomers();
        $vehicles = $workOrderModel->getVehicles();
        
        // Status options
        $statusOptions = [
            '0' => 'Belum diproses',
            '1' => 'Sedang diproses', 
            '2' => 'Proses Selesai',
            '3' => 'Faktur dibuat',
            '4' => 'Telah dibayar',
            '5' => 'Dibatalkan'
        ];
        
        // Pagination calculation
        $totalPages = ceil($totalWorkOrders / $limit);
        $paginationOptions = [10, 25, 50, 100];
        
        $data = [
            'user_id' => $_SESSION['user_id'],
            'user_name' => $user_name,
            'user_data' => $user_data,
            'filters' => $filters,
            'workOrders' => $workOrders,
            'totalWorkOrders' => $totalWorkOrders,
            'customers' => $customers,
            'vehicles' => $vehicles,
            'statusOptions' => $statusOptions,
            'page' => $page,
            'limit' => $limit,
            'totalPages' => $totalPages,
            'paginationOptions' => $paginationOptions
        ];
        
        $this->renderView('workorder/index', $data);
    }
    
    /**
     * Handle AJAX requests
     */
    public function handleAjax() {
        if (isset($_GET['ajax'])) {
            switch ($_GET['ajax']) {
                case 'get_customers':
                    $this->searchCustomers();
                    break;
                case 'get_vehicles':
                    $this->searchVehicles();
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
    public function searchCustomers() {
        // Clear any previous output
        if (ob_get_level()) {
            ob_clean();
        }
        
        $search = $_GET['search'] ?? '';
        
        try {
            $workOrderModel = new WorkOrderModel();
            $customers = $workOrderModel->searchCustomers($search);
            
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($customers, JSON_UNESCAPED_UNICODE);
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Terjadi kesalahan saat mencari customer']);
            exit;
        }
    }
    
    /**
     * Handle AJAX requests for vehicle search
     */
    public function searchVehicles() {
        // Clear any previous output
        if (ob_get_level()) {
            ob_clean();
        }
        
        $search = $_GET['search'] ?? '';
        
        try {
            $workOrderModel = new WorkOrderModel();
            $vehicles = $workOrderModel->searchVehicles($search);
            
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($vehicles, JSON_UNESCAPED_UNICODE);
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Terjadi kesalahan saat mencari kendaraan']);
            exit;
        }
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
