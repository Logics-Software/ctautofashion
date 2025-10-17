<?php
class ProcessWorkOrderController {
    
    public function __construct() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            exit;
        }
    }
    
    /**
     * Display process work order page (StatusOrder < 2)
     * No filters - only search
     */
    public function index() {
        // Check for AJAX requests first
        if (isset($_GET['ajax'])) {
            // Clear any output buffer
            while (ob_get_level()) {
                ob_end_clean();
            }
            $this->handleAjax();
            return;
        }
        
        $user_data = $_SESSION['user_data'] ?? [];
        $user_name = $user_data['UserName'] ?? $user_data['Name'] ?? $user_data['user_name'] ?? $_SESSION['user_id'];
        
        // Get search parameter only
        $search = $_GET['search'] ?? '';
        
        // Pagination parameters
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = (int)($_GET['limit'] ?? 25);
        $offset = ($page - 1) * $limit;
        
        // Initialize ProcessWorkOrderModel
        $processWorkOrderModel = new ProcessWorkOrderModel();
        
        // Get user type from session
        $tipeUser = isset($_SESSION['tipe_user']) ? (int)$_SESSION['tipe_user'] : null;
        $userID = $_SESSION['user_id'] ?? null;
        
        // Get work orders with search only (with role-based access)
        $workOrders = $processWorkOrderModel->getWorkOrders($search, $limit, $offset, $userID, $tipeUser);
        $totalWorkOrders = $processWorkOrderModel->getTotalWorkOrders($search, $userID, $tipeUser);
        
        // Pagination calculation
        $totalPages = ceil($totalWorkOrders / $limit);
        $paginationOptions = [10, 25, 50, 100];
        
        $data = [
            'user_id' => $_SESSION['user_id'],
            'user_name' => $user_name,
            'user_data' => $user_data,
            'search' => $search,
            'workOrders' => $workOrders,
            'totalWorkOrders' => $totalWorkOrders,
            'page' => $page,
            'limit' => $limit,
            'totalPages' => $totalPages,
            'paginationOptions' => $paginationOptions
        ];
        
        $this->renderView('processworkorder/index', $data);
    }
    
    /**
     * Handle AJAX requests
     */
    private function handleAjax() {
        // Ensure clean output
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Suppress any PHP errors/warnings for clean JSON output
        error_reporting(0);
        ini_set('display_errors', '0');
        
        $action = $_GET['ajax'] ?? '';
        
        switch ($action) {
            case 'get_detail':
                $this->getWorkOrderDetail();
                break;
            case 'proses_order':
                $this->prosesWorkOrder();
                break;
            case 'selesai_order':
                $this->selesaiWorkOrder();
                break;
            case 'batal_order':
                $this->batalWorkOrder();
                break;
            default:
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['error' => 'Invalid action']);
                exit;
        }
    }
    
    /**
     * Get work order detail (AJAX endpoint)
     */
    private function getWorkOrderDetail() {
        // Clear any previous output
        if (ob_get_level()) {
            ob_clean();
        }
        
        $noOrder = $_GET['noorder'] ?? '';
        
        if (empty($noOrder)) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'No Order tidak ditemukan']);
            exit;
        }
        
        try {
            $processWorkOrderModel = new ProcessWorkOrderModel();
            $detail = $processWorkOrderModel->getWorkOrderDetail($noOrder);
            
            if ($detail) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode($detail, JSON_UNESCAPED_UNICODE);
            } else {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['error' => 'Data tidak ditemukan']);
            }
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Terjadi kesalahan saat mengambil data']);
            exit;
        }
    }
    
    /**
     * Proses Work Order (AJAX endpoint)
     */
    private function prosesWorkOrder() {
        // Clear any previous output completely
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Start fresh output buffer
        ob_start();
        
        // Set header first
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            // Validate request method
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['error' => 'Invalid request method']);
                exit;
            }
            
            // Get NoOrder from POST
            $noOrder = $_POST['noorder'] ?? '';
            
            if (empty($noOrder)) {
                echo json_encode(['error' => 'NoOrder tidak ditemukan']);
                exit;
            }
            
            // Get User ID from session
            $userID = $_SESSION['user_id'] ?? null;
            
            if (empty($userID)) {
                echo json_encode(['error' => 'User tidak terautentikasi']);
                exit;
            }
            
            // Process work order
            $processWorkOrderModel = new ProcessWorkOrderModel();
            $result = $processWorkOrderModel->prosesWorkOrder($noOrder, $userID);
            
            // Return result
            if ($result['success']) {
                // Set session success message
                $_SESSION['success'] = '✓ Work Order <strong>' . htmlspecialchars($noOrder) . '</strong> berhasil diproses!';
                session_write_close(); // Force write session before response
                $response = json_encode(['success' => true, 'message' => 'Work Order berhasil diproses']);
            } else {
                $_SESSION['error'] = $result['error'] ?? 'Gagal memproses Work Order';
                session_write_close(); // Force write session before response
                $response = json_encode(['error' => $result['error'] ?? 'Gagal memproses Work Order']);
            }
            
            // Clean output buffer and send response
            ob_clean();
            echo $response;
            ob_end_flush();
            exit;
            
        } catch (Exception $e) {
            ob_clean();
            echo json_encode(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
            ob_end_flush();
            exit;
        }
    }
    
    /**
     * Selesai Work Order (AJAX endpoint)
     */
    private function selesaiWorkOrder() {
        // Clear any previous output completely
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Start fresh output buffer
        ob_start();
        
        // Set header first
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            // Validate request method
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['error' => 'Invalid request method']);
                exit;
            }
            
            // Get NoOrder from POST
            $noOrder = $_POST['noorder'] ?? '';
            
            if (empty($noOrder)) {
                echo json_encode(['error' => 'NoOrder tidak ditemukan']);
                exit;
            }
            
            // Get User ID from session
            $userID = $_SESSION['user_id'] ?? null;
            
            if (empty($userID)) {
                echo json_encode(['error' => 'User tidak terautentikasi']);
                exit;
            }
            
            // Complete work order
            $processWorkOrderModel = new ProcessWorkOrderModel();
            $result = $processWorkOrderModel->selesaiWorkOrder($noOrder, $userID);
            
            // Return result
            if ($result['success']) {
                // Set session success message
                $_SESSION['success'] = '✓ Work Order <strong>' . htmlspecialchars($noOrder) . '</strong> berhasil diselesaikan!';
                session_write_close(); // Force write session before response
                $response = json_encode(['success' => true, 'message' => 'Work Order berhasil diselesaikan']);
            } else {
                $_SESSION['error'] = $result['error'] ?? 'Gagal menyelesaikan Work Order';
                session_write_close(); // Force write session before response
                $response = json_encode(['error' => $result['error'] ?? 'Gagal menyelesaikan Work Order']);
            }
            
            // Clean output buffer and send response
            ob_clean();
            echo $response;
            ob_end_flush();
            exit;
            
        } catch (Exception $e) {
            ob_clean();
            echo json_encode(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
            ob_end_flush();
            exit;
        }
    }
    
    /**
     * Batal Work Order (AJAX endpoint)
     */
    private function batalWorkOrder() {
        // Clear any previous output completely
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Start fresh output buffer
        ob_start();
        
        // Set header first
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            // Validate request method
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['error' => 'Invalid request method']);
                exit;
            }
            
            // Get NoOrder from POST
            $noOrder = $_POST['noorder'] ?? '';
            
            if (empty($noOrder)) {
                echo json_encode(['error' => 'NoOrder tidak ditemukan']);
                exit;
            }
            
            // Get User ID from session
            $userID = $_SESSION['user_id'] ?? null;
            
            if (empty($userID)) {
                echo json_encode(['error' => 'User tidak terautentikasi']);
                exit;
            }
            
            // Process cancellation
            $processWorkOrderModel = new ProcessWorkOrderModel();
            $result = $processWorkOrderModel->batalWorkOrder($noOrder, $userID);
            
            // Return result
            if ($result['success']) {
                // Set session success message
                $_SESSION['success'] = '✓ Work Order <strong>' . htmlspecialchars($noOrder) . '</strong> berhasil dibatalkan!';
                session_write_close(); // Force write session before response
                $response = json_encode(['success' => true, 'message' => 'Work Order berhasil dibatalkan']);
            } else {
                $_SESSION['error'] = $result['error'] ?? 'Gagal membatalkan Work Order';
                session_write_close(); // Force write session before response
                $response = json_encode(['error' => $result['error'] ?? 'Gagal membatalkan Work Order']);
            }
            
            // Clean output buffer and send response
            ob_clean();
            echo $response;
            ob_end_flush();
            exit;
            
        } catch (Exception $e) {
            ob_clean();
            echo json_encode(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
            ob_end_flush();
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