<?php
$title = 'Proses Work Order';
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
                    <h4 class="mb-0"><i class="fa-solid fa-gears me-2"></i>Proses Work Order</h4>
                    <button type="button" class="btn" onclick="window.location.href='<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/dashboard'" title="Kembali" style="vertical-align: middle;">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <hr>
        
        
        <!-- Search and Limit Section (No Filters) -->
        <div class="row mb-4">
            <div class="col-md-8">
                <form method="GET" action="" class="d-flex">
                    <div class="input-group">
                        <input type="text" 
                               class="form-control" 
                               name="search" 
                               value="<?php echo htmlspecialchars($search); ?>" 
                               placeholder="Cari No Order, Customer, No Polisi, atau Kendaraan..."
                               aria-label="Search">
                        <button class="btn btn-sm btn-outline-primary" type="submit">
                            <i class="fas fa-search"></i> Cari
                        </button>
                        <?php if (!empty($search)): ?>
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
        
        <!-- Results Info -->
        <div class="row mb-0">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Menampilkan <?php echo count($workOrders); ?> dari <?php echo number_format($totalWorkOrders); ?> data
                    <strong>(Hanya menampilkan Work Order dengan status Belum/Sedang diproses)</strong>
                    <?php if (!empty($search)): ?>
                        <br>untuk pencarian "<strong><?php echo htmlspecialchars($search); ?></strong>"
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
                                <th>No Telepon</th>
                                <th>No Polisi</th>
                                <th>Kendaraan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($workOrders)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                        Tidak ada data yang ditemukan
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($workOrders as $workOrder): ?>
                                    <tr class="clickable-row" onclick="showWorkOrderDetail('<?php echo htmlspecialchars($workOrder['NoOrder']); ?>')" style="cursor: pointer;">
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


