<?php
$title = 'Informasi Transaksi Work Order';
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
    
    <!-- Error Message -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle icon"></i>
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Main Content -->
    <div class="main-container fade-in">
        <div class="row">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="fa-solid fa-clipboard-list me-2"></i>Informasi Transaksi Work Order</h4>
                    <button type="button" class="btn" onclick="window.location.href='<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/dashboard'" title="Kembali" style="vertical-align: middle;">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <hr>
        
        
        <!-- Search and Filter Section -->
        <div class="row mb-2">
            <div class="col-md-8">
                <form method="GET" action="" class="d-flex">
                    <div class="input-group">
                        <input type="text" 
                               class="form-control" 
                               name="search" 
                               value="<?php echo htmlspecialchars($filters['search']); ?>" 
                               placeholder="Cari berdasarkan No Order, Customer, No Polisi, atau Nama Kendaraan..."
                               aria-label="Search">
                        <button class="btn btn-sm btn-outline-primary" type="submit">
                            <i class="fas fa-search"></i> Cari
                        </button>
                        <?php if (!empty($filters['search']) || !empty($filters['status']) || !empty($filters['customer']) || !empty($filters['no_polisi'])): ?>
                            <a href="?" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Reset
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            <div class="col-md-4">
                <form method="GET" action="" class="d-flex">
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($filters['search']); ?>">
                    <input type="hidden" name="status" value="<?php echo htmlspecialchars($filters['status']); ?>">
                    <input type="hidden" name="customer" value="<?php echo htmlspecialchars($filters['customer']); ?>">
                    <input type="hidden" name="no_polisi" value="<?php echo htmlspecialchars($filters['no_polisi']); ?>">
                    <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($filters['start_date']); ?>">
                    <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($filters['end_date']); ?>">
                    <select name="limit" class="form-select" onchange="this.form.submit()">
                        <?php foreach ($paginationOptions as $option): ?>
                            <option value="<?php echo $option; ?>" <?php echo $limit == $option ? 'selected' : ''; ?>>
                                <?php echo $option; ?> per halaman
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>
        
        <!-- Filter Section -->
        <div class="row mb-4">
            <div class="col-12">
                <form method="GET" action="" id="filterForm">
                    <div class="row g-3">
                        <!-- Date Filter -->
                        <div class="col-md-6">
                            <label for="date_range" class="form-label">Periode</label>
                            <select name="date_range" id="date_range" class="form-select" onchange="updateDateRange()">
                                <?php
                                // Get the selected period from URL parameter
                                $selectedPeriod = $_GET['date_range'] ?? 'today';
                                ?>
                                <option value="today" <?php echo $selectedPeriod == 'today' ? 'selected' : ''; ?>>Hari ini</option>
                                <option value="yesterday" <?php echo $selectedPeriod == 'yesterday' ? 'selected' : ''; ?>>Kemarin</option>
                                <option value="this_week" <?php echo $selectedPeriod == 'this_week' ? 'selected' : ''; ?>>Minggu ini</option>
                                <option value="this_month" <?php echo $selectedPeriod == 'this_month' ? 'selected' : ''; ?>>Bulan ini</option>
                                <option value="last_month" <?php echo $selectedPeriod == 'last_month' ? 'selected' : ''; ?>>Bulan lalu</option>
                                <option value="this_year" <?php echo $selectedPeriod == 'this_year' ? 'selected' : ''; ?>>Tahun ini</option>
                                <option value="custom" <?php echo $selectedPeriod == 'custom' ? 'selected' : ''; ?>>Custom</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6" id="custom_date_range" style="<?php echo $selectedPeriod == 'custom' ? 'display: block;' : 'display: none;'; ?>">
                            <label class="form-label">Tanggal Custom</label>
                            <div class="row col-12">
                                <div class="col-6">
                                    <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($filters['start_date']); ?>" onchange="submitCustomDate()">
                                </div>
                                <div class="col-6">
                                    <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($filters['end_date']); ?>" onchange="submitCustomDate()">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Status Filter -->
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status Order</label>
                            <select name="status" id="status" class="form-select" onchange="submitFilter()">
                                <option value="">Semua Status</option>
                                <?php foreach ($statusOptions as $value => $label): ?>
                                    <option value="<?php echo $value; ?>" <?php echo $filters['status'] == $value ? 'selected' : ''; ?>>
                                        <?php echo $label; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Customer Filter -->
                        <div class="col-md-4">
                            <label for="customer" class="form-label">Customer</label>
                            <div class="custom-dropdown" id="customerDropdown">
                                <div class="dropdown-trigger" onclick="toggleDropdown('customer')">
                                    <input type="text" 
                                           id="customer" 
                                           class="form-select dropdown-input" 
                                           placeholder="Pilih Customer..."
                                           value="<?php echo !empty($filters['customer']) ? htmlspecialchars(array_values(array_filter($customers, fn($c) => $c['KodeCustomer'] == $filters['customer']))[0]['NamaCustomer'] ?? '') : ''; ?>"
                                           readonly
                                           autocomplete="off">
                                    <span class="dropdown-caret" id="customerCaret">
                                        <i class="fas fa-chevron-down"></i>
                                    </span>
                                </div>
                                <div class="dropdown-content" id="customerContent">
                                    <div class="dropdown-search">
                                        <input type="text" 
                                               id="customerSearch" 
                                               placeholder="Cari customer..."
                                               autocomplete="off">
                                    </div>
                                    <div class="dropdown-list" id="customerList">
                                        <div class="dropdown-item" data-code="" onclick="selectOption('customer', '', 'SEMUA')">SEMUA</div>
                                        <?php foreach ($customers as $customer): ?>
                                            <div class="dropdown-item" 
                                                 data-code="<?php echo htmlspecialchars($customer['KodeCustomer']); ?>"
                                                 onclick="selectOption('customer', '<?php echo htmlspecialchars($customer['KodeCustomer']); ?>', '<?php echo htmlspecialchars($customer['NamaCustomer']); ?>')">
                                                <?php echo htmlspecialchars($customer['NamaCustomer']); ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="customer" id="customer_code" value="<?php echo htmlspecialchars($filters['customer']); ?>">
                        </div>
                        
                        <!-- Vehicle Filter -->
                        <div class="col-md-4">
                            <label for="no_polisi" class="form-label">No Polisi</label>
                            <div class="custom-dropdown" id="vehicleDropdown">
                                <div class="dropdown-trigger" onclick="toggleDropdown('vehicle')">
                                    <input type="text" 
                                           id="no_polisi" 
                                           class="form-select dropdown-input" 
                                           placeholder="Pilih Kendaraan..."
                                           value="<?php echo !empty($filters['no_polisi']) ? htmlspecialchars($filters['no_polisi']) : ''; ?>"
                                           readonly
                                           autocomplete="off">
                                    <span class="dropdown-caret" id="vehicleCaret">
                                        <i class="fas fa-chevron-down"></i>
                                    </span>
                                </div>
                                <div class="dropdown-content" id="vehicleContent">
                                    <div class="dropdown-search">
                                        <input type="text" 
                                               id="vehicleSearch" 
                                               placeholder="Cari kendaraan..."
                                               autocomplete="off">
                                    </div>
                                    <div class="dropdown-list" id="vehicleList">
                                        <div class="dropdown-item" data-code="" onclick="selectOption('vehicle', '', 'SEMUA')">SEMUA</div>
                                        <?php foreach ($vehicles as $vehicle): ?>
                                            <div class="dropdown-item" 
                                                 data-code="<?php echo htmlspecialchars($vehicle['NoPolisi']); ?>"
                                                 onclick="selectOption('vehicle', '<?php echo htmlspecialchars($vehicle['NoPolisi']); ?>', '<?php echo htmlspecialchars($vehicle['NoPolisi'] . ' - ' . $vehicle['NamaKendaraan']); ?>')">
                                                <?php echo htmlspecialchars($vehicle['NoPolisi'] . ' - ' . $vehicle['NamaKendaraan']); ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="no_polisi" id="no_polisi_code" value="<?php echo htmlspecialchars($filters['no_polisi']); ?>">
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="button" class="btn btn-outline-secondary" onclick="resetFilters();">
                                    <i class="fas fa-filter-circle-xmark"></i> Reset Filter
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hidden inputs to preserve search and other parameters -->
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($filters['search']); ?>">
                    <input type="hidden" name="limit" value="<?php echo htmlspecialchars($limit); ?>">
                </form>
            </div>
        </div>
        
        <!-- Results Info -->
        <div class="row mb-0">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Menampilkan <?php echo count($workOrders); ?> dari <?php echo number_format($totalWorkOrders); ?> data
                    <br><small>Halaman <?php echo $page; ?> dari <?php echo $totalPages; ?> halaman (Limit: <?php echo $limit; ?>, Offset: <?php echo ($page - 1) * $limit; ?>)</small>
                    <br><small>Debug: Total Records = <?php echo $totalWorkOrders; ?>, Current Page = <?php echo $page; ?>, Records per page = <?php echo $limit; ?></small>
                    <?php if (!empty($filters['search'])): ?>
                        untuk pencarian "<strong><?php echo htmlspecialchars($filters['search']); ?></strong>"
                    <?php endif; ?>
                    
                    <?php 
                    $activeFilters = [];
                    
                    // Show date range filter
                    $startDate = $filters['start_date'] ?? date('Y-m-d');
                    $endDate = $filters['end_date'] ?? date('Y-m-d');
                    if ($startDate == $endDate) {
                        $activeFilters[] = "Tanggal: <strong>" . date('d/m/Y', strtotime($startDate)) . "</strong>";
                    } else {
                        $activeFilters[] = "Periode: <strong>" . date('d/m/Y', strtotime($startDate)) . " - " . date('d/m/Y', strtotime($endDate)) . "</strong>";
                    }
                    
                    if (!empty($filters['status'])) {
                        $activeFilters[] = "Status: <strong>" . htmlspecialchars($statusOptions[$filters['status']]) . "</strong>";
                    }
                    if (!empty($filters['customer'])) {
                        $customerName = '';
                        foreach ($customers as $customer) {
                            if ($customer['KodeCustomer'] == $filters['customer']) {
                                $customerName = $customer['NamaCustomer'];
                                break;
                            }
                        }
                        $activeFilters[] = "Customer: <strong>" . htmlspecialchars($customerName) . "</strong>";
                    }
                    if (!empty($filters['no_polisi'])) {
                        $activeFilters[] = "No Polisi: <strong>" . htmlspecialchars($filters['no_polisi']) . "</strong>";
                    }
                    
                    if (!empty($activeFilters)): ?>
                        <br><small>Filter aktif: <?php echo implode(', ', $activeFilters); ?></small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Work Orders Table -->
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-striped table-hover main-table">
                        <thead class="table-dark">
                            <tr>
                                <th>No Order</th>
                                <th>Tanggal</th>
                                <th>Customer</th>
                                <th>Alamat</th>
                                <th>No Telepon</th>
                                <th>No Polisi</th>
                                <th>Kendaraan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($workOrders)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                        Tidak ada data yang ditemukan
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($workOrders as $workOrder): ?>
                                    <tr>
                                        <td class="data-utama">
                                            <strong><?php echo htmlspecialchars($workOrder['NoOrder']); ?></strong>
                                        </td>
                                        <td><?php 
                                            $tanggal = $workOrder['TanggalOrder'] ?? '';
                                            if (!empty($tanggal)) {
                                                echo date('d/m/Y', strtotime($tanggal));
                                            } else {
                                                echo 'N/A';
                                            }
                                        ?></td>
                                        <td><?php echo htmlspecialchars($workOrder['NamaCustomer'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($workOrder['AlamatCustomer'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($workOrder['NoTelepon'] ?? 'N/A'); ?></td>
                                        <td>
                                            <span class="badge bg-primary"><?php echo htmlspecialchars($workOrder['NoPolisi'] ?? 'N/A'); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($workOrder['NamaKendaraan'] ?? 'N/A'); ?></td>
                                        <td>
                                            <span class="badge <?php 
                                                switch($workOrder['StatusOrder'] ?? '0') {
                                                    case '0': echo 'bg-secondary'; break;
                                                    case '1': echo 'bg-warning'; break;
                                                    case '2': echo 'bg-info'; break;
                                                    case '3': echo 'bg-primary'; break;
                                                    case '4': echo 'bg-success'; break;
                                                    case '5': echo 'bg-danger'; break;
                                                    default: echo 'bg-secondary';
                                                }
                                            ?>">
                                                <?php echo htmlspecialchars($workOrder['StatusText'] ?? 'Tidak diketahui'); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <nav aria-label="Work Order pagination">
                        <ul class="pagination justify-content-center">
                            <!-- Previous Page -->
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">
                                        <i class="fas fa-chevron-left"></i> Sebelumnya
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <span class="page-link">
                                        <i class="fas fa-chevron-left"></i> Sebelumnya
                                    </span>
                                </li>
                            <?php endif; ?>
                            
                            <!-- Page Numbers -->
                            <?php
                            $startPage = max(1, $page - 2);
                            $endPage = min($totalPages, $page + 2);
                            
                            if ($startPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>">1</a>
                                </li>
                                <?php if ($startPage > 2): ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($endPage < $totalPages): ?>
                                <?php if ($endPage < $totalPages - 1): ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                <?php endif; ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $totalPages])); ?>">
                                        <?php echo $totalPages; ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <!-- Next Page -->
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">
                                        Selanjutnya <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <span class="page-link">
                                        Selanjutnya <i class="fas fa-chevron-right"></i>
                                    </span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Date range functionality
function updateDateRange() {
    const dateRange = document.getElementById('date_range').value;
    const customDateRange = document.getElementById('custom_date_range');
    const startDateInput = document.querySelector('input[name="start_date"]');
    const endDateInput = document.querySelector('input[name="end_date"]');
    
    if (dateRange === 'custom') {
        customDateRange.style.display = 'block';
        return;
    } else {
        customDateRange.style.display = 'none';
    }
    
    let startDate, endDate;
    const today = new Date();
    
    switch (dateRange) {
        case 'today':
            startDate = endDate = today.toISOString().split('T')[0];
            break;
        case 'yesterday':
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);
            startDate = endDate = yesterday.toISOString().split('T')[0];
            break;
        case 'this_week':
            const monday = new Date(today);
            monday.setDate(today.getDate() - today.getDay() + 1);
            const sunday = new Date(monday);
            sunday.setDate(monday.getDate() + 6);
            startDate = monday.toISOString().split('T')[0];
            endDate = sunday.toISOString().split('T')[0];
            break;
        case 'this_month':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
            endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0).toISOString().split('T')[0];
            break;
        case 'last_month':
            const firstDayLastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            const lastDayLastMonth = new Date(today.getFullYear(), today.getMonth(), 0);
            startDate = firstDayLastMonth.toISOString().split('T')[0];
            endDate = lastDayLastMonth.toISOString().split('T')[0];
            break;
        case 'this_year':
            startDate = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
            endDate = new Date(today.getFullYear(), 11, 31).toISOString().split('T')[0];
            break;
    }
    
    if (startDate && endDate) {
        startDateInput.value = startDate;
        endDateInput.value = endDate;
        
        // Submit form with all current parameters
        const form = document.getElementById('filterForm');
        const formData = new FormData(form);
        
        // Update the date values
        formData.set('start_date', startDate);
        formData.set('end_date', endDate);
        
        // Create URL with all parameters
        const params = new URLSearchParams();
        for (let [key, value] of formData.entries()) {
            if (value) {
                params.append(key, value);
            }
        }
        
        // Redirect to the same page with new parameters
        window.location.href = '?' + params.toString();
    }
}

