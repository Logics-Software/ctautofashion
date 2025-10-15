<?php
$title = 'Login';
?>

<div class="login-container">
    <div class="login-card fade-in">
        <div class="login-header">
            <div class="d-flex align-items-center justify-content-center mb-3">
                <img src="assets/images/iconlogin.png" alt="CTAutoFashion Icon" class="logo-login me-3">
                <h2 class="mb-0">Login Sistem</h2>
            </div>
            <hr class="login-separator">
        </div>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle icon"></i>
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle icon"></i>
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/login">
            <div class="form-group">
                <label for="userid" class="form-label-login">
                    <i class="fas fa-user icon"></i>User ID
                </label>
                <input type="text" 
                       class="form-control form-control-login" 
                       id="userid" 
                       name="userid" 
                       placeholder="Masukkan User ID"
                       value="<?php echo isset($_POST['userid']) ? htmlspecialchars($_POST['userid']) : ''; ?>"
                       required>
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label-login">
                    <i class="fas fa-lock icon"></i>Password
                </label>
                <div class="password-input-group">
                    <input type="password" 
                           class="form-control form-control-login" 
                           id="password" 
                           name="password" 
                           placeholder="Masukkan Password"
                           required>
                    <button type="button" class="password-toggle-btn" onclick="togglePassword()">
                        <i class="fas fa-eye" id="password-icon"></i>
                    </button>
                </div>
            </div>
            
            <button type="submit" class="btn btn-login">
                <i class="fas fa-right-to-bracket icon"></i>Login
            </button>
        </form>
        
        <div class="text-center mt-3">
            <small class="text-muted">
            <i class="fa-solid fa-circle-info"></i>
                <!-- <i class="fas fa-info-circle icon"></i> -->
                Selamat Datang!, Silahkan Login untuk melanjutkan
            </small>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const passwordIcon = document.getElementById('password-icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        passwordIcon.classList.remove('fa-eye');
        passwordIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        passwordIcon.classList.remove('fa-eye-slash');
        passwordIcon.classList.add('fa-eye');
    }
}
</script>
