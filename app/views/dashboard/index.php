<?php
$title = 'Dashboard';
?>

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

        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body card-hover-warning text-center">
                        <i class="fas fa-briefcase fa-3x text-warning mb-3"></i>
                        <h5 class="card-title fw-bold">Input Data Work Order Baru</h5>
                        <a href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/transaksi-work-order" class="btn btn-warning btn">
                            <i class="fas fa-edit me-2"></i>Buat Order
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body card-hover-primary text-center">
                        <i class="fas fa-wrench fa-3x text-primary mb-3"></i>
                        <h5 class="card-title fw-bold">Proses Data Work Order</h5>
                        <a href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/processworkorder" class="btn btn-primary btn">
                            <i class="fas fa-gear me-2"></i>Proses Order
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body card-hover-success text-center">
                        <i class="fas fa-list-check fa-3x text-success mb-3"></i>
                        <h5 class="card-title fw-bold">Informasi Data Work Order</h5>
                        <a href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/workorder" class="btn btn-success btn">
                            <i class="fas fa-clipboard-check me-2"></i>Informasi Data Work Order
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <?php 
        // Only show charts if TipeUser >= 2 (excluding 0 and 1)
        $showCharts = !isset($tipe_user) || ((int)$tipe_user >= 2);
        if ($showCharts): 
        ?>
        <!-- Chart Section - Work Order Statistics -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Statistik Work Order - 12 Bulan Terakhir</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="workOrderChart" style="width: 100%; height: 400px;"></canvas>
                    </div>
                    </div>
                </div>
            </div>

        <!-- Chart Section - Revenue Statistics -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fa-solid fa-chart-simple me-2"></i>Statistik Penjualan (Rupiah) - 12 Bulan Terakhir</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueChart" style="width: 100%; height: 400px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
            
        <!-- Order Statistics Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <h5 class="mb-0">
                                    <i class="fas fa-clipboard-list me-2"></i>Informasi Work Order
                                </h5>
                            </div>
                            <div class="col-md-8">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <select id="periodFilter" class="form-select form-select-sm">
                                            <option value="today" selected>Hari Ini</option>
                                            <option value="yesterday">Kemarin</option>
                                            <option value="this_week">Minggu Ini</option>
                                            <option value="this_month">Bulan Ini</option>
                                            <option value="this_year">Tahun Ini</option>
                                            <option value="custom">Custom</option>
                                        </select>
                                    </div>
                                    <div class="col-md-8" id="customDateContainer" style="display: none;">
                                        <div class="input-group input-group-sm">
                                            <input type="date" id="startDate" class="form-control form-control-sm">
                                            <span class="input-group-text">-</span>
                                            <input type="date" id="endDate" class="form-control form-control-sm">
                                        </div>
                    </div>
                </div>
            </div>
                        </div>
                    </div>
                    <div class="card-body" id="orderStatsContainer">
                        <div class="row">
                            <!-- Total All Orders -->
                            <div class="col-md-12 mb-3">
                                <div class="alert alert-info mb-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 text-white"><i class="fas fa-clipboard-check me-2"></i>Total Semua Work Order</h6>
                </div>
                                        <div>
                                            <h3 class="mb-0 text-white fw-bold"><?php echo $orderStats['total'] ?? 0; ?> WO</h3>
            </div>
                    </div>
                </div>
            </div>

                            <!-- Status Details -->
                            <?php if (isset($orderStats['statistics'])): ?>
                                <?php foreach ($orderStats['statistics'] as $statusCode => $data): ?>
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card border-<?php echo $data['color']; ?> h-100">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h6 class="card-title text-<?php echo $data['color']; ?> mb-2">
                                                            <i class="fas fa-circle me-2"></i><?php echo $data['status']; ?>
                                                        </h6>
                                                        <p class="card-text text-muted mb-0 small"></p>
                    </div>
                                                    <div class="text-end">
                                                        <h2 class="mb-0 text-<?php echo $data['color']; ?> fw-bold"><?php echo $data['count']; ?></h2>
                                                        <small class="text-muted">Work Order</small>
                </div>
            </div>

                                                <!-- Progress Bar -->
                                                <?php 
                                                    $percentage = $orderStats['total'] > 0 ? ($data['count'] / $orderStats['total']) * 100 : 0;
                                                ?>
                                                <div class="progress mt-3" style="height: 8px;">
                                                    <div class="progress-bar bg-<?php echo $data['color']; ?>" 
                                                         role="progressbar" 
                                                         style="width: <?php echo $percentage; ?>%;" 
                                                         aria-valuenow="<?php echo $percentage; ?>" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <small class="text-muted mt-1 d-block text-end"><?php echo number_format($percentage, 1); ?>% dari total</small>
                    </div>
                </div>
            </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="row mt-4">
            <div class="col-12">
                <!-- <i class="fa-solid fa-money-check-dollar"></i> -->
                <h5><i class="fas fa-bolt me-2"></i>Informasi Harga, Stok, dan Transaksi</h5>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body card-hover-warning text-center">
                        <i class="fa-solid fa-money-check-dollar fa-3x text-warning mb-3"></i>
                        <h6 class="">Informasi Harga dan Stok Barang</h6>
                        <p class="card-text text-muted small">Lihat informasi lengkap tentang harga jual dan stok barang terbaru<br/><br/></p>
                        <a href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/products" class="btn btn-warning btn-sm">
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
                        <a href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/service" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body card-hover-success text-center">
                        <i class="fa-solid fa-car-side fa-3x text-success mb-3"></i>
                        <h6 class="card-title fw-bold">Informasi Transaksi Order Kendaraan</h6>
                        <p class="card-text text-muted small">Lihat informasi lengkap tentang service dan invoice yang pernah dilakukan berdasarkan kendaraan</p>
                        <a href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/vehicle" class="btn btn-success btn-sm">
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
                        Anda telah berhasil login ke sistem CT AutoFashion Management System. 
                    </p>
                </div>
            </div>
        </div>
    </div>