// Custom Dropdown Functionality
let activeDropdown = null;

function toggleDropdown(type) {
    const dropdown = document.getElementById(type + 'Dropdown');
    const content = document.getElementById(type + 'Content');
    const caret = document.getElementById(type + 'Caret');
    
    // Close all other dropdowns
    closeAllDropdowns();
    
    if (activeDropdown !== type) {
        content.classList.add('show');
        caret.classList.add('open');
        activeDropdown = type;
        
        // Focus on search input
        setTimeout(() => {
            const searchInput = document.getElementById(type + 'Search');
            if (searchInput) {
                searchInput.focus();
            }
        }, 100);
    } else {
        activeDropdown = null;
    }
}

function closeAllDropdowns() {
    const dropdowns = ['customer', 'vehicle'];
    dropdowns.forEach(type => {
        const content = document.getElementById(type + 'Content');
        const caret = document.getElementById(type + 'Caret');
        if (content) content.classList.remove('show');
        if (caret) caret.classList.remove('open');
    });
    activeDropdown = null;
}

function selectOption(type, code, name) {
    const input = document.getElementById(type === 'customer' ? 'customer' : 'no_polisi');
    const codeInput = document.getElementById(type === 'customer' ? 'customer_code' : 'no_polisi_code');
    
    input.value = name;
    codeInput.value = code;
    
    // Close dropdown
    closeAllDropdowns();
    
    // Submit form
    document.getElementById('filterForm').submit();
}

