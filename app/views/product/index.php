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
                <h4>Informasi Harga Jual dan Stok Barang</h4>
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
                    <input type="hidden" name="kelompok" value="<?php echo htmlspecialchars($filters['kelompok']); ?>">
                    <input type="hidden" name="jenis" value="<?php echo htmlspecialchars($filters['jenis']); ?>">
                    <input type="hidden" name="merek" value="<?php echo htmlspecialchars($filters['merek']); ?>">
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
        <div class="row mb-2">
            <div class="col-12">
                <form method="GET" action="" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="kelompok" class="form-label">Kelompok</label>
                            <select name="kelompok" id="kelompok" class="form-select" onchange="updateJenis(); this.form.submit();">
                                <option value="">SEMUA</option>
                                <?php foreach ($groups as $group): ?>
                                    <option value="<?php echo htmlspecialchars($group['KodeKelompok']); ?>" 
                                            <?php echo $filters['kelompok'] == $group['KodeKelompok'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($group['NamaKelompok']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="jenis" class="form-label">Jenis</label>
                            <select name="jenis" id="jenis" class="form-select" onchange="this.form.submit();">
                                <option value="">SEMUA</option>
                                <?php foreach ($types as $type): ?>
                                    <option value="<?php echo htmlspecialchars($type['KodeJenis']); ?>" 
                                            <?php echo $filters['jenis'] == $type['KodeJenis'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($type['NamaJenis']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="merek" class="form-label">Merek</label>
                            <select name="merek" id="merek" class="form-select" onchange="this.form.submit();">
                                <option value="">SEMUA</option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?php echo htmlspecialchars($brand['KodeMerek']); ?>" 
                                            <?php echo $filters['merek'] == $brand['KodeMerek'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($brand['NamaMerek']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
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

// Filter functionality
function updateJenis() {
    const kelompokSelect = document.getElementById('kelompok');
    const jenisSelect = document.getElementById('jenis');
    const selectedKelompok = kelompokSelect.value;
    
    // Reset jenis selection when kelompok changes
    jenisSelect.value = '';
    
    if (selectedKelompok) {
        // Make AJAX request to get types for selected group
        const xhr = new XMLHttpRequest();
        xhr.open('GET', '?ajax=get_types&kelompok=' + selectedKelompok, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                try {
                    const types = JSON.parse(xhr.responseText);
                    jenisSelect.innerHTML = '<option value="">SEMUA</option>';
                    
                    types.forEach(function(type) {
                        const option = document.createElement('option');
                        option.value = type.KodeJenis;
                        option.textContent = type.NamaJenis;
                        jenisSelect.appendChild(option);
                    });
                } catch (e) {
                    console.error('Error parsing types response:', e);
                }
            }
        };
        xhr.send();
    } else {
        // Reset to all types
        jenisSelect.innerHTML = '<option value="">SEMUA</option>';
        <?php foreach ($types as $type): ?>
        const option<?php echo $type['KodeJenis']; ?> = document.createElement('option');
        option<?php echo $type['KodeJenis']; ?>.value = '<?php echo htmlspecialchars($type['KodeJenis']); ?>';
        option<?php echo $type['KodeJenis']; ?>.textContent = '<?php echo htmlspecialchars($type['NamaJenis']); ?>';
        jenisSelect.appendChild(option<?php echo $type['KodeJenis']; ?>);
        <?php endforeach; ?>
    }
}

function resetFilters() {
    window.location.href = '?';
}
</script>