<!-- Modal Detail Work Order -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detailModalLabel">
                    <i class="fa-solid fa-wrench me-2"></i>Detail Work Order
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailModalBody">
                <!-- Loading Spinner -->
                <div class="text-center py-5" id="loadingSpinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat data...</p>
                </div>
                
                <!-- Detail Content (will be populated by AJAX) -->
                <div id="detailContent" style="display: none;">
                    <!-- Header Information -->
                    <div class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless table-sm detail-table p-0 m-0">
                                    <tr>
                                        <td width="40%"><strong>Nomor WO</strong></td>
                                        <td width="5%">:</td>
                                        <td id="detail_noorder">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal Order</strong></td>
                                        <td>:</td>
                                        <td id="detail_tanggal">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Kendaraan</strong></td>
                                        <td>:</td>
                                        <td id="detail_kendaraan">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>No Polisi</strong></td>
                                        <td>:</td>
                                        <td id="detail_nopolisi">-</td>
                                    </tr>
                                    <tr>
                                        <td width="40%"><strong>Warna</strong></td>
                                        <td width="5%">:</td>
                                        <td id="detail_warna">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Marketing</strong></td>
                                        <td>:</td>
                                        <td id="detail_marketing">-</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless table-sm detail-table">
                                    <tr>
                                        <td><strong>Customer</strong></td>
                                        <td>:</td>
                                        <td id="detail_customer">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Alamat</strong></td>
                                        <td>:</td>
                                        <td id="detail_alamat">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>No Telepon</strong></td>
                                        <td>:</td>
                                        <td id="detail_telepon">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Jasa</strong></td>
                                        <td>:</td>
                                        <td id="detail_totaljasa"><strong>-</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Barang</strong></td>
                                        <td>:</td>
                                        <td id="detail_totalbarang"><strong>-</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Order</strong></td>
                                        <td>:</td>
                                        <td id="detail_totalorder" class="text-danger"><strong>-</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Service Transactions -->
                    <div class="detail-section mb-4">
                        <h6 class="section-title"><i class="fa-solid fa-wrench me-2"></i>Transaksi Service</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nama Jasa</th>
                                        <th>Mekanik</th>
                                        <th width="10%">QTY</th>
                                        <th width="15%">Tarif</th>
                                        <th width="15%">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="detail_services">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Tidak ada data service</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Item Transactions -->
                    <div class="detail-section">
                        <h6 class="section-title"><i class="fa-solid fa-gears me-2"></i>Transaksi Barang</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nama Barang</th>
                                        <th>Merek</th>
                                        <th width="10%">Satuan</th>
                                        <th width="8%">QTY</th>
                                        <th width="15%">Harga</th>
                                        <th width="15%">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="detail_items">
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Tidak ada data barang</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="btnProsesModal">
                    <i class="fa-solid fa-gears pb-1 me-2"></i>Proses
                </button>
                <button type="button" class="btn btn-danger" id="btnBatalModal">
                    <i class="fa-solid fa-xmark pb-1 me-2"></i>Batal
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-times pb-1 me-2"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Proses Work Order -->
<div class="modal fade" id="prosesModal" tabindex="-1" aria-labelledby="prosesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="prosesModalLabel">
                    <i class="fa-solid fa-gears me-2"></i>Konfirmasi Proses Work Order
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mb-3">
                    <i class="fa-solid fa-info-circle me-2"></i>
                    <strong>Informasi:</strong> Work Order akan diproses dan status akan berubah menjadi "Sedang Diproses".
                </div>
                <p class="mb-2"><strong>Apakah Anda yakin ingin memproses Work Order ini?</strong></p>
                <div class="bg-light p-3 rounded">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td width="30%"><strong>No Order</strong></td>
                            <td width="5%">:</td>
                            <td id="proses_noorder">-</td>
                        </tr>
                        <tr>
                            <td><strong>Customer</strong></td>
                            <td>:</td>
                            <td id="proses_customer">-</td>
                        </tr>
                        <tr>
                            <td><strong>Kendaraan</strong></td>
                            <td>:</td>
                            <td id="proses_kendaraan">-</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-times pb-1 me-2"></i>Tidak
                </button>
                <button type="button" class="btn btn-success" id="btnConfirmProses">
                    <i class="fa-solid fa-check pb-1 me-2"></i>Ya, Proses
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Selesai Work Order -->
<div class="modal fade" id="selesaiModal" tabindex="-1" aria-labelledby="selesaiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="selesaiModalLabel">
                    <i class="fa-solid fa-check-circle me-2"></i>Konfirmasi Selesai Work Order
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success mb-3">
                    <i class="fa-solid fa-check-circle me-2"></i>
                    <strong>Informasi:</strong> Work Order akan diselesaikan dan status akan berubah menjadi "Selesai".
                </div>
                <p class="mb-2"><strong>Apakah Anda yakin ingin menyelesaikan Work Order ini?</strong></p>
                <div class="bg-light p-3 rounded">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td width="30%"><strong>No Order</strong></td>
                            <td width="5%">:</td>
                            <td id="selesai_noorder">-</td>
                        </tr>
                        <tr>
                            <td><strong>Customer</strong></td>
                            <td>:</td>
                            <td id="selesai_customer">-</td>
                        </tr>
                        <tr>
                            <td><strong>Kendaraan</strong></td>
                            <td>:</td>
                            <td id="selesai_kendaraan">-</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-times pb-1 me-2"></i>Tidak
                </button>
                <button type="button" class="btn btn-primary" id="btnConfirmSelesai">
                    <i class="fa-solid fa-check pb-1 me-2"></i>Ya, Selesaikan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Batal Work Order -->
<div class="modal fade" id="batalModal" tabindex="-1" aria-labelledby="batalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="batalModalLabel">
                    <i class="fa-solid fa-exclamation-triangle me-2"></i>Konfirmasi Pembatalan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning mb-3">
                    <i class="fa-solid fa-warning me-2"></i>
                    <strong>Perhatian!</strong> Tindakan ini tidak dapat dibatalkan.
                </div>
                <p class="mb-2"><strong>Apakah Anda yakin ingin membatalkan Work Order ini?</strong></p>
                <div class="bg-light p-3 rounded">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td width="30%"><strong>No Order</strong></td>
                            <td width="5%">:</td>
                            <td id="batal_noorder">-</td>
                        </tr>
                        <tr>
                            <td><strong>Customer</strong></td>
                            <td>:</td>
                            <td id="batal_customer">-</td>
                        </tr>
                        <tr>
                            <td><strong>Kendaraan</strong></td>
                            <td>:</td>
                            <td id="batal_kendaraan">-</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-times pb-1 me-2"></i>Tidak
                </button>
                <button type="button" class="btn btn-danger" id="btnConfirmBatal">
                    <i class="fa-solid fa-check pb-1 me-2"></i>Ya, Batalkan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Global variable to store current NoOrder
let currentNoOrder = '';
let currentWorkOrderData = {};