// Search functionality
function setupSearch(type) {
    const searchInput = document.getElementById(type + 'Search');
    const list = document.getElementById(type + 'List');
    const items = list.querySelectorAll('.dropdown-item');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Setup search for all dropdowns
    setupSearch('customer');
    setupSearch('vehicle');
    
    // Initialize custom date range display
    const dateRange = document.getElementById('date_range').value;
    const customDateRange = document.getElementById('custom_date_range');
    if (dateRange === 'custom') {
        customDateRange.style.display = 'block';
    } else {
        customDateRange.style.display = 'none';
    }
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-dropdown')) {
            closeAllDropdowns();
        }
    });
    
    // Handle escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAllDropdowns();
        }
    });
});

function resetFilters() {
    window.location.href = '?';
}

// Submit filter form
function submitFilter() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    
    // Create URL with all parameters
    const params = new URLSearchParams();
    for (let [key, value] of formData.entries()) {
        if (value) {
            params.append(key, value);
        }
    }
    
    // Redirect to the same page with new parameters
    window.location.href = '?' + params.toString();
}

// Submit custom date form
function submitCustomDate() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    
    // Create URL with all parameters
    const params = new URLSearchParams();
    for (let [key, value] of formData.entries()) {
        if (value) {
            params.append(key, value);
        }
    }
    
    // Redirect to the same page with new parameters
    window.location.href = '?' + params.toString();
}
</script>