<script>
// Main Dashboard Script
document.addEventListener('DOMContentLoaded', function() {
    // ===== Order Statistics Filter =====
    const periodFilter = document.getElementById('periodFilter');
    const customDateContainer = document.getElementById('customDateContainer');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const orderStatsContainer = document.getElementById('orderStatsContainer');
    
    // Function to render order statistics
    function renderOrderStats(data) {
        if (!data) {
            orderStatsContainer.innerHTML = '<div class="alert alert-danger">Error: Data tidak valid</div>';
            return;
        }
        
        if (!data.statistics) {
            orderStatsContainer.innerHTML = '<div class="alert alert-warning">Tidak ada data statistik tersedia</div>';
            return;
        }
        
        let html = '<div class="row">';
        
        // Total All Orders - Always render this first
        const totalValue = data.total || 0;
        
        html += `
            <div class="col-md-12 mb-3">
                <div class="alert alert-info mb-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1 text-white"><i class="fas fa-clipboard-check me-2"></i>Total Semua Work Order</h6>
                        </div>
                        <div>
                            <h3 class="mb-0 text-white fw-bold">${totalValue} WO</h3>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Status Details
        const statistics = data.statistics;
        
        // Debug: Log statistics to console
        console.log('Statistics received:', statistics);
        console.log('Statistics type:', typeof statistics, 'IsArray:', Array.isArray(statistics));
        
        // Handle both object and array format
        if (statistics) {
            // Define the correct order of statuses
            const statusOrder = ['0', '1', '2', '3', '4', '5'];
            
            // Iterate in the correct order
            for (const statusCode of statusOrder) {
                const statusData = statistics[statusCode];
                
                if (!statusData || typeof statusData !== 'object') {
                    continue;
                }
                
                // Debug: Log each status mapping
                console.log(`Rendering status ${statusCode}:`, statusData);
                
                const percentage = data.total > 0 ? (statusData.count / data.total * 100).toFixed(1) : 0;
                
                html += `
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card border-${statusData.color} h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-${statusData.color} mb-2">
                                            <i class="fas fa-circle me-2"></i>${statusData.status}
                                        </h6>
                                        <p class="card-text text-muted mb-0 small">Status Order: ${statusCode}</p>
                                    </div>
                                    <div class="text-end">
                                        <h2 class="mb-0 text-${statusData.color} fw-bold">${statusData.count}</h2>
                                        <small class="text-muted">Work Order</small>
    </div>
</div>
                                
                                <!-- Progress Bar -->
                                <div class="progress mt-3" style="height: 8px;">
                                    <div class="progress-bar bg-${statusData.color}" 
                                         role="progressbar" 
                                         style="width: ${percentage}%;" 
                                         aria-valuenow="${percentage}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <small class="text-muted mt-1 d-block text-end">${percentage}% dari total</small>
                            </div>
                        </div>
                    </div>
                `;
            }
        }
        
        html += '</div>';
        orderStatsContainer.innerHTML = html;
    }
    
    // Function to load order statistics
    function loadOrderStats() {
        if (!periodFilter || !orderStatsContainer) return;
        
        const period = periodFilter.value;
        const startDate = startDateInput ? startDateInput.value : '';
        const endDate = endDateInput ? endDateInput.value : '';
        
        // Validate custom dates
        if (period === 'custom' && (!startDate || !endDate)) {
            alert('Silakan pilih tanggal mulai dan tanggal akhir');
            return;
        }
        
        // Show loading
        orderStatsContainer.innerHTML = '<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-3x text-primary"></i><p class="mt-3">Memuat data...</p></div>';
        
        // Prepare form data
        const formData = new FormData();
        formData.append('period', period);
        if (period === 'custom') {
            formData.append('start_date', startDate);
            formData.append('end_date', endDate);
        }
        
        // AJAX request
        fetch('<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/dashboard/getOrderStats', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            renderOrderStats(data);
        })
        .catch(error => {
            console.error('Error loading order stats:', error);
            orderStatsContainer.innerHTML = '<div class="alert alert-danger">Gagal memuat data. Silakan coba lagi.</div>';
        });
    }
    
    // Event listeners for filter
    if (periodFilter) {
        periodFilter.addEventListener('change', function() {
            if (this.value === 'custom') {
                if (customDateContainer) customDateContainer.style.display = 'block';
            } else {
                if (customDateContainer) customDateContainer.style.display = 'none';
                loadOrderStats();
            }
        });
    }
    
    if (startDateInput) {
        startDateInput.addEventListener('change', function() {
            if (periodFilter.value === 'custom' && startDateInput.value && endDateInput.value) {
                loadOrderStats();
            }
        });
    }
    
    if (endDateInput) {
        endDateInput.addEventListener('change', function() {
            if (periodFilter.value === 'custom' && startDateInput.value && endDateInput.value) {
                loadOrderStats();
            }
        });
    }
    
    // ===== Chart.js - Work Order Statistics =====
    const ctx = document.getElementById('workOrderChart');
    
    if (ctx) {
        // Get data from PHP
        const chartData = <?php echo json_encode($chartData ?? []); ?>;
        
        // Debug: log chartData
        console.log('Chart Data:', chartData);
        
        // Check if chartData is empty or null
        if (!chartData || Object.keys(chartData).length === 0 || !chartData.months || chartData.months.length === 0) {
            // Show message instead of empty chart
            const parent = ctx.parentElement;
            parent.innerHTML = '<div class="alert alert-info text-center my-5"><i class="fas fa-info-circle me-2"></i>Belum ada data statistik untuk 12 bulan terakhir</div>';
            console.warn('Chart data is empty or invalid');
        } else {
            // Create chart
            new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.months || [],
                datasets: [
                    {
                        label: 'Total Work Order',
                        data: chartData.totalOrders || [],
                        borderColor: 'rgb(54, 162, 235)',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: 'rgb(54, 162, 235)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    },
                    {
                        label: 'Work Order Selesai',
                        data: chartData.completedOrders || [],
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: 'rgb(75, 192, 192)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    },
                    {
                        label: 'Work Order Dibatalkan',
                        data: chartData.canceledOrders || [],
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: 'rgb(255, 99, 132)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 13,
                                weight: '600'
                            },
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    title: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.parsed.y + ' WO';
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        title: {
                            display: true,
                            text: 'Jumlah Work Order',
                            font: {
                                size: 13,
                                weight: '600'
                            }
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Bulan',
                            font: {
                                size: 13,
                                weight: '600'
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
        }
    }
    
    // ===== Revenue Chart =====
    const ctxRevenue = document.getElementById('revenueChart');
    
    if (ctxRevenue) {
        // Get data from PHP
        const revenueData = <?php echo json_encode($revenueData ?? []); ?>;
        
        // Debug: log revenueData
        console.log('Revenue Data:', revenueData);
        
        // Check if revenueData is empty or null
        if (!revenueData || Object.keys(revenueData).length === 0 || !revenueData.months || revenueData.months.length === 0) {
            // Show message instead of empty chart
            const parent = ctxRevenue.parentElement;
            parent.innerHTML = '<div class="alert alert-info text-center my-5"><i class="fas fa-info-circle me-2"></i>Belum ada data penjualan untuk 12 bulan terakhir</div>';
            console.warn('Revenue data is empty or invalid');
        } else {
        
        // Function to format currency
        const formatRupiah = (amount) => {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        };
        
        // Create chart
        new Chart(ctxRevenue, {
            type: 'bar',
            data: {
                labels: revenueData.months || [],
                datasets: [
                    {
                        label: 'Total Penjualan',
                        data: revenueData.totalRevenue || [],
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgb(54, 162, 235)',
                        borderWidth: 2,
                        borderRadius: 5,
                        barThickness: 'flex',
                        maxBarThickness: 60
                    },
                    {
                        label: 'Penjualan Customer Perorangan',
                        data: revenueData.peroranganRevenue || [],
                        backgroundColor: 'rgba(255, 159, 64, 0.7)',
                        borderColor: 'rgb(255, 159, 64)',
                        borderWidth: 2,
                        borderRadius: 5,
                        barThickness: 'flex',
                        maxBarThickness: 60
                    },
                    {
                        label: 'Penjualan Customer Perusahaan',
                        data: revenueData.perusahaanRevenue || [],
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgb(75, 192, 192)',
                        borderWidth: 2,
                        borderRadius: 5,
                        barThickness: 'flex',
                        maxBarThickness: 60
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 13,
                                weight: '600'
                            },
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'rectRounded'
                        }
                    },
                    title: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += formatRupiah(context.parsed.y);
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: {
                                size: 11
                            },
                            callback: function(value) {
                                // Format in millions for better readability
                                if (value >= 1000000) {
                                    return 'Rp ' + (value / 1000000).toFixed(1) + ' Jt';
                                } else if (value >= 1000) {
                                    return 'Rp ' + (value / 1000).toFixed(0) + ' Rb';
                                }
                                return 'Rp ' + value;
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        title: {
                            display: true,
                            text: 'Total Penjualan (Rupiah)',
                            font: {
                                size: 13,
                                weight: '600'
                            }
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Bulan',
                            font: {
                                size: 13,
                                weight: '600'
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
        }
    }
});
</script>