// Show Work Order Detail Modal
function showWorkOrderDetail(noOrder) {
    currentNoOrder = noOrder;
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('detailModal'));
    modal.show();
    
    // Show loading spinner, hide content
    document.getElementById('loadingSpinner').style.display = 'block';
    document.getElementById('detailContent').style.display = 'none';
    
    // Fetch data via AJAX
    const basePath = '<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>';
    const url = basePath + '/processworkorder?ajax=get_detail&noorder=' + encodeURIComponent(noOrder);
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('Error: ' + data.error);
                modal.hide();
                return;
            }
            
            // Store data for later use (batal function)
            currentWorkOrderData = {
                NoOrder: data.header.NoOrder,
                NamaCustomer: data.header.NamaCustomer,
                NamaKendaraan: data.header.NamaKendaraan,
                NoPolisi: data.header.NoPolisi
            };
            
            // Populate header information
            document.getElementById('detail_noorder').textContent = data.header.NoOrder || '-';
            document.getElementById('detail_tanggal').textContent = data.header.TanggalOrder ? 
                formatDate(data.header.TanggalOrder) : '-';
            document.getElementById('detail_kendaraan').textContent = data.header.NamaKendaraan || '-';
            document.getElementById('detail_nopolisi').textContent = data.header.NoPolisi || '-';
            document.getElementById('detail_warna').textContent = data.header.Warna || '-';
            document.getElementById('detail_marketing').textContent = data.header.Marketing || '-';
            document.getElementById('detail_customer').textContent = data.header.NamaCustomer || '-';
            document.getElementById('detail_alamat').textContent = data.header.AlamatCustomer || '-';
            document.getElementById('detail_telepon').textContent = data.header.NoTelepon || '-';
            document.getElementById('detail_totaljasa').innerHTML = '<strong>' + formatNumber(data.header.TotalJasa || 0) + '</strong>';
            document.getElementById('detail_totalbarang').innerHTML = '<strong>' + formatNumber(data.header.TotalBarang || 0) + '</strong>';
            document.getElementById('detail_totalorder').innerHTML = '<strong>' + formatNumber(data.header.TotalOrder || 0) + '</strong>';
            
            // Update tombol Proses berdasarkan StatusOrder
            const btnProses = document.getElementById('btnProsesModal');
            const statusOrder = data.header.StatusOrder;
            
            if (statusOrder == 1) {
                // Status: Sedang Diproses - Tombol jadi "Selesai"
                btnProses.innerHTML = '<i class="fa-solid fa-check pb-1 me-2"></i>Selesai';
                btnProses.className = 'btn btn-primary'; // Ganti warna jadi biru
            } else {
                // Status: Belum Diproses - Tombol "Proses"
                btnProses.innerHTML = '<i class="fa-solid fa-gears pb-1 me-2"></i>Proses';
                btnProses.className = 'btn btn-success'; // Warna hijau
            }
            
            // Populate service transactions
            const servicesBody = document.getElementById('detail_services');
            servicesBody.innerHTML = '';
            
            if (data.services && data.services.length > 0) {
                data.services.forEach(service => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${escapeHtml(service.NamaJasa || '-')}</td>
                        <td>${escapeHtml(service.Mekanik || '-')}</td>
                        <td class="text-center"><span class="badge bg-info">${parseInt(service.Qty) || 0}</span></td>
                        <td class="text-end">${formatNumber(service.Tarif || 0)}</td>
                        <td class="text-end"><strong>${formatNumber(service.Total || 0)}</strong></td>
                    `;
                    servicesBody.appendChild(row);
                });
            } else {
                servicesBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Tidak ada data service</td></tr>';
            }
            
            // Populate item transactions
            const itemsBody = document.getElementById('detail_items');
            itemsBody.innerHTML = '';
            
            if (data.items && data.items.length > 0) {
                data.items.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${escapeHtml(item.NamaBarang || '-')}</td>
                        <td>${escapeHtml(item.MerekBarang || '-')}</td>
                        <td class="text-center">${escapeHtml(item.Satuan || '-')}</td>
                        <td class="text-center"><span class="badge bg-success">${parseInt(item.Qty) || 0}</span></td>
                        <td class="text-end">${formatNumber(item.Harga || 0)}</td>
                        <td class="text-end"><strong>${formatNumber(item.Total || 0)}</strong></td>
                    `;
                    itemsBody.appendChild(row);
                });
            } else {
                itemsBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Tidak ada data barang</td></tr>';
            }
            
            // Hide loading, show content
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('detailContent').style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memuat data');
            modal.hide();
        });
}

