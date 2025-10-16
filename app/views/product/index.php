<?php
$title = 'Informasi Harga Jual dan Stok Barang';
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
                    <h4 class="mb-0"><i class="fa-solid fa-money-bill-trend-up me-2"></i>Informasi Harga Jual dan Stok Barang</h4>
                    <button type="button" class="btn" onclick="history.back()" title="Kembali" style="vertical-align: middle;">
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
                               value="<?php echo htmlspecialchars($search); ?>" 
                               placeholder="Cari barang..."
                               aria-label="Search">
                        <button class="btn btn-sm btn-outline-primary" type="submit">
                            <i class="fas fa-search"></i> Cari
                        </button>
                        <?php if (!empty($search) || !empty($filters['kelompok']) || !empty($filters['jenis']) || !empty($filters['merek'])): ?>
                            <a href="?" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Reset
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            <div class="col-md-4">
                <form method="GET" action="" class="d-flex">
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <input type="hidden" name="kelompok_code" value="<?php echo htmlspecialchars($filters['kelompok']); ?>">
                    <input type="hidden" name="jenis_code" value="<?php echo htmlspecialchars($filters['jenis']); ?>">
                    <input type="hidden" name="merek_code" value="<?php echo htmlspecialchars($filters['merek']); ?>">
                    <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sortBy); ?>">
                    <input type="hidden" name="order" value="<?php echo htmlspecialchars($sortOrder); ?>">
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
                        <div class="col-md-6">
                            <label for="kelompok" class="form-label">Kelompok</label>
                            <div class="custom-dropdown" id="kelompokDropdown">
                                <div class="dropdown-trigger" onclick="toggleDropdown('kelompok')">
                                    <input type="text" 
                                           id="kelompok" 
                                           class="form-select dropdown-input" 
                                           placeholder="Pilih Kelompok..."
                                           value="<?php echo !empty($filters['kelompok']) ? htmlspecialchars(array_values(array_filter($groups, fn($g) => $g['KodeKelompok'] == $filters['kelompok']))[0]['NamaKelompok'] ?? '') : ''; ?>"
                                           readonly
                                           autocomplete="off">
                                    <span class="dropdown-caret" id="kelompokCaret">
                                        <i class="fas fa-chevron-down"></i>
                                    </span>
                                </div>
                                <div class="dropdown-content" id="kelompokContent">
                                    <div class="dropdown-search">
                                        <input type="text" 
                                               id="kelompokSearch" 
                                               placeholder="Cari kelompok..."
                                               autocomplete="off">
                                    </div>
                                    <div class="dropdown-list" id="kelompokList">
                                        <div class="dropdown-item" data-code="" onclick="selectOption('kelompok', '', 'SEMUA')">SEMUA</div>
                                        <?php foreach ($groups as $group): ?>
                                            <div class="dropdown-item" 
                                                 data-code="<?php echo htmlspecialchars($group['KodeKelompok']); ?>"
                                                 onclick="selectOption('kelompok', '<?php echo htmlspecialchars($group['KodeKelompok']); ?>', '<?php echo htmlspecialchars($group['NamaKelompok']); ?>')">
                                                <?php echo htmlspecialchars($group['NamaKelompok']); ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="kelompok_code" id="kelompok_code" value="<?php echo htmlspecialchars($filters['kelompok']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="jenis" class="form-label">Jenis</label>
                            <div class="custom-dropdown" id="jenisDropdown">
                                <div class="dropdown-trigger" onclick="toggleDropdown('jenis')">
                                    <input type="text" 
                                           id="jenis" 
                                           class="form-select dropdown-input" 
                                           placeholder="Pilih Jenis..."
                                           value="<?php echo !empty($filters['jenis']) ? htmlspecialchars(array_values(array_filter($types, fn($t) => $t['KodeJenis'] == $filters['jenis']))[0]['NamaJenis'] ?? '') : ''; ?>"
                                           readonly
                                           autocomplete="off">
                                    <span class="dropdown-caret" id="jenisCaret">
                                        <i class="fas fa-chevron-down"></i>
                                    </span>
                                </div>
                                <div class="dropdown-content" id="jenisContent">
                                    <div class="dropdown-search">
                                        <input type="text" 
                                               id="jenisSearch" 
                                               placeholder="Cari jenis..."
                                               autocomplete="off">
                                    </div>
                                    <div class="dropdown-list" id="jenisList">
                                        <div class="dropdown-item" data-code="" onclick="selectOption('jenis', '', 'SEMUA')">SEMUA</div>
                                        <?php foreach ($types as $type): ?>
                                            <div class="dropdown-item" 
                                                 data-code="<?php echo htmlspecialchars($type['KodeJenis']); ?>"
                                                 onclick="selectOption('jenis', '<?php echo htmlspecialchars($type['KodeJenis']); ?>', '<?php echo htmlspecialchars($type['NamaJenis']); ?>')">
                                                <?php echo htmlspecialchars($type['NamaJenis']); ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="jenis_code" id="jenis_code" value="<?php echo htmlspecialchars($filters['jenis']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="merek" class="form-label">Merek</label>
                            <div class="custom-dropdown" id="merekDropdown">
                                <div class="dropdown-trigger" onclick="toggleDropdown('merek')">
                                    <input type="text" 
                                           id="merek" 
                                           class="form-select dropdown-input" 
                                           placeholder="Pilih Merek..."
                                           value="<?php echo !empty($filters['merek']) ? htmlspecialchars(array_values(array_filter($brands, fn($b) => $b['KodeMerek'] == $filters['merek']))[0]['NamaMerek'] ?? '') : ''; ?>"
                                           readonly
                                           autocomplete="off">
                                    <span class="dropdown-caret" id="merekCaret">
                                        <i class="fas fa-chevron-down"></i>
                                    </span>
                                </div>
                                <div class="dropdown-content" id="merekContent">
                                    <div class="dropdown-search">
                                        <input type="text" 
                                               id="merekSearch" 
                                               placeholder="Cari merek..."
                                               autocomplete="off">
                                    </div>
                                    <div class="dropdown-list" id="merekList">
                                        <div class="dropdown-item" data-code="" onclick="selectOption('merek', '', 'SEMUA')">SEMUA</div>
                                        <?php foreach ($brands as $brand): ?>
                                            <div class="dropdown-item" 
                                                 data-code="<?php echo htmlspecialchars($brand['KodeMerek']); ?>"
                                                 onclick="selectOption('merek', '<?php echo htmlspecialchars($brand['KodeMerek']); ?>', '<?php echo htmlspecialchars($brand['NamaMerek']); ?>')">
                                                <?php echo htmlspecialchars($brand['NamaMerek']); ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="merek_code" id="merek_code" value="<?php echo htmlspecialchars($filters['merek']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="button" class="btn btn-outline-secondary" onclick="resetFilters();">
                                    <i class="fas fa-filter-circle-xmark"></i> Reset Filter
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hidden inputs to preserve search and other parameters -->
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <input type="hidden" name="limit" value="<?php echo htmlspecialchars($limit); ?>">
                    <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sortBy); ?>">
                    <input type="hidden" name="order" value="<?php echo htmlspecialchars($sortOrder); ?>">
                </form>
            </div>
        </div>
        
        <!-- Results Info -->
        <div class="row mb-0">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Menampilkan <?php echo count($products); ?> dari <?php echo number_format($totalProducts); ?> data
                    <?php if (!empty($search)): ?>
                        untuk pencarian "<strong><?php echo htmlspecialchars($search); ?></strong>"
                    <?php endif; ?>
                    
                    <?php 
                    $activeFilters = [];
                    if (!empty($filters['kelompok'])) {
                        $groupName = '';
                        foreach ($groups as $group) {
                            if ($group['KodeKelompok'] == $filters['kelompok']) {
                                $groupName = $group['NamaKelompok'];
                                break;
                            }
                        }
                        $activeFilters[] = "Kelompok: <strong>" . htmlspecialchars($groupName) . "</strong>";
                    }
                    if (!empty($filters['jenis'])) {
                        $typeName = '';
                        foreach ($types as $type) {
                            if ($type['KodeJenis'] == $filters['jenis']) {
                                $typeName = $type['NamaJenis'];
                                break;
                            }
                        }
                        $activeFilters[] = "Jenis: <strong>" . htmlspecialchars($typeName) . "</strong>";
                    }
                    if (!empty($filters['merek'])) {
                        $brandName = '';
                        foreach ($brands as $brand) {
                            if ($brand['KodeMerek'] == $filters['merek']) {
                                $brandName = $brand['NamaMerek'];
                                break;
                            }
                        }
                        $activeFilters[] = "Merek: <strong>" . htmlspecialchars($brandName) . "</strong>";
                    }
                    
                    if (!empty($activeFilters)): ?>
                        <br><small>Filter aktif: <?php echo implode(', ', $activeFilters); ?></small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Products Table -->
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-striped table-hover main-table">
                        <thead class="table-dark">
                            <tr>
                                <th class="sortable" data-sort="B.NamaBarang">
                                    Nama Barang
                                    <?php if ($sortBy == 'B.NamaBarang'): ?>
                                        <i class="fas fa-sort-<?php echo $sortOrder == 'ASC' ? 'up' : 'down'; ?> ms-1"></i>
                                    <?php endif; ?>
                                </th>
                                <th class="sortable" data-sort="B.Satuan">
                                    Satuan
                                    <?php if ($sortBy == 'B.Satuan'): ?>
                                        <i class="fas fa-sort-<?php echo $sortOrder == 'ASC' ? 'up' : 'down'; ?> ms-1"></i>
                                    <?php endif; ?>
                                </th>
                                <th class="sortable" data-sort="M.NamaMerek">
                                    Merek
                                    <?php if ($sortBy == 'M.NamaMerek'): ?>
                                        <i class="fas fa-sort-<?php echo $sortOrder == 'ASC' ? 'up' : 'down'; ?> ms-1"></i>
                                    <?php endif; ?>
                                </th>
                                <th class="sortable" data-sort="J.NamaJenis">
                                    Jenis
                                    <?php if ($sortBy == 'J.NamaJenis'): ?>
                                        <i class="fas fa-sort-<?php echo $sortOrder == 'ASC' ? 'up' : 'down'; ?> ms-1"></i>
                                    <?php endif; ?>
                                </th>
                                <th class="sortable" data-sort="B.NamaUkuran">
                                    Ukuran
                                    <?php if ($sortBy == 'B.NamaUkuran'): ?>
                                        <i class="fas fa-sort-<?php echo $sortOrder == 'ASC' ? 'up' : 'down'; ?> ms-1"></i>
                                    <?php endif; ?>
                                </th>
                                <th class="sortable text-end" data-sort="B.HargaJual">
                                    Harga
                                    <?php if ($sortBy == 'B.HargaJual'): ?>
                                        <i class="fas fa-sort-<?php echo $sortOrder == 'ASC' ? 'up' : 'down'; ?> ms-1"></i>
                                    <?php endif; ?>
                                </th>
                                <th class="sortable text-end" data-sort="S.StokAkhir">
                                    Stok
                                    <?php if ($sortBy == 'S.StokAkhir'): ?>
                                        <i class="fas fa-sort-<?php echo $sortOrder == 'ASC' ? 'up' : 'down'; ?> ms-1"></i>
                                    <?php endif; ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                        Tidak ada data yang ditemukan
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td class="data-utama">
                                            <?php echo htmlspecialchars($product['NamaBarang']); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($product['Satuan']); ?></td>
                                        <td><?php echo htmlspecialchars($product['NamaMerek']); ?></td>
                                        <td><?php echo htmlspecialchars($product['NamaJenis']); ?></td>
                                        <td><?php echo htmlspecialchars($product['NamaUkuran']); ?></td>
                                        <td class="text-end data-harga">
                                            <span>
                                                <?php echo number_format($product['HargaJual'], 0, ',', '.'); ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge data-stok <?php echo $product['StokAkhir'] > 0 ? 'bg-success' : 'bg-danger'; ?>">
                                                <?php echo number_format($product['StokAkhir'], 0, ',', '.'); ?>
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
                    <nav aria-label="Product pagination">
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
// Table sorting functionality
document.addEventListener('DOMContentLoaded', function() {
    const sortableHeaders = document.querySelectorAll('.sortable');
    
    sortableHeaders.forEach(header => {
        header.style.cursor = 'pointer';
        
        header.addEventListener('click', function() {
            const sortColumn = this.dataset.sort;
            const currentSort = '<?php echo $sortBy; ?>';
            const currentOrder = '<?php echo $sortOrder; ?>';
            
            let newOrder = 'ASC';
            if (sortColumn === currentSort && currentOrder === 'ASC') {
                newOrder = 'DESC';
            }
            
            const url = new URL(window.location);
            url.searchParams.set('sort', sortColumn);
            url.searchParams.set('order', newOrder);
            url.searchParams.set('page', '1'); // Reset to first page when sorting
            
            window.location.href = url.toString();
        });
    });
});

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
    const dropdowns = ['kelompok', 'jenis', 'merek'];
    dropdowns.forEach(type => {
        const content = document.getElementById(type + 'Content');
        const caret = document.getElementById(type + 'Caret');
        if (content) content.classList.remove('show');
        if (caret) caret.classList.remove('open');
    });
    activeDropdown = null;
}

