<?php
$title = 'Dashboard';
?>

<div class="container">
    
    <!-- Success Message -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle icon"></i>
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Main Dashboard Content -->
    <div class="main-container fade-in">
        <div class="row">
            <div class="col-12">
                <h4><i class="fa-solid fa-gauge me-2"></i></i>Dashboard</h4>
            </div>
        </div>
        
        <hr>

        <!-- Dashboard Cards -->
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body card-hover-success-add text-center">
                        <i class="fas fa-truck fa-2x text-primary mb-3 mt-3"></i>
                        <h6 class="card-title fw-bold mb-2">Work Order Baru</h6>
                        <a href="#" class="btn btn-primary  mb-3">Buat Order</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fa-solid fa-list-check fa-2x text-primary mb-3"></i>
                        <h6 class="card-title">Total Semua Work Order</h6>
                        <p class="card-text text-muted fw-bold">110 WO</p>
                        <a href="#" class="btn btn-primary btn-sm">Tampilkan Semua</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-solid fa-list-check fa-2x text-success mb-3"></i>
                        <h6 class="card-title">Work Order Belum Proses</h6>
                        <p class="card-text text-muted fw-bold">15 WO</p>
                        <a href="#" class="btn btn-success btn-sm">Tampilkan Semua</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-list-check fa-2x text-info mb-3"></i>
                        <h6 class="card-title">Work Order Dalam Proses</h6>
                        <p class="card-text text-muted fw-bold">20 WO</p>
                        <a href="#" class="btn btn-info btn-sm">Tampilkan Semua</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-list-check fa-2x text-warning mb-3"></i>
                        <h6 class="card-title">Work Order Telah Selesai</h6>
                        <p class="card-text text-muted fw-bold">30 WO</p>
                        <a href="#" class="btn btn-warning btn-sm">Tampilkan Semua</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-list-check fa-2x text-dark mb-3"></i>
                        <h6 class="card-title">Work Order Faktur Dibuat</h6>
                        <p class="card-text text-muted fw-bold">21 WO</p>
                        <a href="#" class="btn btn-dark btn-sm">Tampilkan Semua</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-list-check fa-2x text-danger mb-3"></i>
                        <h6 class="card-title">Work Order Telah Selesai</h6>
                        <p class="card-text text-muted fw-bold">18 WO</p>
                        <a href="#" class="btn btn-danger btn-sm">Tampilkan Semua</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-list-check fa-2x text-secondary mb-3"></i>
                        <h6 class="card-title">Work Order Dibatalkan</h6>
                        <p class="card-text text-muted fw-bold">5 WO</p>
                        <a href="#" class="btn btn-secondary btn-sm">Tampilkan Semua</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="row mt-4">
            <div class="col-12">
                <h4><i class="fas fa-bolt me-2 text-warning"></i>Informasi</h4>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body card-hover-warning text-center">
                        <i class="fas fa-chart-line fa-3x text-warning mb-3"></i>
                        <h6 class="card-title fw-bold">Informasi Daftar Harga dan Stok Barang</h6>
                        <p class="card-text text-muted small">Lihat informasi harga dan stok barang terbaru</p>
                        <a href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/products" class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-eye me-1"></i>Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body card-hover-primary text-center">
                        <i class="fas fa-wrench fa-3x text-primary mb-3"></i>
                        <h6 class="card-title fw-bold">Informasi Transaksi Order Customer</h6>
                        <p class="card-text text-muted small">Lihat informasi lengkap tentang service dan invoice yang pernah dilakukan berdasarkan customer</p>
                        <a href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/service" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body card-hover-success text-center">
                        <i class="fas fa-users fa-3x text-success mb-3"></i>
                        <h6 class="card-title fw-bold">Informasi Transaksi Order Kendaraan</h6>
                        <p class="card-text text-muted small">Lihat informasi lengkap tentang service dan invoice yang pernah dilakukan berdasarkan kendaraan</p>
                        <a href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/vehicle" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-eye me-1"></i>Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Welcome Message -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle icon"></i>Selamat Datang!</h6>
                    <p class="mb-0">
                        Anda telah berhasil login ke sistem CTAutoFashion Management System. 
                        Menu dan dashboard saat ini masih dalam pengembangan. 
                        Silakan gunakan menu navigasi di atas untuk mengakses fitur-fitur yang tersedia.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
