<?php
// Check if user is logged in to determine header type
$is_logged_in = isset($_SESSION['user_id']);
$user_name = '';
$user_id = '';
$profile_photo = '';

if ($is_logged_in) {
    $user_id = $_SESSION['user_id'];
    $user_data = $_SESSION['user_data'] ?? [];
    $user_name = $user_data['UserName'] ?? $user_data['Name'] ?? $user_data['user_name'] ?? $user_id;
    
    // Get profile photo
    require_once 'app/models/ProfileModel.php';
    $profileModel = new ProfileModel();
    $profile_photo = $profileModel->getProfilePhoto($user_id);
}
?>

<header class="main-header">
    <?php if ($is_logged_in): ?>
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-8 col-md-8">
                        <div class="d-flex align-items-center">
                            <!-- Hamburger Menu Button -->
                            <button class="hamburger-menu-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
                                <span class="hamburger-line"></span>
                                <span class="hamburger-line"></span>
                                <span class="hamburger-line"></span>
                            </button>
                            
                            <!-- Logo -->
                            <a href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/dashboard" class="logo-link ms-3">
                                <!-- Desktop Logo -->
                                <img src="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/assets/images/logo.png" alt="CTAutoFashion Logo" class="logo logo-desktop">
                                <!-- Mobile Logo -->
                                <img src="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/assets/images/icon.png" alt="CTAutoFashion Icon" class="logo logo-mobile">
                            </a>
                        </div>
                    </div>
                    <div class="col-4 col-md-4 text-end">
                        <div class="profile-dropdown">
                            <button class="btn btn-profile dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php if ($profile_photo && file_exists(BASE_PATH . '/' . $profile_photo)): ?>
                                    <img src="<?php echo dirname($_SERVER['SCRIPT_NAME']) . '/' . htmlspecialchars($profile_photo); ?>" 
                                         alt="Profile Photo" 
                                         class="profile-photo-header me-2">
                                <?php else: ?>
                                    <i class="fas fa-user-circle me-2"></i>
                                <?php endif; ?>
                                <span class="username username-text"><?php echo htmlspecialchars($user_name); ?></span>
                                <i class="fas fa-angle-down ms-2"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/profile">
                                        <i class="fas fa-user me-2"></i>Profil Saya
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/profile/password">
                                        <i class="fas fa-key me-2"></i>Ganti Password
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/logout">
                                        <i class="fas fa-sign-out me-2"></i>Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar Navigation -->
        <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="sidebarMenuLabel">
                    <i class="fas fa-bars me-2"></i>Menu
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <nav class="sidebar-nav">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link sidebar-link" href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/dashboard">
                            <i class="fa-solid fa-gauge  me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <hr class="my-3">
                        </li>
                        <li class="nav-item">
                            <a class="nav-link sidebar-link" href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/profile">
                                <i class="fas fa-list-check me-2"></i>Buat Work Order Baru
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link sidebar-link" href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/processworkorder">
                                <i class="fa-solid fa-gears me-2"></i>Proses Work Order
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link sidebar-link" href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/workorder">
                                <i class="fa-solid fa-clipboard-list me-2"></i>Data Work Order
                            </a>
                        </li>
                        <li class="nav-item">
                            <hr class="my-3">
                        </li>
                        <li class="nav-item">
                            <a class="nav-link sidebar-link" href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/products">
                                <i class="fa-solid fa-money-bill-trend-up me-2"></i>Informasi Stok & Harga
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link sidebar-link" href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/service">
                                <i class="fa-solid fa-wrench me-2"></i>Informasi Service Customer
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link sidebar-link" href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/vehicle">
                                <i class="fa-solid fa-car me-2"></i>Informasi Service Kendaraan
                            </a>
                        </li>
                        <li class="nav-item">
                            <hr class="my-3">
                        </li>
                        <li class="nav-item">
                            <a class="nav-link sidebar-link text-danger" href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/logout">
                                <i class="fas fa-sign-out me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    <?php else: ?>
        <!-- No header for login page -->
    <?php endif; ?>
</header>