function selectOption(type, code, name) {
    const input = document.getElementById(type);
    const codeInput = document.getElementById(type + '_code');
    
    input.value = name;
    codeInput.value = code;
    
    // Close dropdown
    closeAllDropdowns();
    
    // Handle kelompok change
    if (type === 'kelompok') {
        handleKelompokChange(code);
        // Submit form
        document.getElementById('filterForm').submit();
    } else {
        // Submit form for jenis and merek
        document.getElementById('filterForm').submit();
    }
}

function handleKelompokChange(kelompokCode) {
    const jenisInput = document.getElementById('jenis');
    const jenisCodeInput = document.getElementById('jenis_code');
    
    // Reset jenis selection when kelompok changes
    jenisInput.value = '';
    jenisCodeInput.value = '';
    
    // Update jenis list if kelompok is selected
    if (kelompokCode) {
        updateJenisList(kelompokCode);
    } else {
        resetJenisList();
    }
}

function updateJenisList(kelompokCode) {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', '?ajax=get_types&kelompok=' + kelompokCode, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            try {
                const types = JSON.parse(xhr.responseText);
                const jenisList = document.getElementById('jenisList');
                
                // Clear existing options except SEMUA
                jenisList.innerHTML = '<div class="dropdown-item" data-code="" onclick="selectOption(\'jenis\', \'\', \'SEMUA\')">SEMUA</div>';
                
                types.forEach(function(type) {
                    const div = document.createElement('div');
                    div.className = 'dropdown-item';
                    div.setAttribute('data-code', type.KodeJenis);
                    div.onclick = function() { selectOption('jenis', type.KodeJenis, type.NamaJenis); };
                    div.textContent = type.NamaJenis;
                    jenisList.appendChild(div);
                });
            } catch (e) {
                console.error('Error parsing types response:', e);
            }
        }
    };
    xhr.send();
}

function resetJenisList() {
    const jenisList = document.getElementById('jenisList');
    jenisList.innerHTML = '<div class="dropdown-item" data-code="" onclick="selectOption(\'jenis\', \'\', \'SEMUA\')">SEMUA</div>';
    <?php foreach ($types as $type): ?>
    const div<?php echo $type['KodeJenis']; ?> = document.createElement('div');
    div<?php echo $type['KodeJenis']; ?>.className = 'dropdown-item';
    div<?php echo $type['KodeJenis']; ?>.setAttribute('data-code', '<?php echo htmlspecialchars($type['KodeJenis']); ?>');
    div<?php echo $type['KodeJenis']; ?>.onclick = function() { selectOption('jenis', '<?php echo htmlspecialchars($type['KodeJenis']); ?>', '<?php echo htmlspecialchars($type['NamaJenis']); ?>'); };
    div<?php echo $type['KodeJenis']; ?>.textContent = '<?php echo htmlspecialchars($type['NamaJenis']); ?>';
    jenisList.appendChild(div<?php echo $type['KodeJenis']; ?>);
    <?php endforeach; ?>
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
    setupSearch('kelompok');
    setupSearch('jenis');
    setupSearch('merek');
    
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
</script>