// Helper function to format date
function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
}

// Helper function to format number
function formatNumber(number) {
    if (!number) return '0';
    return Math.floor(number).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

// Helper function to escape HTML
function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.toString().replace(/[&<>"']/g, m => map[m]);
}

// Proses/Selesai Work Order function - Show confirmation modal
function prosesWorkOrder() {
    if (!currentNoOrder) {
        alert('No Order tidak ditemukan!');
        return;
    }
    
    const btnProses = document.getElementById('btnProsesModal');
    const isSelesai = btnProses.textContent.trim() === 'Selesai';
    
    if (isSelesai) {
        // Fungsi Selesai (StatusOrder = 1 -> 2)
        // Populate modal konfirmasi selesai
        document.getElementById('selesai_noorder').textContent = currentWorkOrderData.NoOrder || currentNoOrder;
        document.getElementById('selesai_customer').textContent = currentWorkOrderData.NamaCustomer || '-';
        document.getElementById('selesai_kendaraan').textContent = 
            (currentWorkOrderData.NoPolisi ? currentWorkOrderData.NoPolisi + ' - ' : '') + 
            (currentWorkOrderData.NamaKendaraan || '-');
        
        // Close detail modal
        const detailModal = bootstrap.Modal.getInstance(document.getElementById('detailModal'));
        if (detailModal) {
            detailModal.hide();
        }
        
        // Show selesai confirmation modal
        const selesaiModal = new bootstrap.Modal(document.getElementById('selesaiModal'));
        selesaiModal.show();
    } else {
        // Fungsi Proses (StatusOrder = 0 -> 1)
        // Populate modal konfirmasi
        document.getElementById('proses_noorder').textContent = currentWorkOrderData.NoOrder || currentNoOrder;
        document.getElementById('proses_customer').textContent = currentWorkOrderData.NamaCustomer || '-';
        document.getElementById('proses_kendaraan').textContent = 
            (currentWorkOrderData.NoPolisi ? currentWorkOrderData.NoPolisi + ' - ' : '') + 
            (currentWorkOrderData.NamaKendaraan || '-');
        
        // Close detail modal
        const detailModal = bootstrap.Modal.getInstance(document.getElementById('detailModal'));
        if (detailModal) {
            detailModal.hide();
        }
        
        // Show proses confirmation modal
        const prosesModal = new bootstrap.Modal(document.getElementById('prosesModal'));
        prosesModal.show();
    }
}

// Batal Work Order function - Show confirmation modal
function batalWorkOrder() {
    if (!currentNoOrder) {
        alert('No Order tidak ditemukan!');
        return;
    }
    
    // Populate modal konfirmasi
    document.getElementById('batal_noorder').textContent = currentWorkOrderData.NoOrder || currentNoOrder;
    document.getElementById('batal_customer').textContent = currentWorkOrderData.NamaCustomer || '-';
    document.getElementById('batal_kendaraan').textContent = 
        (currentWorkOrderData.NoPolisi ? currentWorkOrderData.NoPolisi + ' - ' : '') + 
        (currentWorkOrderData.NamaKendaraan || '-');
    
    // Close detail modal
    const detailModal = bootstrap.Modal.getInstance(document.getElementById('detailModal'));
    if (detailModal) {
        detailModal.hide();
    }
    
    // Show batal confirmation modal
    const batalModal = new bootstrap.Modal(document.getElementById('batalModal'));
    batalModal.show();
}

// Confirm Batal Work Order - Process cancellation
function confirmBatalWorkOrder() {
    if (!currentNoOrder) {
        alert('No Order tidak ditemukan!');
        return;
    }
    
    // Disable button to prevent double click
    const btnConfirm = document.getElementById('btnConfirmBatal');
    btnConfirm.disabled = true;
    btnConfirm.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Memproses...';
    
    // Send AJAX request
    const basePath = '<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>';
    const url = basePath + '/processworkorder?ajax=batal_order';
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'noorder=' + encodeURIComponent(currentNoOrder)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal and remove backdrop
            const batalModal = bootstrap.Modal.getInstance(document.getElementById('batalModal'));
            if (batalModal) {
                batalModal.hide();
            }
            
            // Remove all modal backdrops
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            
            // Reload page to show success message from session
            setTimeout(function() {
                window.location.href = window.location.pathname + '?success=1&t=' + Date.now();
            }, 200);
        } else {
            alert('Error: ' + (data.error || 'Gagal membatalkan Work Order'));
            
            // Re-enable button
            btnConfirm.disabled = false;
            btnConfirm.innerHTML = '<i class="fa-solid fa-check pb-1 me-2"></i>Ya, Batalkan';
        }
    })
    .catch(error => {
        alert('Error: Terjadi kesalahan saat membatalkan Work Order');
        
        // Re-enable button
        btnConfirm.disabled = false;
        btnConfirm.innerHTML = '<i class="fa-solid fa-check pb-1 me-2"></i>Ya, Batalkan';
    });
}

