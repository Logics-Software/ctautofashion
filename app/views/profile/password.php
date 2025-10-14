<?php
$title = 'Ganti Password';
?>

<div class="container">
    <div class="main-container fade-in">
        <div class="row me-0">
            <div class="col-12">
                <h2>Ganti Password</h2>
            </div>
        </div>
        
        <hr>

        <div class="row justify-content-center">
            <div class="col-md-12">
                <!-- Change Password Form -->
                <form method="POST" action="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/profile/change-password">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="current_password" class="form-label">
                                    <i class="fas fa-lock icon"></i>Password Lama <span class="text-danger">*</span>
                                </label>
                                <div class="password-input-group">
                                    <input type="password" 
                                        class="form-control" 
                                        id="current_password" 
                                        name="current_password" 
                                        placeholder="Masukkan password lama"
                                        required>
                                    <button type="button" class="password-toggle-btn" onclick="togglePassword('current_password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="new_password" class="form-label">
                                    <i class="fas fa-key icon"></i>Password Baru <span class="text-danger">*</span>
                                </label>
                                <div class="password-input-group">
                                    <input type="password" 
                                        class="form-control" 
                                        id="new_password" 
                                        name="new_password" 
                                        placeholder="Masukkan password baru"
                                        required>
                                    <button type="button" class="password-toggle-btn" onclick="togglePassword('new_password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Confirm Password Section -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">
                                    <i class="fas fa-check-circle icon"></i>Konfirmasi Password Baru <span class="text-danger">*</span>
                                </label>
                                <div class="password-input-group">
                                    <input type="password" 
                                        class="form-control" 
                                        id="confirm_password" 
                                        name="confirm_password" 
                                        placeholder="Konfirmasi password baru"
                                        required>
                                    <button type="button" class="password-toggle-btn" onclick="togglePassword('confirm_password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Password Strength</label>
                                <div class="password-strength-indicator">
                                    <div class="strength-bar">
                                        <div class="strength-fill" id="strength-fill"></div>
                                    </div>
                                    <div class="strength-text" id="strength-text">Masukkan password baru</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/dashboard" class="btn btn-secondary">
                            <i class="fas fa-times icon"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save icon"></i>Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Password Toggle Function
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const toggleBtn = field.nextElementSibling;
    const icon = toggleBtn.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Password Strength Checker
document.getElementById('new_password').addEventListener('input', function() {
    const password = this.value;
    const strengthFill = document.getElementById('strength-fill');
    const strengthText = document.getElementById('strength-text');
    
    let strength = 0;
    let strengthLabel = '';
    let strengthColor = '';
    
    // Length check
    if (password.length >= 6) strength++;
    if (password.length >= 8) strength++;
    
    // Character variety check
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    // Determine strength level
    if (strength <= 2) {
        strengthLabel = 'Lemah';
        strengthColor = '#dc3545';
    } else if (strength <= 4) {
        strengthLabel = 'Sedang';
        strengthColor = '#ffc107';
    } else {
        strengthLabel = 'Kuat';
        strengthColor = '#28a745';
    }
    
    // Update UI
    strengthFill.style.width = (strength * 20) + '%';
    strengthFill.style.backgroundColor = strengthColor;
    strengthText.textContent = strengthLabel;
    strengthText.style.color = strengthColor;
});

// Password Confirmation Check
document.getElementById('confirm_password').addEventListener('input', function() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = this.value;
    
    if (confirmPassword && newPassword !== confirmPassword) {
        this.setCustomValidity('Password tidak cocok');
    } else {
        this.setCustomValidity('');
    }
});
</script>
