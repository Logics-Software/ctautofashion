<?php
class ProductController {
    
    public function __construct() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            exit;
        }
    }
    
    /**
     * Display products page with search, pagination, and sorting
     */
    public function index() {
        $user_data = $_SESSION['user_data'] ?? [];
        $user_name = $user_data['UserName'] ?? $user_data['Name'] ?? $user_data['user_name'] ?? $_SESSION['user_id'];
        
        // Get parameters from request
        $search = $_GET['search'] ?? '';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = $this->getValidLimit($_GET['limit'] ?? 10);
        $sortBy = $_GET['sort'] ?? 'B.NamaBarang';
        $sortOrder = $_GET['order'] ?? 'ASC';
        
        // Initialize ProductModel
        $productModel = new ProductModel();
        
        // Get products and total count
        $products = $productModel->getProducts($search, $page, $limit, $sortBy, $sortOrder);
        $totalProducts = $productModel->getTotalProducts($search);
        $totalPages = ceil($totalProducts / $limit);
        
        
        // Get available sort columns
        $sortColumns = $productModel->getSortColumns();
        
        // Prepare data for view
        $data = [
            'user_id' => $_SESSION['user_id'],
            'user_name' => $user_name,
            'user_data' => $user_data,
            'products' => $products,
            'search' => $search,
            'page' => $page,
            'limit' => $limit,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'totalProducts' => $totalProducts,
            'totalPages' => $totalPages,
            'sortColumns' => $sortColumns,
            'paginationOptions' => [10, 20, 40, 50, 100]
        ];
        
        $this->renderView('product/index', $data);
    }
    
    /**
     * Get valid pagination limit
     */
    private function getValidLimit($limit) {
        $validLimits = [10, 20, 40, 50, 100];
        $limit = (int)$limit;
        return in_array($limit, $validLimits) ? $limit : 10;
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
