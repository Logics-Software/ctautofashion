<?php
class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
    }
    
    /**
     * Display login page
     */
    public function login() {
        // If already logged in, redirect to dashboard
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
            exit;
        }
        
        $this->renderView('auth/login');
    }
    
    /**
     * Process login form
     */
    public function processLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
            exit;
        }
        
        $userid = $_POST['userid'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($userid) || empty($password)) {
            $_SESSION['error'] = 'UserID dan Password harus diisi!';
            $this->redirect('/login');
            exit;
        }
        
        // Authenticate user (case-insensitive)
        $user = $this->userModel->authenticate($userid, $password);
        
        if ($user) {
            // Login successful
            $_SESSION['user_id'] = $user['UserID'];
            $_SESSION['user_data'] = $user;
            // Default TipeUser to 0 (Operator) if not set
            $_SESSION['tipe_user'] = $user['TipeUser'] ?? 0; // Store TipeUser in session, default 0
            $_SESSION['success'] = 'Login berhasil! Selamat datang.';
            
            $this->redirect('/dashboard');
            exit;
        } else {
            // Login failed
            $_SESSION['error'] = 'UserID atau Password salah!';
            $this->redirect('/login');
            exit;
        }
    }
    
    /**
     * Logout user
     */
    public function logout() {
        session_destroy();
        $this->redirect('/login');
        exit;
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