<style>
/* Tablet-specific styles for work order table */
@media (max-width: 1024px) and (min-width: 768px) {
    .main-table {
        font-size: 0.75rem !important;
    }
    
    .main-table th,
    .main-table td {
        padding: 0.4rem 0.3rem !important;
        font-size: 0.75rem !important;
        line-height: 1.2 !important;
    }
    
    .main-table th {
        font-weight: 600 !important;
        font-size: 0.7rem !important;
    }
    
    .main-table .badge {
        font-size: 0.65rem !important;
        padding: 0.25rem 0.4rem !important;
    }
    
    .main-table .data-utama {
        font-weight: 600 !important;
        font-size: 0.7rem !important;
    }
    
    /* Reduce spacing in table */
    .table-responsive {
        font-size: 0.75rem;
    }
    
    /* Make table more compact */
    .main-table tbody tr {
        height: auto !important;
        min-height: 2.5rem;
    }
    
    /* Adjust column widths for better fit */
    .main-table th:nth-child(1), .main-table td:nth-child(1) { width: 12%; } /* No Order */
    .main-table th:nth-child(2), .main-table td:nth-child(2) { width: 10%; } /* Tanggal */
    .main-table th:nth-child(3), .main-table td:nth-child(3) { width: 15%; } /* Customer */
    .main-table th:nth-child(4), .main-table td:nth-child(4) { width: 20%; } /* Alamat */
    .main-table th:nth-child(5), .main-table td:nth-child(5) { width: 10%; } /* Telepon */
    .main-table th:nth-child(6), .main-table td:nth-child(6) { width: 8%; }  /* No Polisi */
    .main-table th:nth-child(7), .main-table td:nth-child(7) { width: 15%; } /* Kendaraan */
    .main-table th:nth-child(8), .main-table td:nth-child(8) { width: 10%; } /* Status */
}

/* Extra small tablets */
@media (max-width: 768px) and (min-width: 576px) {
    .main-table {
        font-size: 0.7rem !important;
    }
    
    .main-table th,
    .main-table td {
        padding: 0.3rem 0.2rem !important;
        font-size: 0.7rem !important;
        line-height: 1.1 !important;
    }
    
    .main-table th {
        font-size: 0.65rem !important;
    }
    
    .main-table .badge {
        font-size: 0.6rem !important;
        padding: 0.2rem 0.3rem !important;
    }
    
    .main-table .data-utama {
        font-size: 0.65rem !important;
    }
}
</style>