// Confirm Proses Work Order - Process the work order
function confirmProsesWorkOrder() {
    if (!currentNoOrder) {
        alert('No Order tidak ditemukan!');
        return;
    }
    
    // Disable button to prevent double click
    const btnConfirm = document.getElementById('btnConfirmProses');
    btnConfirm.disabled = true;
    btnConfirm.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Memproses...';
    
    // Send AJAX request
    const basePath = '<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>';
    const url = basePath + '/processworkorder?ajax=proses_order';
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'noorder=' + encodeURIComponent(currentNoOrder)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal and remove backdrop
            const prosesModal = bootstrap.Modal.getInstance(document.getElementById('prosesModal'));
            if (prosesModal) {
                prosesModal.hide();
            }
            
            // Remove all modal backdrops
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            
            // Reload page to show success message from session
            setTimeout(function() {
                window.location.href = window.location.pathname + '?success=1&t=' + Date.now();
            }, 200);
        } else {
            alert('Error: ' + (data.error || 'Gagal memproses Work Order'));
            
            // Re-enable button
            btnConfirm.disabled = false;
            btnConfirm.innerHTML = '<i class="fa-solid fa-check pb-1 me-2"></i>Ya, Proses';
        }
    })
    .catch(error => {
        alert('Error: Terjadi kesalahan saat memproses Work Order');
        
        // Re-enable button
        btnConfirm.disabled = false;
        btnConfirm.innerHTML = '<i class="fa-solid fa-check pb-1 me-2"></i>Ya, Proses';
    });
}

// Confirm Selesai Work Order - Complete the work order
function confirmSelesaiWorkOrder() {
    if (!currentNoOrder) {
        alert('No Order tidak ditemukan!');
        return;
    }
    
    // Disable button to prevent double click
    const btnConfirm = document.getElementById('btnConfirmSelesai');
    btnConfirm.disabled = true;
    btnConfirm.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Memproses...';
    
    // Send AJAX request
    const basePath = '<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>';
    const url = basePath + '/processworkorder?ajax=selesai_order';
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'noorder=' + encodeURIComponent(currentNoOrder)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal and remove backdrop
            const selesaiModal = bootstrap.Modal.getInstance(document.getElementById('selesaiModal'));
            if (selesaiModal) {
                selesaiModal.hide();
            }
            
            // Remove all modal backdrops
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            
            // Reload page to show success message from session
            setTimeout(function() {
                window.location.href = window.location.pathname + '?success=1&t=' + Date.now();
            }, 200);
        } else {
            alert('Error: ' + (data.error || 'Gagal menyelesaikan Work Order'));
            
            // Re-enable button
            btnConfirm.disabled = false;
            btnConfirm.innerHTML = '<i class="fa-solid fa-check pb-1 me-2"></i>Ya, Selesaikan';
        }
    })
    .catch(error => {
        alert('Error: Terjadi kesalahan saat menyelesaikan Work Order');
        
        // Re-enable button
        btnConfirm.disabled = false;
        btnConfirm.innerHTML = '<i class="fa-solid fa-check pb-1 me-2"></i>Ya, Selesaikan';
    });
}

// Event listeners for modal buttons
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('btnProsesModal').addEventListener('click', prosesWorkOrder);
    document.getElementById('btnBatalModal').addEventListener('click', batalWorkOrder);
    document.getElementById('btnConfirmProses').addEventListener('click', confirmProsesWorkOrder);
    document.getElementById('btnConfirmSelesai').addEventListener('click', confirmSelesaiWorkOrder);
    document.getElementById('btnConfirmBatal').addEventListener('click', confirmBatalWorkOrder);
});
</script>
