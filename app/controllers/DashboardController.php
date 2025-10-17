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
        
        // Get monthly statistics for chart
        $chartData = $this->workOrderModel->getMonthlyStatistics();
        
        // Get monthly revenue for chart
        $revenueData = $this->workOrderModel->getMonthlyRevenue();
        
        // Get order statistics by status
        $orderStats = $this->workOrderModel->getOrderStatistics();
        
        $data = [
            'user_id' => $_SESSION['user_id'],
            'user_name' => $user_name,
            'user_data' => $user_data,
            'chartData' => $chartData,
            'revenueData' => $revenueData,
            'orderStats' => $orderStats
        ];
        
        $this->renderView('dashboard/index', $data);
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
