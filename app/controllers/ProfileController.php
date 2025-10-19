<?php
class ProfileController {
    private $profileModel;
    
    public function __construct() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            exit;
        }
        
        $this->profileModel = new ProfileModel();
    }
    
    /**
     * Display profile page
     */
    public function index() {
        $user_id = $_SESSION['user_id'];
        $user_data = $_SESSION['user_data'] ?? [];
        $profile_photo = $this->profileModel->getProfilePhoto($user_id);
        
        $data = [
            'user_id' => $user_id,
            'user_name' => $user_data['UserName'] ?? $user_data['Name'] ?? $user_data['user_name'] ?? $user_id,
            'user_data' => $user_data,
            'profile_photo' => $profile_photo
        ];
        
        $this->renderView('profile/index', $data);
    }
    
    /**
     * Display change password page
     */
    public function password() {
        $user_id = $_SESSION['user_id'];
        $user_data = $_SESSION['user_data'] ?? [];
        
        $data = [
            'user_id' => $user_id,
            'user_name' => $user_data['UserName'] ?? $user_data['Name'] ?? $user_data['user_name'] ?? $user_id,
            'user_data' => $user_data
        ];
        
        $this->renderView('profile/password', $data);
    }
    
    /**
     * Update profile information
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/profile');
            exit;
        }
        
        $user_id = $_SESSION['user_id'];
        $user_name = trim($_POST['user_name'] ?? '');
        
        if (empty($user_name)) {
            $_SESSION['error'] = 'Nama user tidak boleh kosong!';
            $this->redirect('/profile');
            exit;
        }
        
        // Handle photo upload first if provided
        $photo_uploaded = false;
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $upload_result = $this->profileModel->uploadProfilePhoto($user_id, $_FILES['profile_photo']);
            if (!$upload_result['success']) {
                $_SESSION['error'] = 'Foto gagal diupload: ' . $upload_result['message'];
                $this->redirect('/profile');
                exit;
            }
            $photo_uploaded = true;
        } else if (isset($_FILES['profile_photo'])) {
        }
        
        // Update user name
        $result = $this->profileModel->updateProfile($user_id, ['UserName' => $user_name]);
        
        if ($result['success']) {
            // Update session data
            $_SESSION['user_data']['UserName'] = $user_name;
            
            if ($photo_uploaded) {
                $_SESSION['success'] = 'Profile dan foto berhasil diupdate!';
            } else {
                $_SESSION['success'] = 'Profile berhasil diupdate!';
            }
        } else {
            $_SESSION['error'] = $result['message'];
        }
        
        $this->redirect('/dashboard');
        exit;
    }
    
    /**
     * Change password
     */
    public function changePassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/profile/password');
            exit;
        }
        
        $user_id = $_SESSION['user_id'];
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Validation
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $_SESSION['error'] = 'Semua field harus diisi!';
            $this->redirect('/profile/password');
            exit;
        }
        
        if ($new_password !== $confirm_password) {
            $_SESSION['error'] = 'Password baru dan konfirmasi password tidak cocok!';
            $this->redirect('/profile/password');
            exit;
        }
        
        if (strlen($new_password) < 6) {
            $_SESSION['error'] = 'Password baru minimal 6 karakter!';
            $this->redirect('/profile/password');
            exit;
        }
        
        // Verify current password
        $userModel = new UserModel();
        $user = $userModel->authenticate($_SESSION['user_data']['UserID'], $current_password);
        
        if (!$user) {
            $_SESSION['error'] = 'Password lama tidak benar!';
            $this->redirect('/profile/password');
            exit;
        }
        
        // Update password
        $result = $this->profileModel->updatePassword($user_id, $new_password);
        
        if ($result['success']) {
            $_SESSION['success'] = 'Password berhasil diubah!';
            $this->redirect('/dashboard');
        } else {
            $_SESSION['error'] = $result['message'];
            $this->redirect('/profile/password');
        }
        exit;
    }
    
    /**
     * Upload profile photo
     */
    public function uploadPhoto() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/profile');
            exit;
        }
        
        $user_id = $_SESSION['user_id'];
        
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $upload_result = $this->profileModel->uploadProfilePhoto($user_id, $_FILES['profile_photo']);
            
            if ($upload_result['success']) {
                $_SESSION['success'] = 'Foto profile berhasil diupload!';
            } else {
                $_SESSION['error'] = $upload_result['message'];
            }
        } else {
            $_SESSION['error'] = 'Gagal upload foto. Silakan pilih file yang valid.';
        }
        
        $this->redirect('/profile');
        exit;
    }
    
    /**
     * Delete profile photo
     */
    public function deletePhoto() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/profile');
            exit;
        }
        
        $user_id = $_SESSION['user_id'];
        $result = $this->profileModel->deleteProfilePhoto($user_id);
        
        if ($result['success']) {
            $_SESSION['success'] = 'Foto profile berhasil dihapus!';
        } else {
            $_SESSION['error'] = $result['message'];
        }
        
        $this->redirect('/profile');
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
