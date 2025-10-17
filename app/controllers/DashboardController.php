<?php
require_once BASE_PATH . '/app/models/WorkOrderModel.php';

class DashboardController {
    private $workOrderModel;
    
    public function __construct() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            exit;
        }
        
        $this->workOrderModel = new WorkOrderModel();
    }
    
    /**
     * Display dashboard page
     */
    public function index() {
        $user_data = $_SESSION['user_data'] ?? [];
        $user_name = $user_data['UserName'] ?? $user_data['Name'] ?? $user_data['user_name'] ?? $_SESSION['user_id'];
        $user_id = $_SESSION['user_id'];
        
        // Get TipeUser from session or user_data
        $tipe_user = $_SESSION['tipe_user'] ?? $user_data['TipeUser'] ?? null;
        
        // Get monthly statistics for chart (only if TipeUser >= 2, excluding 0 and 1)
        $chartData = null;
        $revenueData = null;
        if ((int)$tipe_user >= 2) {
            $chartData = $this->workOrderModel->getMonthlyStatistics();
            $revenueData = $this->workOrderModel->getMonthlyRevenue();
        }
        
        // Get order statistics by status (default: today)
        // If Operator (TipeUser = 0), filter by UserID
        $orderStats = $this->workOrderModel->getOrderStatistics('today', null, null, $user_id, $tipe_user);
        
        $data = [
            'user_id' => $user_id,
            'user_name' => $user_name,
            'user_data' => $user_data,
            'tipe_user' => $tipe_user,
            'chartData' => $chartData,
            'revenueData' => $revenueData,
            'orderStats' => $orderStats
        ];
        
        $this->renderView('dashboard/index', $data);
    }
    
    /**
     * AJAX endpoint to get order statistics by period
     */
    public function getOrderStats() {
        // Check if it's an AJAX request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }
        
        // Get POST data
        $period = $_POST['period'] ?? 'today';
        $startDate = $_POST['start_date'] ?? null;
        $endDate = $_POST['end_date'] ?? null;
        
        // Get user info from session
        $user_id = $_SESSION['user_id'];
        $user_data = $_SESSION['user_data'] ?? [];
        $tipe_user = $_SESSION['tipe_user'] ?? $user_data['TipeUser'] ?? null;
        
        // Get statistics (filtered by UserID if Operator/Staff)
        $orderStats = $this->workOrderModel->getOrderStatistics($period, $startDate, $endDate, $user_id, $tipe_user);
        
        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($orderStats);
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
