<div class="container">
    <div class="main-container fade-in">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center me-2">
                    <h4 class="mb-0">
                        <i class="fas fa-file-invoice me-2"></i>Transaksi Work Order
                    </h4>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success btn-sm" onclick="window.location.href='<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/workorder'">
                            <i class="fas fa-clipboard-list me-1"></i>Data Work Order
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" onclick="window.location.href='<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/processworkorder'">
                            <i class="fas fa-gears me-1"></i>Proses Work Order
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" id="btnNewOrder">
                            <i class="fas fa-plus me-1"></i>Work Order Baru
                        </button>
                    </div>
                </div>

                <hr>

                <!-- Form Section (Initially Hidden) -->
                <div id="formSection" style="display: none;">
                    <h5 class="mb-3">
                        <i class="fa-solid fa-plus-circle me-2"></i>Buat Work Order Baru
                    </h5>
                    <form id="formWorkOrder">
                        <!-- Header Section -->
                        <div class="mb-3">
                            <div class="row">
                                <!-- Customer -->
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Customer <span class="text-danger">*</span>
                                    </label>
                                    <div class="d-flex gap-2">
                                        <div class="flex-grow-1">
                                            <select class="form-select" id="selectCustomer" name="KodeCustomer">
                                                <option value="">Pilih Customer...</option>
                                            </select>
                                        </div>
                                        <button type="button" class="btn btn-primary btn-icon-add" id="btnAddCustomer" title="Tambah Customer Baru">
                                            <i class="fa-solid fa-plus fa-lg"></i>
                                        </button>
                                    </div>
                                    <div class="mb-2 mt-1" id="customerInfo" style="display: none;">
                                        <div class="info-empesis p-2 rounded">
                                            <small class="text-muted">
                                                <div>
                                                    <strong>Alamat:</strong>
                                                    <span id="customerAlamat">-</span>
                                                </div>
                                                <div>
                                                    <strong>Kota:</strong>
                                                    <span id="customerKota">-</span>
                                                </div>
                                                <div>
                                                    <strong>Telepon:</strong>
                                                    <span id="customerTelepon">-</span>
                                                </div>
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kendaraan -->
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Kendaraan <span class="text-danger">*</span>
                                    </label>
                                    <div class="d-flex gap-2">
                                        <div class="flex-grow-1">
                                            <select class="form-select" id="selectKendaraan" name="KodeKendaraan">
                                                <option value="">Pilih Kendaraan...</option>
                                            </select>
                                        </div>
                                        <button type="button" class="btn btn-primary btn-icon-add" id="btnAddKendaraan" title="Tambah Kendaraan Baru">
                                            <i class="fa-solid fa-plus fa-lg"></i>
                                        </button>
                                    </div>
                                    <div class="mb-2 mt-1" id="kendaraanInfo" style="display: none;">
                                        <div class="info-empesis p-2 rounded">
                                            <small class="text-muted">
                                                <div>
                                                    <strong>No Polisi:</strong>
                                                    <span id="kendaraanNoPolisi">-</span>
                                                </div>
                                                <div>
                                                    <strong>Merek:</strong>
                                                    <span id="kendaraanMerek">-</span>
                                                </div>
                                                <div>
                                                    <strong>Model:</strong>
                                                    <span id="kendaraanModel">-</span>
                                                </div>
                                                <div>
                                                    <strong>Tipe:</strong>
                                                    <span id="kendaraanTipe">-</span>
                                                </div>
                                                <div>
                                                    <strong>Tahun:</strong>
                                                    <span id="kendaraanTahun">-</span> |
                                                    <strong>Warna:</strong>
                                                    <span id="kendaraanWarna">-</span>
                                                </div>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Montir -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Mekanik <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select select-montir" id="selectMontir" name="KodeMontir">
                                        <option value="">Pilih Montir...</option>
                                    </select>
                                </div>

                                <!-- Picker -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Marketing <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select select-picker" id="selectPicker" name="KodePicker">
                                        <option value="">Pilih Picker...</option>
                                    </select>
                                </div>
                            </div>

                            <!-- KM Awal -->
                            <div class="row mb-3"> 
                                <div class="col-md-2 mb-2"> 
                                    <label class="form-label">KM Awal</label>
                                    <input type="number" class="form-control" id="inputKMAwal" name="KMAwal" value="0" min="0">
                                </div>

                                <!-- KM Akhir -->
                                <div class="col-md-2 mb-2">
                                    <label class="form-label">KM Akhir</label>
                                    <input type="number" class="form-control" id="inputKMAkhir" name="KMAkhir" value="0" min="0">
                                </div>

                                <!-- Keterangan -->
                                <div class="col-md-8 mb-2">
                                    <label class="form-label">Keterangan</label>
                                    <input type="text" class="form-control" id="inputKeterangan" name="Keterangan" value="">
                                </div>
                            </div>
                        </div>
                        
                        <!-- -------------------------------------------------------------------------- -->
                        <!-- Detail Jasa Section -->
                        <!-- -------------------------------------------------------------------------- -->
                        <div class="bg-transparent d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">
                                <i class="fas fa-tools me-2"></i>Detail Jasa
                            </h6>
                            <button type="button" class="btn btn-success btn-sm" id="btnAddJasa">
                                <i class="fas fa-plus me-1"></i>Tambah Jasa
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-bordered table-hover" id="tableJasa">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="20%">Kode/Nama Jasa</th>
                                        <th width="10%">Satuan</th>
                                        <th width="15%">Kategori</th>
                                        <th width="10%">Jumlah</th>
                                        <th width="12%">Tarif</th>
                                        <th width="10%">Disc (%)</th>
                                        <th width="13%">Total</th>
                                        <th width="5%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyJasa">
                                    <tr class="text-center">
                                        <td colspan="9" class="text-muted">Belum ada data jasa</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="table-light fw-bold">
                                        <td colspan="7" class="text-end">Total Jasa:</td>
                                        <td class="text-end" id="totalJasa">Rp 0</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <!-- -------------------------------------------------------------------------- -->

                        <!-- -------------------------------------------------------------------------- -->
                        <!-- Detail Barang Section -->
                        <!-- -------------------------------------------------------------------------- -->
                        <div class="bg-transparent d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">
                                <i class="fas fa-box me-2"></i>Detail Barang
                            </h6>
                            <button type="button" class="btn btn-success btn-sm" id="btnAddBarang">
                                <i class="fas fa-plus me-1"></i>Tambah Barang
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-bordered table-hover" id="tableBarang">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="4%">No</th>
                                        <th width="18%">Kode/Nama Barang</th>
                                        <th width="8%">Satuan</th>
                                        <th width="12%">Merek</th>
                                        <th width="12%">Jenis</th>
                                        <th width="8%">Jumlah</th>
                                        <th width="12%">Harga</th>
                                        <th width="8%">Disc (%)</th>
                                        <th width="13%">Total</th>
                                        <th width="5%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyBarang">
                                    <tr class="text-center">
                                        <td colspan="10" class="text-muted">Belum ada data barang</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="table-light fw-bold">
                                        <td colspan="8" class="text-end">Total Barang:</td>
                                        <td class="text-end" id="totalBarang">Rp 0</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <!-- -------------------------------------------------------------------------- -->
                        
                        <!-- Grand Total & Action Buttons -->
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-secondary" id="btnCancel">
                                            <i class="fas fa-times me-1"></i>Batal
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex justify-content-end align-items-center mb-2">
                                            <h5 class="mb-0 me-3">GRAND TOTAL:</h5>
                                            <h5 class="mb-0 text-primary fw-bold" id="grandTotal">Rp 0</h5>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100" id="btnSave">
                                            <i class="fas fa-save me-1"></i>Simpan Work Order
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- List Section -->
                <div id="listSection">
                    <!-- Search & Filter -->
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" id="searchInput" placeholder="Cari NoOrder, Customer, NoPolisi..." value="<?php echo htmlspecialchars($filters['search']); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="filterDate" placeholder="DD/MM/YYYY" value="<?php echo htmlspecialchars($filters['display_date']); ?>" autocomplete="off">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-secondary" id="btnClearFilter">
                                <i class="fas fa-undo me-1"></i>Reset
                            </button>
                        </div>
                    </div>
                    
                    <!-- Active Filter Info -->
                    <?php if (!empty($filters['display_date']) || !empty($filters['search'])): ?>
                    <div class="row mb-0">
                        <div class="col-12">
                            <div class="alert alert-info py-2 mb-0">
                                <i class="fas fa-filter me-2"></i><strong>Filter Aktif:</strong>
                                <?php if (!empty($filters['search'])): ?>
                                    <span class="badge bg-primary ms-2">Pencarian: <?php echo htmlspecialchars($filters['search']); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($filters['display_date'])): ?>
                                    <span class="badge bg-success ms-2">Tanggal: <?php echo htmlspecialchars($filters['display_date']); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Work Orders Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>No Order</th>
                                    <th>Tanggal</th>
                                    <th>Customer</th>
                                    <th>Kendaraan</th>
                                    <th>Mek./Mark.</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($workOrders) > 0): ?>
                                    <?php foreach ($workOrders as $wo): ?>
                                        <tr style="cursor: pointer;" onclick="showWorkOrderDetail('<?php echo htmlspecialchars($wo['NoOrder']); ?>')">
                                            <td><?php echo htmlspecialchars($wo['NoOrder']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($wo['TanggalOrder'])); ?></td>
                                            <td><?php echo htmlspecialchars($wo['NamaCustomer'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($wo['NamaKendaraan'] ?? '-') . ' - ' . htmlspecialchars($wo['NoPolisi'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($wo['NamaMontir'] ?? '-'); ?>/<?php echo htmlspecialchars($wo['NamaPicker'] ?? '-'); ?></td>
                                            <td class="text-end"><?php echo number_format($wo['TotalOrder'], 0, ',', '.'); ?></td>
                                            <td>
                                                <?php
                                                $statusLabels = [
                                                    0 => '<span class="badge bg-secondary">Belum diproses</span>',
                                                    1 => '<span class="badge bg-info">Sedang diproses</span>',
                                                    2 => '<span class="badge bg-warning">Proses Selesai</span>',
                                                    3 => '<span class="badge bg-primary">Faktur dibuat</span>',
                                                    4 => '<span class="badge bg-success">Telah dibayar</span>',
                                                    5 => '<span class="badge bg-danger">Dibatalkan</span>'
                                                ];
                                                echo $statusLabels[$wo['StatusOrder']] ?? '<span class="badge bg-secondary">Unknown</span>';
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">Tidak ada data work order</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/transaksi-work-order?p=<?php echo $page - 1; ?>&search=<?php echo urlencode($filters['search']); ?>&date=<?php echo urlencode($filters['date']); ?>">Previous</a>
                                </li>
                                
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <?php if ($i == 1 || $i == $totalPages || abs($i - $page) <= 2): ?>
                                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/transaksi-work-order?p=<?php echo $i; ?>&search=<?php echo urlencode($filters['search']); ?>&date=<?php echo urlencode($filters['date']); ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php elseif (abs($i - $page) == 3): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                
                                <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/transaksi-work-order?p=<?php echo $page + 1; ?>&search=<?php echo urlencode($filters['search']); ?>&date=<?php echo urlencode($filters['date']); ?>">Next</a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
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
                                <table class="table table-borderless table-sm detail-table">
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
                                        <td><strong>Mekanik</strong></td>
                                        <td>:</td>
                                        <td id="detail_mekanik">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Marketing</strong></td>
                                        <td>:</td>
                                        <td id="detail_marketing">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Customer</strong></td>
                                        <td>:</td>
                                        <td id="detail_customer">-</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless table-sm detail-table">
                                    <tr>
                                        <td width="40%"><strong>Alamat</strong></td>
                                        <td width="5%">:</td>
                                        <td id="detail_alamat">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>No Telepon</strong></td>
                                        <td>:</td>
                                        <td id="detail_telepon">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>KM Awal</strong></td>
                                        <td>:</td>
                                        <td id="detail_kmawal">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>KM Akhir</strong></td>
                                        <td>:</td>
                                        <td id="detail_kmakhir">-</td>
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
                        <h6 class="section-title"><i class="fa-solid fa-wrench me-2"></i>Detail Jasa</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nama Jasa</th>
                                        <th>Kategori</th>
                                        <th width="10%">QTY</th>
                                        <th width="15%">Tarif</th>
                                        <th width="10%">Disc (%)</th>
                                        <th width="15%">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="detail_services">
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Tidak ada data jasa</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Item Transactions -->
                    <div class="detail-section">
                        <h6 class="section-title"><i class="fa-solid fa-gears me-2"></i>Detail Barang</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nama Barang</th>
                                        <th>Merek</th>
                                        <th width="10%">Satuan</th>
                                        <th width="8%">QTY</th>
                                        <th width="15%">Harga</th>
                                        <th width="10%">Disc (%)</th>
                                        <th width="15%">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="detail_items">
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Tidak ada data barang</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Modal Footer dengan Button Edit dan Download PDF -->
                <div class="modal-footer" id="detailModalFooter" style="display: none;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa-solid fa-times me-2"></i>Tutup
                    </button>
                    <button type="button" class="btn btn-danger" id="btnDownloadPDF" onclick="downloadWorkOrderPDF()" style="display: none;">
                        <i class="fa-solid fa-file-pdf me-2"></i>Work Order (PDF)
                    </button>
                    <button type="button" class="btn btn-primary" id="btnEditWorkOrder" onclick="editWorkOrder()" style="display: none;">
                        <i class="fa-solid fa-edit me-2"></i>Edit Work Order
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Simpan Work Order -->
<div class="modal fade" id="confirmSaveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fa-solid fa-save me-2"></i>Konfirmasi Simpan Work Order
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mb-3">
                    <i class="fa-solid fa-info-circle me-2"></i>
                    <strong>Informasi:</strong> Data akan disimpan dan No Order akan dibuat otomatis.
                </div>
                <p class="mb-2"><strong>Pastikan semua data sudah benar sebelum menyimpan!</strong></p>
                <div class="bg-light p-3 rounded">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td width="35%"><strong>Customer</strong></td>
                            <td width="5%">:</td>
                            <td id="confirm_customer">-</td>
                        </tr>
                        <tr>
                            <td><strong>Kendaraan</strong></td>
                            <td>:</td>
                            <td id="confirm_kendaraan">-</td>
                        </tr>
                        <tr>
                            <td><strong>Total Jasa</strong></td>
                            <td>:</td>
                            <td id="confirm_totaljasa">Rp 0</td>
                        </tr>
                        <tr>
                            <td><strong>Total Barang</strong></td>
                            <td>:</td>
                            <td id="confirm_totalbarang">Rp 0</td>
                        </tr>
                        <tr class="fw-bold">
                            <td><strong>Total Order</strong></td>
                            <td>:</td>
                            <td id="confirm_totalorder" class="text-primary">Rp 0</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-times me-2"></i>Batal
                </button>
                <button type="button" class="btn btn-primary" id="btnConfirmSave">
                    <i class="fa-solid fa-save me-2"></i>Ya, Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Item -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fa-solid fa-exclamation-triangle me-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning mb-3">
                    <i class="fa-solid fa-exclamation-triangle me-2"></i>
                    <strong>Perhatian:</strong> Item yang dihapus tidak dapat dikembalikan!
                </div>
                <p class="mb-0"><strong>Apakah Anda yakin ingin menghapus item ini?</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-times me-2"></i>Tidak
                </button>
                <button type="button" class="btn btn-danger" id="btnConfirmDelete">
                    <i class="fa-solid fa-trash me-2"></i>Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Batal Input -->
<div class="modal fade" id="confirmCancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">
                    <i class="fa-solid fa-exclamation-circle me-2"></i>Konfirmasi Pembatalan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning mb-3">
                    <i class="fa-solid fa-exclamation-triangle me-2"></i>
                    <strong>Perhatian:</strong> Data yang sudah diisi akan hilang!
                </div>
                <p class="mb-0"><strong>Apakah Anda yakin ingin membatalkan input work order?</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-times me-2"></i>Tidak
                </button>
                <button type="button" class="btn btn-warning" id="btnConfirmCancel" data-bs-dismiss="modal">
                    <i class="fa-solid fa-ban me-2"></i>Ya, Batalkan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add New Customer -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fa-solid fa-user-plus me-2"></i>Tambah Customer Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formAddCustomer">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Customer <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="newCustomerNama" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Customer <span class="text-danger">*</span></label>
                            <select class="form-select" id="newCustomerJenis" required>
                                <option value="">Pilih Jenis...</option>
                                <option value="0">Perorangan</option>
                                <option value="1">Perusahaan</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="newCustomerAlamat" rows="2" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kota <span class="text-danger">*</span></label>
                            <select class="form-select" id="newCustomerKota" required>
                                <option value="">Pilih Kota...</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No Telepon <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="newCustomerTelepon" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">PIC (Person In Charge)</label>
                        <input type="text" class="form-control" id="newCustomerPIC">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-times me-2"></i>Batal
                </button>
                <button type="button" class="btn btn-primary" id="btnSaveNewCustomer">
                    <i class="fa-solid fa-save me-2"></i>Simpan Customer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add New Kendaraan -->
<div class="modal fade" id="addKendaraanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fa-solid fa-car me-2"></i>Tambah Kendaraan Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formAddKendaraan">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Merek <span class="text-danger">*</span></label>
                            <select class="form-select" id="newKendaraanMerek" required>
                                <option value="">Pilih Merek...</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Model (Jenis) <span class="text-danger">*</span></label>
                            <select class="form-select" id="newKendaraanModel" required>
                                <option value="">Pilih Model...</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipe <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="newKendaraanTipe" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Warna <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="newKendaraanWarna" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tahun <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="newKendaraanTahun" min="1900" max="2100" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Silinder <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="newKendaraanSilinder" min="0" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Bahan Bakar <span class="text-danger">*</span></label>
                            <select class="form-select" id="newKendaraanBahanBakar" required>
                                <option value="">Pilih...</option>
                                <option value="Bensin">Bensin</option>
                                <option value="Diesel">Diesel</option>
                                <option value="Listrik">Listrik</option>
                                <option value="Hybrid">Hybrid</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Kendaraan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="newKendaraanNama" required>
                        <small class="text-muted">Otomatis terisi, tetapi bisa diedit</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No Polisi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="newKendaraanNoPolisi" required style="text-transform: uppercase;">
                        <small class="text-muted">Format: B1234XYZ</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-times me-2"></i>Batal
                </button>
                <button type="button" class="btn btn-primary" id="btnSaveNewKendaraan">
                    <i class="fa-solid fa-save me-2"></i>Simpan Kendaraan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Include Choices.js CSS -->
<link href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/assets/css/choices.min.css" rel="stylesheet" />

<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<!-- Include Choices.js JS -->
<script src="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/assets/js/choices.min.js"></script>

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
// Global variables (outside DOMContentLoaded for access by edit functions)
const basePath = '<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>';
let customerChoice, kendaraanChoice, montirChoice, pickerChoice;
let detailJasaCounter = 0;
let detailBarangCounter = 0;
// Modal instances
let confirmCancelModal, confirmSaveModal, confirmDeleteModal;
let addCustomerModal, addKendaraanModal, detailModal;
let detailJasaData = [];
let detailBarangData = [];
let currentEditNoOrder = '';

document.addEventListener('DOMContentLoaded', function() {

    // Initialize Choices.js for Customer
    customerChoice = new Choices('#selectCustomer', {
        searchEnabled: true,
        searchPlaceholderValue: 'Ketik untuk mencari...',
        noResultsText: 'Tidak ada hasil',
        itemSelectText: '',
        removeItemButton: true,
        placeholder: true,
        placeholderValue: 'Pilih Customer...',
        searchResultLimit: 50,
        renderChoiceLimit: 50
    });
    
    // Auto-focus to search input when dropdown opens for Customer
    const customerElement = document.getElementById('selectCustomer');
    customerElement.addEventListener('showDropdown', function() {
        setTimeout(() => {
            const searchInput = customerChoice.input.element;
            if (searchInput) {
                searchInput.focus();
            }
        }, 50);
    });

    // Initialize Choices.js for Kendaraan
    kendaraanChoice = new Choices('#selectKendaraan', {
        searchEnabled: true,
        searchPlaceholderValue: 'Ketik untuk mencari...',
        noResultsText: 'Tidak ada hasil',
        itemSelectText: '',
        removeItemButton: true,
        placeholder: true,
        placeholderValue: 'Pilih Kendaraan...',
        searchResultLimit: 50,
        renderChoiceLimit: 50
    });
    
    // Auto-focus to search input when dropdown opens for Kendaraan
    const kendaraanElement = document.getElementById('selectKendaraan');
    kendaraanElement.addEventListener('showDropdown', function() {
        setTimeout(() => {
            const searchInput = kendaraanChoice.input.element;
            if (searchInput) {
                searchInput.focus();
            }
        }, 50);
    });

    // Initialize Choices.js for Montir & Picker (same way as Customer & Kendaraan)
    montirChoice = new Choices('#selectMontir', {
        searchEnabled: true,
        searchPlaceholderValue: 'Ketik untuk mencari...',
        noResultsText: 'Tidak ada hasil',
        itemSelectText: '',
        removeItemButton: true,
        placeholder: true,
        placeholderValue: 'Pilih Mekanik...',
        searchResultLimit: 50,
        renderChoiceLimit: 50
    });
    
    // Auto-focus to search input when dropdown opens for Montir
    const montirElement = document.getElementById('selectMontir');
    montirElement.addEventListener('showDropdown', function() {
        setTimeout(() => {
            const searchInput = montirChoice.input.element;
            if (searchInput) {
                searchInput.focus();
            }
        }, 50);
    });

    // Initialize Choices.js for Picker
    pickerChoice = new Choices('#selectPicker', {
        searchEnabled: true,
        searchPlaceholderValue: 'Ketik untuk mencari...',
        noResultsText: 'Tidak ada hasil',
        itemSelectText: '',
        removeItemButton: true,
        placeholder: true,
        placeholderValue: 'Pilih Marketing...',
        searchResultLimit: 50,
        renderChoiceLimit: 50
    });
    
    // Auto-focus to search input when dropdown opens for Picker
    const pickerElement = document.getElementById('selectPicker');
    pickerElement.addEventListener('showDropdown', function() {
        setTimeout(() => {
            const searchInput = pickerChoice.input.element;
            if (searchInput) {
                searchInput.focus();
            }
        }, 50);
    });
    
    // Load customers on search
    document.getElementById('selectCustomer').addEventListener('search', function(e) {
        const searchTerm = e.detail.value;
        if (searchTerm.length >= 2) {
            loadCustomers(searchTerm);
        }
    });

    // Load vehicles on search
    document.getElementById('selectKendaraan').addEventListener('search', function(e) {
        const searchTerm = e.detail.value;
        if (searchTerm.length >= 2) {
            loadVehicles(searchTerm);
        }
    });

    // Load montir on search
    document.getElementById('selectMontir').addEventListener('search', function(e) {
        const searchTerm = e.detail.value;
        if (searchTerm.length >= 1) {
            loadMontir(searchTerm);
        }
    });

    // Load picker on search
    document.getElementById('selectPicker').addEventListener('search', function(e) {
        const searchTerm = e.detail.value;
        if (searchTerm.length >= 1) {
            loadPicker(searchTerm);
        }
    });
    
    // Initialize Bootstrap Modals with proper options
    const confirmCancelElement = document.getElementById('confirmCancelModal');
    confirmCancelModal = new bootstrap.Modal(confirmCancelElement, {
        backdrop: true,
        keyboard: true
    });
    
    confirmSaveModal = new bootstrap.Modal(document.getElementById('confirmSaveModal'), {
        backdrop: true,
        keyboard: true
    });
    confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'), {
        backdrop: true,
        keyboard: true
    });
    addCustomerModal = new bootstrap.Modal(document.getElementById('addCustomerModal'), {
        backdrop: true,
        keyboard: true
    });
    addKendaraanModal = new bootstrap.Modal(document.getElementById('addKendaraanModal'), {
        backdrop: true,
        keyboard: true
    });
    detailModal = new bootstrap.Modal(document.getElementById('detailModal'), {
        backdrop: true,
        keyboard: true
    });
    
    // Flag to track if cancel was confirmed
    let cancelConfirmed = false;
    
    // Listen to modal hidden event for cleanup
    confirmCancelElement.addEventListener('hidden.bs.modal', function() {
        if (cancelConfirmed) {
            // Perform the actual cancellation
            document.getElementById('formSection').style.display = 'none';
            document.getElementById('listSection').style.display = 'block';
            resetForm();
            cancelConfirmed = false;
            
            // Enable btnNewOrder saat kembali ke list
            const btnNewOrder = document.getElementById('btnNewOrder');
            if (btnNewOrder) {
                btnNewOrder.disabled = false;
            }
            
            // Force cleanup of any remaining backdrops
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        } else {
            // Modal closed without confirming (clicked X or Tidak)
            // Reset flag just in case
            cancelConfirmed = false;
        }
    });
    
    // Customer change event
    document.getElementById('selectCustomer').addEventListener('change', function(e) {
        const value = e.target.value;
        if (value) {
            fetch(`${basePath}/transaksi-work-order/get-customer?code=${encodeURIComponent(value)}`)
                .then(response => response.json())
                .then(customer => {
                    if (customer && customer.KodeCustomer) {
                        document.getElementById('customerAlamat').textContent = customer.AlamatCustomer || '-';
                        document.getElementById('customerKota').textContent = customer.Kota || '-';
                        document.getElementById('customerTelepon').textContent = customer.NoTelepon || '-';
                        document.getElementById('customerInfo').style.display = 'block';
                    }
                })
                .catch(error => console.error('Error:', error));
        } else {
            document.getElementById('customerInfo').style.display = 'none';
        }
    });
    
    // Vehicle change event
    document.getElementById('selectKendaraan').addEventListener('change', function(e) {
        const value = e.target.value;
        if (value) {
            fetch(`${basePath}/transaksi-work-order/get-vehicle?code=${encodeURIComponent(value)}`)
                .then(response => response.json())
                .then(vehicle => {
                    if (vehicle && vehicle.KodeKendaraan) {
                        document.getElementById('kendaraanNoPolisi').textContent = vehicle.NoPolisi || '-';
                        document.getElementById('kendaraanMerek').textContent = vehicle.NamaMerek || '-';
                        document.getElementById('kendaraanModel').textContent = vehicle.NamaJenis || '-';
                        document.getElementById('kendaraanTipe').textContent = vehicle.Tipe || '-';
                        document.getElementById('kendaraanTahun').textContent = vehicle.Tahun || '-';
                        document.getElementById('kendaraanWarna').textContent = vehicle.Warna || '-';
                        document.getElementById('kendaraanInfo').style.display = 'block';
                    }
                })
                .catch(error => console.error('Error:', error));
        } else {
            document.getElementById('kendaraanInfo').style.display = 'none';
        }
    });
    
    // Load customers function
    function loadCustomers(searchTerm) {
        fetch(`${basePath}/transaksi-work-order/search-customers?term=${encodeURIComponent(searchTerm)}`)
            .then(response => response.json())
            .then(data => {
                if (data.results && data.results.length > 0) {
                    const choices = data.results.map(item => ({
                        value: item.id,
                        label: item.text,
                        customProperties: item.data
                    }));
                    customerChoice.setChoices(choices, 'value', 'label', true);
                }
            })
            .catch(error => console.error('Error:', error));
    }
    
    // Load vehicles function
    function loadVehicles(searchTerm) {
        fetch(`${basePath}/transaksi-work-order/search-vehicles?term=${encodeURIComponent(searchTerm)}`)
            .then(response => response.json())
            .then(data => {
                if (data.results && data.results.length > 0) {
                    const choices = data.results.map(item => ({
                        value: item.id,
                        label: item.text,  // Already formatted as "NoPolisi - NamaKendaraan" from controller
                        customProperties: item.data
                    }));
                    kendaraanChoice.setChoices(choices, 'value', 'label', true);
                }
            })
            .catch(error => console.error('Error:', error));
    }
    
    // Load montir function
    function loadMontir(searchTerm) {
        fetch(`${basePath}/transaksi-work-order/search-montir?term=${encodeURIComponent(searchTerm)}`)
            .then(response => response.json())
            .then(data => {
                if (data.results && data.results.length > 0) {
                    const choices = data.results.map(item => ({
                        value: item.id,
                        label: item.text,
                        customProperties: item.data
                    }));

                    // Clear existing choices first
                    montirChoice.clearChoices();

                    // Set new choices
                    montirChoice.setChoices(choices, 'value', 'label', true);
                } else {
                    montirChoice.clearChoices();
                }
            })
            .catch(error => console.error('Error loading montir:', error));
    }

    // Load picker function
    // Load Kota List for Add Customer Modal from TabelKota (Status = 1)
    function loadKotaList() {
        fetch(`${basePath}/transaksi-work-order/get-kota-list`)
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('newCustomerKota');
                select.innerHTML = '<option value="">Pilih Kota...</option>';
                
                if (data.success && data.kota && data.kota.length > 0) {
                    data.kota.forEach(kota => {
                        // Use Kota field for both value and display
                        const option = new Option(kota.Kota, kota.Kota);
                        select.appendChild(option);
                    });
                }
            })
            .catch(error => console.error('Error loading kota:', error));
    }
    
    function loadMerekList() {
        fetch(`${basePath}/transaksi-work-order/get-merek-list`)
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('newKendaraanMerek');
                select.innerHTML = '<option value="">Pilih Merek...</option>';
                
                if (data.success && data.merek && data.merek.length > 0) {
                    data.merek.forEach(merek => {
                        const option = new Option(merek.NamaMerek, merek.KodeMerek);
                        select.appendChild(option);
                    });
                }
            })
            .catch(error => console.error('Error loading merek:', error));
    }
    
    function loadModelList() {
        fetch(`${basePath}/transaksi-work-order/get-model-list`)
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('newKendaraanModel');
                select.innerHTML = '<option value="">Pilih Model...</option>';
                
                if (data.success && data.model && data.model.length > 0) {
                    data.model.forEach(model => {
                        const option = new Option(model.NamaJenis, model.KodeJenis);
                        select.appendChild(option);
                    });
                }
            })
            .catch(error => console.error('Error loading model:', error));
    }
    
    function loadPicker(searchTerm) {
        fetch(`${basePath}/transaksi-work-order/search-picker?term=${encodeURIComponent(searchTerm)}`)
            .then(response => response.json())
            .then(data => {
                if (data.results && data.results.length > 0) {
                    const choices = data.results.map(item => ({
                        value: item.id,
                        label: item.text,
                        customProperties: item.data
                    }));

                    // Clear existing choices first
                    pickerChoice.clearChoices();

                    // Set new choices
                    pickerChoice.setChoices(choices, 'value', 'label', true);
                } else {
                    pickerChoice.clearChoices();
                }
            })
            .catch(error => console.error('Error loading picker:', error));
    }
    
    // Show/Hide Form
    document.getElementById('btnNewOrder').addEventListener('click', function() {
        document.getElementById('formSection').style.display = 'block';
        document.getElementById('listSection').style.display = 'none';
        resetForm();
        
        // Disable btnNewOrder saat masuk form create
        this.disabled = true;
        
        // Auto-fill default picker if TipeUser = 1
        <?php if (isset($defaultPicker) && $defaultPicker): ?>
        setTimeout(function() {
            const defaultPickerData = {
                KodePicker: '<?php echo htmlspecialchars($defaultPicker['KodePicker']); ?>',
                NamaPicker: '<?php echo htmlspecialchars($defaultPicker['NamaPicker']); ?>',
                AlamatPicker: '<?php echo htmlspecialchars($defaultPicker['AlamatPicker'] ?? ''); ?>',
                NoTelepon: '<?php echo htmlspecialchars($defaultPicker['NoTelepon'] ?? ''); ?>'
            };
            
            pickerChoice.clearStore();
            pickerChoice.setChoices([{
                value: defaultPickerData.KodePicker,
                label: defaultPickerData.NamaPicker,
                selected: true,
                customProperties: defaultPickerData
            }], 'value', 'label', true);
        }, 100);
        <?php endif; ?>
    });
    
    // Button Tambah Customer Baru
    document.getElementById('btnAddCustomer').addEventListener('click', function() {
        // Load kota list
        loadKotaList();
        
        // Reset form
        document.getElementById('formAddCustomer').reset();
        
        // Show modal
        addCustomerModal.show();
    });
    
    // Save New Customer
    document.getElementById('btnSaveNewCustomer').addEventListener('click', function() {
        const form = document.getElementById('formAddCustomer');
        
        // Validate form
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Get form data
        const customerData = {
            NamaCustomer: document.getElementById('newCustomerNama').value.trim(),
            AlamatCustomer: document.getElementById('newCustomerAlamat').value.trim(),
            Kota: document.getElementById('newCustomerKota').value,
            NoTelepon: document.getElementById('newCustomerTelepon').value.trim(),
            JenisCustomer: parseInt(document.getElementById('newCustomerJenis').value),
            PIC: document.getElementById('newCustomerPIC').value.trim()
        };
        
        // Validate
        if (!customerData.NamaCustomer || !customerData.Kota || !customerData.NoTelepon || customerData.JenisCustomer === '') {
            showAlert('Semua field yang bertanda (*) wajib diisi!', 'warning');
            return;
        }
        
        // Disable button
        const btnSave = document.getElementById('btnSaveNewCustomer');
        btnSave.disabled = true;
        btnSave.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Menyimpan...';
        
        // Send AJAX request
        fetch(basePath + '/transaksi-work-order/save-customer', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(customerData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close add customer modal
                addCustomerModal.hide();
                
                // Auto-select the new customer in dropdown
                customerChoice.setChoices([{
                    value: data.kodeCustomer,
                    label: data.namaCustomer,
                    selected: true,
                    customProperties: {
                        KodeCustomer: data.kodeCustomer,
                        NamaCustomer: data.namaCustomer,
                        AlamatCustomer: customerData.AlamatCustomer,
                        Kota: customerData.Kota,
                        NoTelepon: customerData.NoTelepon
                    }
                }], 'value', 'label', false);
                
                // Trigger change event to show customer info
                document.getElementById('selectCustomer').dispatchEvent(new Event('change'));
                
                // Show success message
                showSuccessModal('Customer baru berhasil ditambahkan!', 'Kode Customer: ' + data.kodeCustomer);
                
                // Auto-close success modal after 2 seconds
                setTimeout(() => {
                    const successModal = bootstrap.Modal.getInstance(document.getElementById('successModal'));
                    if (successModal) {
                        successModal.hide();
                    }
                }, 2000);
            } else {
                showAlert(data.message || 'Gagal menyimpan customer', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan saat menyimpan customer', 'error');
        })
        .finally(() => {
            // Re-enable button
            btnSave.disabled = false;
            btnSave.innerHTML = '<i class="fa-solid fa-save me-2"></i>Simpan Customer';
        });
    });
    
    // Button Tambah Kendaraan Baru
    document.getElementById('btnAddKendaraan').addEventListener('click', function() {
        // Load merek and model list
        loadMerekList();
        loadModelList();
        
        // Reset form
        document.getElementById('formAddKendaraan').reset();
        
        // Show modal
        addKendaraanModal.show();
    });
    
    // Auto-generate vehicle name when inputs change
    ['newKendaraanMerek', 'newKendaraanModel', 'newKendaraanTipe', 'newKendaraanWarna', 'newKendaraanTahun', 'newKendaraanSilinder'].forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('change', generateVehicleName);
            element.addEventListener('input', generateVehicleName);
        }
    });
    
    // Function to generate vehicle name
    function generateVehicleName() {
        const merekSelect = document.getElementById('newKendaraanMerek');
        const modelSelect = document.getElementById('newKendaraanModel');
        const tipe = document.getElementById('newKendaraanTipe').value.trim();
        const warna = document.getElementById('newKendaraanWarna').value.trim();
        const tahun = document.getElementById('newKendaraanTahun').value.trim();
        const silinder = document.getElementById('newKendaraanSilinder').value.trim();
        
        // Get text from select options
        const merek = merekSelect.options[merekSelect.selectedIndex]?.text || '';
        const model = modelSelect.options[modelSelect.selectedIndex]?.text || '';
        
        // Build vehicle name
        const parts = [merek, model, tipe, warna, tahun, silinder].filter(part => part);
        const vehicleName = parts.join(' ');
        
        document.getElementById('newKendaraanNama').value = vehicleName;
    }
    
    // Save New Vehicle
    document.getElementById('btnSaveNewKendaraan').addEventListener('click', function() {
        const form = document.getElementById('formAddKendaraan');
        
        // Validate form
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Get form data
        const vehicleData = {
            KodeMerek: document.getElementById('newKendaraanMerek').value,
            KodeJenis: document.getElementById('newKendaraanModel').value,
            Tipe: document.getElementById('newKendaraanTipe').value.trim(),
            Warna: document.getElementById('newKendaraanWarna').value.trim(),
            Tahun: parseInt(document.getElementById('newKendaraanTahun').value),
            Silinder: parseInt(document.getElementById('newKendaraanSilinder').value),
            BahanBakar: document.getElementById('newKendaraanBahanBakar').value,
            NamaKendaraan: document.getElementById('newKendaraanNama').value.trim(),
            NoPolisi: document.getElementById('newKendaraanNoPolisi').value.trim()
        };
        
        // Validate
        if (!vehicleData.KodeMerek || !vehicleData.KodeJenis || !vehicleData.Tipe || 
            !vehicleData.Warna || !vehicleData.Tahun || !vehicleData.Silinder || 
            !vehicleData.BahanBakar || !vehicleData.NamaKendaraan || !vehicleData.NoPolisi) {
            showAlert('Semua field yang bertanda (*) wajib diisi!', 'warning');
            return;
        }
        
        // Disable button
        const btnSave = document.getElementById('btnSaveNewKendaraan');
        btnSave.disabled = true;
        btnSave.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Menyimpan...';
        
        // Send AJAX request
        fetch(basePath + '/transaksi-work-order/save-vehicle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(vehicleData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close add vehicle modal
                addKendaraanModal.hide();
                
                // Auto-select the new vehicle in dropdown
                kendaraanChoice.setChoices([{
                    value: data.kodeKendaraan,
                    label: data.namaKendaraan + ' - ' + data.noPolisi,
                    selected: true,
                    customProperties: data.vehicleData
                }], 'value', 'label', false);
                
                // Trigger change event to show vehicle info
                document.getElementById('selectKendaraan').dispatchEvent(new Event('change'));
                
                // Show success message
                showSuccessModal('Kendaraan baru berhasil ditambahkan!', 'Kode Kendaraan: ' + data.kodeKendaraan);
                
                // Auto-close success modal after 2 seconds
                setTimeout(() => {
                    const successModal = bootstrap.Modal.getInstance(document.getElementById('successModal'));
                    if (successModal) {
                        successModal.hide();
                    }
                }, 2000);
            } else {
                showAlert(data.message || 'Gagal menyimpan kendaraan', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan saat menyimpan kendaraan', 'error');
        })
        .finally(() => {
            // Re-enable button
            btnSave.disabled = false;
            btnSave.innerHTML = '<i class="fa-solid fa-save me-2"></i>Simpan Kendaraan';
        });
    });
    
    document.getElementById('btnCancel').addEventListener('click', function() {
        confirmCancelModal.show();
    });
    
    // Add Jasa Row
    document.getElementById('btnAddJasa').addEventListener('click', function() {
        showJasaModal();
    });
    
    // Add Barang Row
    document.getElementById('btnAddBarang').addEventListener('click', function() {
        showBarangModal();
    });
    
    // Function: Show Jasa Modal
    function showJasaModal() {
        const modalHtml = `
            <div class="modal fade" id="modalJasa" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Detail Jasa</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Pilih Jasa</label>
                                <select class="form-select" id="selectJasaModal">
                                    <option value="">Cari jasa...</option>
                                </select>
                            </div>
                            <div id="jasaDetailForm" style="display:none;">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nama Jasa</label>
                                        <input type="text" class="form-control" id="jasaNama" readonly>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Satuan</label>
                                        <input type="text" class="form-control" id="jasaSatuan" readonly>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Kategori</label>
                                        <input type="text" class="form-control" id="jasaKategori" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Jumlah</label>
                                        <input type="number" class="form-control" id="jasaJumlah" value="1" min="0" step="0.01">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Tarif</label>
                                        <input type="number" class="form-control" id="jasaTarif" value="0" min="0">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Discount (%)</label>
                                        <input type="number" class="form-control" id="jasaDiscount" value="0" min="0" max="100" step="0.01">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="form-label">Total Harga</label>
                                        <input type="text" class="form-control fw-bold" id="jasaTotalHarga" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-primary" id="btnSaveJasa">Tambahkan</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove old modal if exists
        const oldModal = document.getElementById('modalJasa');
        if (oldModal) oldModal.remove();
        
        // Append and show modal
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modalElement = document.getElementById('modalJasa');
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        
        // Initialize Choices.js for Jasa
        const jasaChoice = new Choices('#selectJasaModal', {
            searchEnabled: true,
            placeholder: true,
            itemSelectText: '',
            placeholderValue: 'Cari jasa...',
            searchResultLimit: 50,
            renderChoiceLimit: 50
        });
        
        let currentJasaData = null;
        
        // Load jasa on search
        document.getElementById('selectJasaModal').addEventListener('search', function(e) {
            const searchTerm = e.detail.value;
            if (searchTerm.length >= 2) {
                fetch(`${basePath}/transaksi-work-order/search-jasa?term=${encodeURIComponent(searchTerm)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.results && data.results.length > 0) {
                            const choices = data.results.map(item => ({
                                value: item.id,
                                label: item.text,
                                customProperties: item.data
                            }));
                            jasaChoice.setChoices(choices, 'value', 'label', true);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        });
        
        // Jasa change event
        document.getElementById('selectJasaModal').addEventListener('change', function(e) {
            const value = e.target.value;
            if (value) {
                fetch(`${basePath}/transaksi-work-order/get-jasa?code=${encodeURIComponent(value)}`)
                    .then(response => response.json())
                    .then(jasa => {
                        if (jasa && jasa.KodeJasa) {
                            currentJasaData = jasa;
                            document.getElementById('jasaNama').value = jasa.NamaJasa || '';
                            document.getElementById('jasaSatuan').value = jasa.Satuan || '';
                            document.getElementById('jasaKategori').value = jasa.NamaKategori || 'NORMAL';
                            document.getElementById('jasaTarif').value = Math.round(jasa.Harga || 0);
                            document.getElementById('jasaDetailForm').style.display = 'block';
                            calculateJasaTotal();
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        });
        
        // Calculate total on input change
        ['jasaJumlah', 'jasaTarif', 'jasaDiscount'].forEach(id => {
            document.getElementById(id).addEventListener('input', calculateJasaTotal);
        });
        
        function calculateJasaTotal() {
            const jumlah = parseFloat(document.getElementById('jasaJumlah').value) || 0;
            const tarif = parseFloat(document.getElementById('jasaTarif').value) || 0;
            const discount = parseFloat(document.getElementById('jasaDiscount').value) || 0;
            
            const subtotal = jumlah * tarif;
            const discountAmount = subtotal * (discount / 100);
            const total = subtotal - discountAmount;
            
            document.getElementById('jasaTotalHarga').value = 'Rp ' + formatNumber(total);
        }
        
        // Save jasa
        document.getElementById('btnSaveJasa').addEventListener('click', function() {
            if (!currentJasaData) {
                alert('Pilih jasa terlebih dahulu!');
                return;
            }
            
            const jumlah = parseFloat(document.getElementById('jasaJumlah').value) || 0;
            const tarif = parseFloat(document.getElementById('jasaTarif').value) || 0;
            const discount = parseFloat(document.getElementById('jasaDiscount').value) || 0;
            
            if (jumlah <= 0) {
                alert('Jumlah harus lebih dari 0!');
                return;
            }
            
            const subtotal = jumlah * tarif;
            const discountAmount = subtotal * (discount / 100);
            const total = subtotal - discountAmount;
            
            // Add to array
            detailJasaCounter++;
            detailJasaData.push({
                id: detailJasaCounter,
                KodeJasa: currentJasaData.KodeJasa,
                NamaJasa: currentJasaData.NamaJasa,
                Satuan: currentJasaData.Satuan,
                KodeKategori: currentJasaData.KodeKategori || '',
                Kategori: currentJasaData.NamaKategori || 'NORMAL',
                Jumlah: jumlah,
                HargaSatuan: tarif,
                Discount: discount,
                DiscountRupiah: discountAmount,
                TotalHarga: total
            });
            
            renderJasaTable();
            calculateTotals();
            modal.hide();
        });
    }
    
    // Function: Show Barang Modal
    function showBarangModal() {
        const modalHtml = `
            <div class="modal fade" id="modalBarang" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Detail Barang</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Pilih Barang</label>
                                <select class="form-select" id="selectBarangModal">
                                    <option value="">Cari barang...</option>
                                </select>
                            </div>
                            <div id="barangDetailForm" style="display:none;">
                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label class="form-label">Nama Barang</label>
                                        <input type="text" class="form-control" id="barangNama" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Satuan</label>
                                        <input type="text" class="form-control" id="barangSatuan" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Merek</label>
                                        <input type="text" class="form-control" id="barangMerek" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Jenis</label>
                                        <input type="text" class="form-control" id="barangJenis" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Stok Tersedia</label>
                                        <input type="text" class="form-control fw-bold" id="barangStok" readonly style="background-color: #f8f9fa;">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Jumlah</label>
                                        <input type="number" class="form-control" id="barangJumlah" value="1" min="0" step="0.01">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div id="barangStokAlert" class="alert alert-warning py-2 mt-2 mb-0" style="display: none;">
                                            <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                            <small id="barangStokAlertText"></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Harga Satuan</label>
                                        <input type="number" class="form-control" id="barangHarga" value="0" min="0">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Discount (%)</label>
                                        <input type="number" class="form-control" id="barangDiscount" value="0" min="0" max="100" step="0.01">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label">Total Harga</label>
                                        <input type="text" class="form-control fw-bold" id="barangTotalHarga" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-primary" id="btnSaveBarang">Tambahkan</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        const oldModal = document.getElementById('modalBarang');
        if (oldModal) oldModal.remove();
        
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modalElement = document.getElementById('modalBarang');
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        
        const barangChoice = new Choices('#selectBarangModal', {
            searchEnabled: true,
            placeholder: true,
            itemSelectText: '',
            placeholderValue: 'Cari barang...',
            searchResultLimit: 50,
            renderChoiceLimit: 50
        });
        
        // Use window object untuk scope global agar bisa diakses dari event delegation
          window.currentBarangData = null;
          window.currentStokBarang = 0;
          
          // Reset dan sembunyikan alert saat modal dibuka
          const elBarangDetailForm = document.getElementById('barangDetailForm');
          const elBarangStokAlert = document.getElementById('barangStokAlert');
          if (elBarangDetailForm) elBarangDetailForm.style.display = 'none';
          if (elBarangStokAlert) elBarangStokAlert.style.display = 'none';
        
        document.getElementById('selectBarangModal').addEventListener('search', function(e) {
            const searchTerm = e.detail.value;
            if (searchTerm.length >= 2) {
                fetch(`${basePath}/transaksi-work-order/search-barang?term=${encodeURIComponent(searchTerm)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.results && data.results.length > 0) {
                            const choices = data.results.map(item => ({
                                value: item.id,
                                label: item.text,
                                customProperties: item.data
                            }));
                            barangChoice.setChoices(choices, 'value', 'label', true);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        });
        
        document.getElementById('selectBarangModal').addEventListener('change', function(e) {
            const value = e.target.value;
            if (value) {
                  // Fetch barang data
                fetch(`${basePath}/transaksi-work-order/get-barang?code=${encodeURIComponent(value)}`)
                    .then(response => response.json())
                    .then(barang => {
                        if (barang && barang.KodeBarang) {
                             // Fetch stock data
                             fetch(`${basePath}/transaksi-work-order/get-stok-barang?code=${encodeURIComponent(value)}`)
                                 .then(response => response.json())
                                .then(stokData => {
                                     window.currentStokBarang = stokData.stok || 0;
                                     
                                     // Show barang info first
                                     window.currentBarangData = barang;
                                     const elBarangNama = document.getElementById('barangNama');
                                     const elBarangSatuan = document.getElementById('barangSatuan');
                                     const elBarangMerek = document.getElementById('barangMerek');
                                     const elBarangJenis = document.getElementById('barangJenis');
                                     const elBarangHarga = document.getElementById('barangHarga');
                                     const elBarangDiscount = document.getElementById('barangDiscount');
                                     const elBarangStok = document.getElementById('barangStok');
                                     const elBarangJumlah = document.getElementById('barangJumlah');
                                     const elBarangDetailForm = document.getElementById('barangDetailForm');
                                     const elBarangStokAlert = document.getElementById('barangStokAlert');
                                     const elBarangStokAlertText = document.getElementById('barangStokAlertText');
                                     const elBtnSaveBarang = document.getElementById('btnSaveBarang');
                                     
                                     // Fill barang info
                                     if (elBarangNama) elBarangNama.value = barang.NamaBarang || '';
                                     if (elBarangSatuan) elBarangSatuan.value = barang.Satuan || '';
                                     if (elBarangMerek) elBarangMerek.value = barang.NamaMerek || '-';
                                     if (elBarangJenis) elBarangJenis.value = barang.NamaJenis || '-';
                                     if (elBarangHarga) elBarangHarga.value = Math.round(barang.HargaJual || 0);
                                     if (elBarangDiscount) elBarangDiscount.value = barang.DiscountJual || 0;
                                     
                                     // Display stock info
                                     if (elBarangStok) {
                                         elBarangStok.value = window.currentStokBarang;
                                         if (window.currentStokBarang <= 0) {
                                             elBarangStok.style.color = 'red';
                                         } else if (window.currentStokBarang < 10) {
                                             elBarangStok.style.color = 'orange';
                                         } else {
                                             elBarangStok.style.color = 'green';
                                         }
                                     }
                                     
                                     // Enable/disable form based on stock
                                     if (window.currentStokBarang <= 0) {
                                         // Disable form when no stock
                                         if (elBarangJumlah) elBarangJumlah.disabled = true;
                                         if (elBtnSaveBarang) elBtnSaveBarang.disabled = true;
                                     } else {
                                         // Enable form when stock available
                                         if (elBarangJumlah) {
                                             elBarangJumlah.disabled = false;
                                             elBarangJumlah.max = window.currentStokBarang;
                                         }
                                         if (elBtnSaveBarang) elBtnSaveBarang.disabled = false;
                                     }
                                     
                                     // Show form
                                     if (elBarangDetailForm) elBarangDetailForm.style.display = 'block';
                                     
                                     // Setup event listeners untuk jumlah, harga, discount
                                     setupBarangEventListeners();
                                     
                                     // Calculate total
                            calculateBarangTotal();
                                     
                                     // Validate stock after all elements are ready
                                     setTimeout(function() {
                                         validateBarangStock();
                                     }, 50);
                                 })
                                 .catch(error => {
                                     console.error('Error fetching stock:', error);
                                     const elBarangStokAlert = document.getElementById('barangStokAlert');
                                     const elBarangStokAlertText = document.getElementById('barangStokAlertText');
                                     if (elBarangStokAlert && elBarangStokAlertText) {
                                         elBarangStokAlert.className = 'alert alert-danger py-2 mt-2 mb-0';
                                         elBarangStokAlertText.textContent = 'Gagal mengambil data stok barang';
                                         elBarangStokAlert.style.display = 'block';
                                     }
                                 });
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        });
        
        // Function to setup event listeners (called every time barang is selected)
        function setupBarangEventListeners() {
            // Use event delegation via document body untuk menghindari masalah timing
            // Hapus old listeners dengan flag
            if (!window.barangEventListenersSetup) {
                // Setup ONE-TIME event delegation
                document.body.addEventListener('input', function(e) {
                    if (e.target && e.target.id === 'barangJumlah') {
                        validateBarangStock();
                        calculateBarangTotal();
                    } else if (e.target && (e.target.id === 'barangHarga' || e.target.id === 'barangDiscount')) {
                        calculateBarangTotal();
                    }
                });
                
                document.body.addEventListener('change', function(e) {
                    if (e.target && e.target.id === 'barangJumlah') {
                        validateBarangStock();
                        calculateBarangTotal();
                    }
                });
                
                window.barangEventListenersSetup = true;
            }
        }
        
        // Function to validate stock (make it accessible in wider scope)
         function validateBarangStock() {
             const elBarangJumlah = document.getElementById('barangJumlah');
             const alertDiv = document.getElementById('barangStokAlert');
             const alertText = document.getElementById('barangStokAlertText');
             const btnSave = document.getElementById('btnSaveBarang');
             
             if (!elBarangJumlah || !alertDiv || !alertText || !btnSave) {
                 return true; // Elements not ready yet
             }
             
             const jumlah = parseFloat(elBarangJumlah.value) || 0;
             
             // Check if stock is zero first
             if (window.currentStokBarang <= 0) {
                 alertDiv.className = 'alert alert-danger py-2 mt-2 mb-0';
                 alertText.textContent = 'Stok barang tersebut kosong';
                 alertDiv.style.display = 'block';
                 btnSave.disabled = true;
                 return false;
             }
             
             // Then check if quantity exceeds stock
             if (jumlah > window.currentStokBarang) {
                 // Stock insufficient
                 alertDiv.className = 'alert alert-danger py-2 mt-4 mb-0';
                 alertText.textContent = 'Stok tidak mencukupi (Tersedia: ' + window.currentStokBarang + ')';
                 alertDiv.style.display = 'block';
                 btnSave.disabled = true;
                 return false;
             } else if (jumlah > 0 && jumlah <= window.currentStokBarang) {
                 // Valid quantity
                 alertDiv.style.display = 'none';
                 btnSave.disabled = false;
                 return true;
             } else {
                 // Zero or negative (will be handled by save button)
                 alertDiv.style.display = 'none';
                 btnSave.disabled = false;
                 return true;
             }
         }
        
        function calculateBarangTotal() {
             const elBarangJumlah = document.getElementById('barangJumlah');
             const elBarangHarga = document.getElementById('barangHarga');
             const elBarangDiscount = document.getElementById('barangDiscount');
             const elBarangTotalHarga = document.getElementById('barangTotalHarga');
             
             if (!elBarangJumlah || !elBarangHarga || !elBarangDiscount || !elBarangTotalHarga) {
                 return; // Elements not ready yet
             }
             
             const jumlah = parseFloat(elBarangJumlah.value) || 0;
             const harga = parseFloat(elBarangHarga.value) || 0;
             const discount = parseFloat(elBarangDiscount.value) || 0;
            
            const subtotal = jumlah * harga;
            const discountAmount = subtotal * (discount / 100);
            const total = subtotal - discountAmount;
            
             elBarangTotalHarga.value = 'Rp ' + formatNumber(total);
          }
        
          const btnSaveBarangEl = document.getElementById('btnSaveBarang');
         if (btnSaveBarangEl) {
             btnSaveBarangEl.addEventListener('click', function() {
                 const elBarangStokAlert = document.getElementById('barangStokAlert');
                 const elBarangStokAlertText = document.getElementById('barangStokAlertText');
                 
                // Validasi 1: Barang sudah dipilih?
                 if (!window.currentBarangData) {
                     if (elBarangStokAlert && elBarangStokAlertText) {
                         elBarangStokAlert.className = 'alert alert-warning py-2 mt-2 mb-0';
                         elBarangStokAlertText.textContent = 'Pilih barang terlebih dahulu!';
                         elBarangStokAlert.style.display = 'block';
                     }
                return;
            }
            
                 const elBarangJumlahSave = document.getElementById('barangJumlah');
                 const elBarangHargaSave = document.getElementById('barangHarga');
                 const elBarangDiscountSave = document.getElementById('barangDiscount');
                 
                 const jumlah = parseFloat(elBarangJumlahSave?.value) || 0;
                 const harga = parseFloat(elBarangHargaSave?.value) || 0;
                 const discount = parseFloat(elBarangDiscountSave?.value) || 0;
             
             // Validasi 2: Jumlah > 0?
            if (jumlah <= 0) {
                 if (elBarangStokAlert && elBarangStokAlertText) {
                     elBarangStokAlert.className = 'alert alert-warning py-2 mt-2 mb-0';
                     elBarangStokAlertText.textContent = 'Jumlah harus lebih dari 0!';
                     elBarangStokAlert.style.display = 'block';
                 }
                return;
            }
             
             // Validasi 3: Stok tersedia?
             if (window.currentStokBarang <= 0) {
                 if (elBarangStokAlert && elBarangStokAlertText) {
                     elBarangStokAlert.className = 'alert alert-danger py-2 mt-2 mb-0';
                     elBarangStokAlertText.textContent = 'Stok barang tersebut kosong';
                     elBarangStokAlert.style.display = 'block';
                 }
                 btnSaveBarangEl.disabled = true;
                 return;
             }
             
             // Validasi 4: Jumlah tidak melebihi stok?
             if (jumlah > window.currentStokBarang) {
                 if (elBarangStokAlert && elBarangStokAlertText) {
                     elBarangStokAlert.className = 'alert alert-danger py-2 mt-2 mb-0';
                     elBarangStokAlertText.textContent = 'Stok tidak mencukupi (Tersedia: ' + window.currentStokBarang + ')';
                     elBarangStokAlert.style.display = 'block';
                 }
                 btnSaveBarangEl.disabled = true;
                 return;
             }
             
             // Semua validasi passed - sembunyikan alert dan lanjutkan
             if (elBarangStokAlert) {
                 elBarangStokAlert.style.display = 'none';
            }
            
            const subtotal = jumlah * harga;
            const discountAmount = subtotal * (discount / 100);
            const total = subtotal - discountAmount;
            
            detailBarangCounter++;
            detailBarangData.push({
                id: detailBarangCounter,
                KodeBarang: window.currentBarangData.KodeBarang,
                NamaBarang: window.currentBarangData.NamaBarang,
                Satuan: window.currentBarangData.Satuan,
                Merek: window.currentBarangData.NamaMerek || '-',
                Jenis: window.currentBarangData.NamaJenis || '-',
                Jumlah: jumlah,
                HargaSatuan: harga,
                Discount: discount,
                DiscountRupiah: discountAmount,
                TotalHarga: total
            });
            
            renderBarangTable();
            calculateTotals();
            modal.hide();
        });
         }
    }
    
    // Render Jasa Table (assigned to global window)
    window.renderJasaTable = function() {
        const tbody = document.getElementById('tbodyJasa');
        tbody.innerHTML = '';
        
        if (detailJasaData.length === 0) {
            tbody.innerHTML = '<tr class="text-center"><td colspan="9" class="text-muted">Belum ada data jasa</td></tr>';
            return;
        }
        
        detailJasaData.forEach((jasa, index) => {
            tbody.insertAdjacentHTML('beforeend', `
                <tr>
                    <td>${index + 1}</td>
                    <td><small><strong>${jasa.KodeJasa}</strong><br>${jasa.NamaJasa}</small></td>
                    <td>${jasa.Satuan}</td>
                    <td>${jasa.Kategori}</td>
                    <td class="text-end">${jasa.Jumlah}</td>
                    <td class="text-end">Rp ${formatNumber(jasa.HargaSatuan)}</td>
                    <td class="text-end">${jasa.Discount}%</td>
                    <td class="text-end">Rp ${formatNumber(jasa.TotalHarga)}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeJasa(${jasa.id})">
                            <i class="fas fa-trash-can"></i>
                        </button>
                    </td>
                </tr>
            `);
        });
    };
    
    // Render Barang Table (assigned to global window)
    window.renderBarangTable = function() {
        const tbody = document.getElementById('tbodyBarang');
        tbody.innerHTML = '';
        
        if (detailBarangData.length === 0) {
            tbody.innerHTML = '<tr class="text-center"><td colspan="10" class="text-muted">Belum ada data barang</td></tr>';
            return;
        }
        
        detailBarangData.forEach((barang, index) => {
            tbody.insertAdjacentHTML('beforeend', `
                <tr>
                    <td>${index + 1}</td>
                    <td><small><strong>${barang.KodeBarang}</strong><br>${barang.NamaBarang}</small></td>
                    <td>${barang.Satuan}</td>
                    <td>${barang.Merek}</td>
                    <td>${barang.Jenis}</td>
                    <td class="text-end">${barang.Jumlah}</td>
                    <td class="text-end">Rp ${formatNumber(barang.HargaSatuan)}</td>
                    <td class="text-end">${barang.Discount}%</td>
                    <td class="text-end">Rp ${formatNumber(barang.TotalHarga)}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeBarang(${barang.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `);
        });
    };
    
    // Variables for delete confirmation
    let pendingDeleteItem = null;
    let pendingDeleteType = null;
    
    // Remove Jasa
    window.removeJasa = function(id) {
        pendingDeleteItem = id;
        pendingDeleteType = 'jasa';
        confirmDeleteModal.show();
    };
    
    // Remove Barang
    window.removeBarang = function(id) {
        pendingDeleteItem = id;
        pendingDeleteType = 'barang';
        confirmDeleteModal.show();
    };
    
    // Calculate Totals (assigned to global window)
    window.calculateTotals = function() {
        const totalJasa = detailJasaData.reduce((sum, item) => sum + item.TotalHarga, 0);
        const totalBarang = detailBarangData.reduce((sum, item) => sum + item.TotalHarga, 0);
        const grandTotal = totalJasa + totalBarang;
        
        document.getElementById('totalJasa').textContent = 'Rp ' + formatNumber(totalJasa);
        document.getElementById('totalBarang').textContent = 'Rp ' + formatNumber(totalBarang);
        document.getElementById('grandTotal').textContent = 'Rp ' + formatNumber(grandTotal);
    };
    
    // Format Number (assigned to global window)
    window.formatNumber = function(num) {
        return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    };
    
    // Reset Form
    function resetForm() {
        document.getElementById('formWorkOrder').reset();
        customerChoice.removeActiveItems();
        kendaraanChoice.removeActiveItems();
        montirChoice.removeActiveItems();
        pickerChoice.removeActiveItems();
        document.getElementById('customerInfo').style.display = 'none';
        document.getElementById('kendaraanInfo').style.display = 'none';
        detailJasaData = [];
        detailBarangData = [];
        renderJasaTable();
        renderBarangTable();
        calculateTotals();
        
        // Reset edit mode
        currentEditNoOrder = '';
        
        // Reset form title and button text
        document.querySelector('#formSection h5').innerHTML = '<i class="fa-solid fa-plus-circle me-2"></i>Buat Work Order Baru';
        document.getElementById('btnSave').innerHTML = '<i class="fas fa-save me-1"></i>Simpan Work Order';
    }
    
    // Submit Form
    document.getElementById('formWorkOrder').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate
        const kodeCustomer = document.getElementById('selectCustomer').value;
        const kodeKendaraan = document.getElementById('selectKendaraan').value;
        const kodeMontir = document.getElementById('selectMontir').value;
        const kodePicker = document.getElementById('selectPicker').value;
        
        if (!kodeCustomer) {
            alert('Customer harus diisi!');
            return;
        }
        
        if (!kodeKendaraan) {
            alert('Kendaraan harus diisi!');
            return;
        }
        
        if (!kodeMontir) {
            alert('Montir (Mekanik) harus diisi!');
            return;
        }
        
        if (!kodePicker) {
            alert('Picker (Marketing) harus diisi!');
            return;
        }
        
        if (detailJasaData.length === 0 && detailBarangData.length === 0) {
            showAlert('Minimal harus ada 1 detail jasa atau barang!', 'warning');
            return;
        }
        
        // Show confirmation modal with data summary
        const totalJasa = detailJasaData.reduce((sum, item) => sum + item.TotalHarga, 0);
        const totalBarang = detailBarangData.reduce((sum, item) => sum + item.TotalHarga, 0);
        const totalOrder = totalJasa + totalBarang;
        
        // Get customer and vehicle names from Choices.js
        const customerValue = customerChoice.getValue(true);
        const kendaraanValue = kendaraanChoice.getValue(true);
        
        // Get text labels
        let namaCustomer = '-';
        let namaKendaraan = '-';
        
        if (customerValue) {
            const customerItems = customerChoice._currentState.choices.filter(c => c.value === customerValue);
            if (customerItems.length > 0) {
                namaCustomer = customerItems[0].label;
            }
        }
        
        if (kendaraanValue) {
            const kendaraanItems = kendaraanChoice._currentState.choices.filter(c => c.value === kendaraanValue);
            if (kendaraanItems.length > 0) {
                namaKendaraan = kendaraanItems[0].label;
            }
        }
        
        // Populate confirmation modal
        document.getElementById('confirm_customer').textContent = namaCustomer;
        document.getElementById('confirm_kendaraan').textContent = namaKendaraan;
        document.getElementById('confirm_totaljasa').textContent = 'Rp ' + formatNumber(totalJasa);
        document.getElementById('confirm_totalbarang').textContent = 'Rp ' + formatNumber(totalBarang);
        document.getElementById('confirm_totalorder').textContent = 'Rp ' + formatNumber(totalOrder);
        
        // Show modal
        confirmSaveModal.show();
    });
    
    // Initialize Flatpickr for date input with DD/MM/YYYY format
    const filterDateInput = document.getElementById('filterDate');
    
    flatpickr(filterDateInput, {
        dateFormat: "d/m/Y",
        allowInput: true,
        disableMobile: true,  // Disable mobile mode to prevent duplication
        wrap: false,          // Don't wrap the input
        locale: {
            firstDayOfWeek: 1,
            weekdays: {
                shorthand: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                longhand: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
            },
            months: {
                shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                longhand: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
            }
        },
        onChange: function(selectedDates, dateStr, instance) {
            if (dateStr) {
                const search = document.getElementById('searchInput').value;
                const dbDate = convertDateToDb(dateStr);
                window.location.href = `${basePath}/transaksi-work-order?search=${encodeURIComponent(search)}&date=${encodeURIComponent(dbDate)}`;
            }
        }
    });
    
    // Function to convert DD/MM/YYYY to YYYY-MM-DD
    function convertDateToDb(dateStr) {
        if (!dateStr || dateStr.length !== 10) return '';
        const parts = dateStr.split('/');
        if (parts.length !== 3) return '';
        return parts[2] + '-' + parts[1] + '-' + parts[0]; // YYYY-MM-DD
    }
    
    // Search functionality
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            const search = document.getElementById('searchInput').value;
            const date = convertDateToDb(document.getElementById('filterDate').value);
            window.location.href = `${basePath}/transaksi-work-order?search=${encodeURIComponent(search)}&date=${encodeURIComponent(date)}`;
        }, 500);
    });
    
    document.getElementById('btnClearFilter').addEventListener('click', function() {
        window.location.href = `${basePath}/transaksi-work-order`;
    });
    
    // ============= Modal Confirmation Event Listeners =============
    
    // Handle Confirm Save Button
    document.getElementById('btnConfirmSave').addEventListener('click', function() {
        const btnConfirm = this;
        btnConfirm.disabled = true;
        
        // Check if edit mode
        const isEditMode = currentEditNoOrder !== '';
        btnConfirm.innerHTML = isEditMode ? 
            '<i class="fa-solid fa-spinner fa-spin me-2"></i>Mengupdate...' : 
            '<i class="fa-solid fa-spinner fa-spin me-2"></i>Menyimpan...';
        
        // Prepare data
        const kodeCustomer = document.getElementById('selectCustomer').value;
        const kodeKendaraan = document.getElementById('selectKendaraan').value;
        const kodeMontir = document.getElementById('selectMontir').value;
        const kodePicker = document.getElementById('selectPicker').value;
        
        const totalJasa = detailJasaData.reduce((sum, item) => sum + item.TotalHarga, 0);
        const totalBarang = detailBarangData.reduce((sum, item) => sum + item.TotalHarga, 0);
        const totalOrder = totalJasa + totalBarang;
        
        const formData = {
            KodeCustomer: kodeCustomer,
            KodeKendaraan: kodeKendaraan,
            KodeMontir: kodeMontir,
            KodePicker: kodePicker,
            Keterangan: document.getElementById('inputKeterangan').value || '',
            KMAwal: parseInt(document.getElementById('inputKMAwal').value) || 0,
            KMAkhir: parseInt(document.getElementById('inputKMAkhir').value) || 0,
            TotalJasa: totalJasa,
            TotalBarang: totalBarang,
            TotalOrder: totalOrder,
            DetailJasa: detailJasaData,
            DetailBarang: detailBarangData
        };
        
        // Add NoOrder if edit mode
        if (isEditMode) {
            formData.NoOrder = currentEditNoOrder;
        }
        
        // Determine endpoint
        const endpoint = isEditMode ? 
            `${basePath}/transaksi-work-order/update` : 
            `${basePath}/transaksi-work-order/save`;
        
        // Send AJAX request
        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close modal
                confirmSaveModal.hide();
                
                // Show success modal
                const successTitle = isEditMode ? 'Work Order berhasil diupdate!' : 'Work Order berhasil disimpan!';
                showSuccessModal(successTitle, 'No Order: ' + data.NoOrder);
                
                // Reset edit mode
                currentEditNoOrder = '';
                
                // Enable btnNewOrder
                document.getElementById('btnNewOrder').disabled = false;
                
                // Redirect after 2 seconds
                setTimeout(() => {
                    window.location.href = `${basePath}/transaksi-work-order`;
                }, 2000);
            } else {
                showAlert('Gagal menyimpan work order: ' + (data.error || 'Unknown error'), 'danger');
                btnConfirm.disabled = false;
                btnConfirm.innerHTML = isEditMode ? 
                    '<i class="fa-solid fa-save me-2"></i>Ya, Update' : 
                    '<i class="fa-solid fa-save me-2"></i>Ya, Simpan';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan saat menyimpan data', 'danger');
            btnConfirm.disabled = false;
            btnConfirm.innerHTML = isEditMode ? 
                '<i class="fa-solid fa-save me-2"></i>Ya, Update' : 
                '<i class="fa-solid fa-save me-2"></i>Ya, Simpan';
        });
    });
    
    // Handle Confirm Delete Button
    document.getElementById('btnConfirmDelete').addEventListener('click', function() {
        if (pendingDeleteType === 'jasa') {
            detailJasaData = detailJasaData.filter(item => item.id !== pendingDeleteItem);
            renderJasaTable();
            calculateTotals();
        } else if (pendingDeleteType === 'barang') {
            detailBarangData = detailBarangData.filter(item => item.id !== pendingDeleteItem);
            renderBarangTable();
            calculateTotals();
        }
        
        // Reset pending delete
        pendingDeleteItem = null;
        pendingDeleteType = null;
        
        // Close modal
        confirmDeleteModal.hide();
    });
    
    // Handle Confirm Cancel Button
    document.getElementById('btnConfirmCancel').addEventListener('click', function() {
        // Set flag that cancel was confirmed
        // Modal will close automatically due to data-bs-dismiss attribute
        // The actual reset will happen in 'hidden.bs.modal' event
        cancelConfirmed = true;
    });
    
    // Helper function to show alert (inside DOMContentLoaded for access to basePath)
    window.showAlert = function(message, type = 'info') {
        // Create alert element
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.setAttribute('role', 'alert');
        alertDiv.innerHTML = `
            <i class="fa-solid fa-${type === 'warning' ? 'exclamation-triangle' : type === 'danger' ? 'times-circle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Insert at top of form section
        const formSection = document.getElementById('formSection');
        if (formSection) {
            formSection.insertBefore(alertDiv, formSection.firstChild);
        } else {
            // If no form section, insert at top of container
            const container = document.querySelector('.container');
            if (container) {
                container.insertBefore(alertDiv, container.firstChild);
            }
        }
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            alertDiv.classList.remove('show');
            setTimeout(() => alertDiv.remove(), 150);
        }, 5000);
    };
    
    // Helper function to show success modal (inside DOMContentLoaded for access to basePath)
    window.showSuccessModal = function(title, message) {
        const modalHtml = `
            <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">
                                <i class="fa-solid fa-check-circle me-2"></i>${title}
                            </h5>
                        </div>
                        <div class="modal-body text-center py-4">
                            <i class="fa-solid fa-check-circle text-success" style="font-size: 3rem;"></i>
                            <h5 class="mt-3">${message}</h5>
                            <p class="text-muted">Halaman akan dimuat ulang...</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove old modal if exists
        const oldModal = document.getElementById('successModal');
        if (oldModal) oldModal.remove();
        
        // Append and show modal
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modalElement = document.getElementById('successModal');
        const modal = new bootstrap.Modal(modalElement, {
            backdrop: 'static',
            keyboard: false
        });
        modal.show();
    };
});

// Show Work Order Detail Modal
function showWorkOrderDetail(noOrder) {
    // Show modal
    detailModal.show();
    
    // Show loading spinner, hide content
    document.getElementById('loadingSpinner').style.display = 'block';
    document.getElementById('detailContent').style.display = 'none';
    
    // Fetch data via AJAX
    const basePath = '<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>';
    const url = basePath + '/transaksi-work-order/get-detail?noorder=' + encodeURIComponent(noOrder);
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('Error: ' + data.error);
                modal.hide();
                return;
            }
            
            // Populate header information
            document.getElementById('detail_noorder').textContent = data.header.NoOrder || '-';
            document.getElementById('detail_tanggal').textContent = data.header.TanggalOrder ? 
                formatDate(data.header.TanggalOrder) : '-';
            document.getElementById('detail_kendaraan').textContent = data.header.NamaKendaraan || '-';
            document.getElementById('detail_nopolisi').textContent = data.header.NoPolisi || '-';
            document.getElementById('detail_mekanik').textContent = data.header.NamaMontir || '-';
            document.getElementById('detail_marketing').textContent = data.header.NamaPicker || '-';
            document.getElementById('detail_customer').textContent = data.header.NamaCustomer || '-';
            document.getElementById('detail_alamat').textContent = data.header.AlamatCustomer || '-';
            document.getElementById('detail_telepon').textContent = data.header.NoTelepon || '-';
            document.getElementById('detail_kmawal').textContent = formatNumber(data.header.KMAwal || 0);
            document.getElementById('detail_kmakhir').textContent = formatNumber(data.header.KMAkhir || 0);
            document.getElementById('detail_totaljasa').innerHTML = '<strong>Rp ' + formatNumber(data.header.TotalJasa || 0) + '</strong>';
            document.getElementById('detail_totalbarang').innerHTML = '<strong>Rp ' + formatNumber(data.header.TotalBarang || 0) + '</strong>';
            document.getElementById('detail_totalorder').innerHTML = '<strong>Rp ' + formatNumber(data.header.TotalOrder || 0) + '</strong>';
            
            // Populate service transactions
            const servicesBody = document.getElementById('detail_services');
            servicesBody.innerHTML = '';
            
            if (data.jasa && data.jasa.length > 0) {
                data.jasa.forEach(jasa => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${escapeHtml(jasa.NamaJasa || '-')}</td>
                        <td>${escapeHtml(jasa.NamaKategori || '-')}</td>
                        <td class="text-center"><span class="badge bg-primary">${parseInt(jasa.Jumlah) || 0}</span></td>
                        <td class="text-end">Rp ${formatNumber(jasa.HargaSatuan || 0)}</td>
                        <td class="text-center">${jasa.Discount || 0}%</td>
                        <td class="text-end"><strong>Rp ${formatNumber(jasa.TotalHarga || 0)}</strong></td>
                    `;
                    servicesBody.appendChild(row);
                });
            } else {
                servicesBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Tidak ada data jasa</td></tr>';
            }
            
            // Populate item transactions
            const itemsBody = document.getElementById('detail_items');
            itemsBody.innerHTML = '';
            
            if (data.barang && data.barang.length > 0) {
                data.barang.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${escapeHtml(item.NamaBarang || '-')}</td>
                        <td>${escapeHtml(item.NamaMerek || '-')}</td>
                        <td class="text-center">${escapeHtml(item.Satuan || '-')}</td>
                        <td class="text-center"><span class="badge bg-success">${parseInt(item.Jumlah) || 0}</span></td>
                        <td class="text-end">Rp ${formatNumber(item.HargaSatuan || 0)}</td>
                        <td class="text-center">${item.Discount || 0}%</td>
                        <td class="text-end"><strong>Rp ${formatNumber(item.TotalHarga || 0)}</strong></td>
                    `;
                    itemsBody.appendChild(row);
                });
            } else {
                itemsBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Tidak ada data barang</td></tr>';
            }
            
            // Check if editable and show/hide edit button
            const statusOrder = parseInt(data.header.StatusOrder) || 0;
            const detailModalFooter = document.getElementById('detailModalFooter');
            const btnEditWorkOrder = document.getElementById('btnEditWorkOrder');
            const btnDownloadPDF = document.getElementById('btnDownloadPDF');
            
            // Show footer if StatusOrder <= 2 (untuk download PDF atau edit)
            if (statusOrder <= 2) {
                detailModalFooter.style.display = 'flex';
                
                // Show edit button only if StatusOrder < 2
                if (statusOrder < 2) {
                    btnEditWorkOrder.setAttribute('data-noorder', data.header.NoOrder);
                    btnEditWorkOrder.style.display = 'inline-block';
                } else {
                    btnEditWorkOrder.style.display = 'none';
                }
                
                // Show download PDF button for StatusOrder <= 2
                btnDownloadPDF.setAttribute('data-noorder', data.header.NoOrder);
                btnDownloadPDF.style.display = 'inline-block';
            } else {
                // Hide footer for StatusOrder > 2
                detailModalFooter.style.display = 'none';
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

// Download Work Order PDF Function
function downloadWorkOrderPDF() {
    const btnDownload = document.getElementById('btnDownloadPDF');
    const noOrder = btnDownload.getAttribute('data-noorder');
    
    if (!noOrder) {
        alert('NoOrder tidak ditemukan');
        return;
    }
    
    // Open PDF in new window/tab
    const basePath = '<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>';
    const pdfUrl = basePath + '/workorder?action=download_pdf&noorder=' + encodeURIComponent(noOrder);
    
    // Open in new tab
    window.open(pdfUrl, '_blank');
}

// Edit Work Order Function
function editWorkOrder() {
    const btnEdit = document.getElementById('btnEditWorkOrder');
    const noOrder = btnEdit.getAttribute('data-noorder');
    
    if (!noOrder) {
        alert('NoOrder tidak ditemukan');
        return;
    }
    
    // Store current NoOrder
    currentEditNoOrder = noOrder;
    
    // Close detail modal
    detailModal.hide();
    
    // Show loading indicator
    const basePath = '<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>';
    
    // Fetch data for edit
    fetch(basePath + '/transaksi-work-order/get-data-for-edit?noorder=' + encodeURIComponent(noOrder))
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('Error: ' + data.error);
                return;
            }
            
            // Populate form with data
            populateFormForEdit(data);
            
            // Show form section, hide list section
            document.getElementById('formSection').style.display = 'block';
            document.getElementById('listSection').style.display = 'none';
            
            // Disable btnNewOrder saat edit mode
            document.getElementById('btnNewOrder').disabled = true;
            
            // Scroll to top
            window.scrollTo(0, 0);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengambil data untuk edit');
        });
}

// Populate Form For Edit
function populateFormForEdit(data) {
    const header = data.header;
    
    // Set form title to edit mode
    document.querySelector('#formSection h5').innerHTML = '<i class="fa-solid fa-edit me-2"></i>Edit Work Order: ' + header.NoOrder;
    
    // Change button text
    document.getElementById('btnSave').innerHTML = '<i class="fas fa-save me-1"></i>Update Work Order';
    
    // Populate Customer
    customerChoice.setChoices([{
        value: header.KodeCustomer,
        label: header.NamaCustomer,
        selected: true
    }], 'value', 'label', true);
    
    // Trigger customer change to show info
    fetch('<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/transaksi-work-order/get-customer?code=' + encodeURIComponent(header.KodeCustomer))
        .then(response => response.json())
        .then(customer => {
            if (customer && customer.KodeCustomer) {
                document.getElementById('customerAlamat').textContent = customer.AlamatCustomer || '-';
                document.getElementById('customerKota').textContent = customer.Kota || '-';
                document.getElementById('customerTelepon').textContent = customer.NoTelepon || '-';
                document.getElementById('customerInfo').style.display = 'block';
            }
        });
    
    // Populate Kendaraan
    kendaraanChoice.setChoices([{
        value: header.KodeKendaraan,
        label: header.NamaKendaraan + ' - ' + header.NoPolisi,
        selected: true
    }], 'value', 'label', true);
    
    // Trigger kendaraan fetch to show info
    fetch('<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/transaksi-work-order/get-vehicle?code=' + encodeURIComponent(header.KodeKendaraan))
        .then(response => response.json())
        .then(vehicle => {
            if (vehicle && vehicle.KodeKendaraan) {
                document.getElementById('kendaraanNoPolisi').textContent = vehicle.NoPolisi || '-';
                document.getElementById('kendaraanMerek').textContent = vehicle.NamaMerek || '-';
                document.getElementById('kendaraanModel').textContent = vehicle.NamaJenis || '-';
                document.getElementById('kendaraanTipe').textContent = vehicle.Tipe || '-';
                document.getElementById('kendaraanTahun').textContent = vehicle.Tahun || '-';
                document.getElementById('kendaraanWarna').textContent = vehicle.Warna || '-';
                document.getElementById('kendaraanInfo').style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error fetching vehicle data:', error);
        });
    
    // Populate Montir
    montirChoice.setChoices([{
        value: header.KodeMontir,
        label: header.NamaMontir,
        selected: true
    }], 'value', 'label', true);
    
    // Populate Picker
    pickerChoice.setChoices([{
        value: header.KodePicker,
        label: header.NamaPicker,
        selected: true
    }], 'value', 'label', true);
    
    // Populate other fields
    document.getElementById('inputKMAwal').value = header.KMAwal || '';
    document.getElementById('inputKMAkhir').value = header.KMAkhir || '';
    document.getElementById('inputKeterangan').value = header.Keterangan || '';
    
    // Clear existing detail arrays
    detailJasaData = [];
    detailBarangData = [];
    detailJasaCounter = 0;
    detailBarangCounter = 0;
    
    // Populate Detail Jasa
    if (data.jasa && data.jasa.length > 0) {
        data.jasa.forEach(jasa => {
            detailJasaCounter++;
            detailJasaData.push({
                id: detailJasaCounter,
                KodeJasa: jasa.KodeJasa,
                NamaJasa: jasa.NamaJasa,
                Satuan: jasa.Satuan,
                KodeKategori: jasa.KodeKategori,
                NamaKategori: jasa.NamaKategori,
                Jumlah: parseFloat(jasa.Jumlah),
                HargaSatuan: parseFloat(jasa.HargaSatuan),
                Discount: parseFloat(jasa.Discount),
                TotalHarga: parseFloat(jasa.TotalHarga)
            });
        });
    }
    
    // Populate Detail Barang
    if (data.barang && data.barang.length > 0) {
        data.barang.forEach(barang => {
            detailBarangCounter++;
            detailBarangData.push({
                id: detailBarangCounter,
                KodeBarang: barang.KodeBarang,
                NamaBarang: barang.NamaBarang,
                Satuan: barang.Satuan,
                Merek: barang.NamaMerek,
                Jenis: barang.NamaJenis,
                Jumlah: parseFloat(barang.Jumlah),
                HargaSatuan: parseFloat(barang.HargaSatuan),
                Discount: parseFloat(barang.Discount),
                TotalHarga: parseFloat(barang.TotalHarga)
            });
        });
    }
    
    // Render tables
    renderJasaTable();
    renderBarangTable();
    calculateTotals();
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
    return Number(number).toLocaleString('id-ID');
}

// Helper function to escape HTML
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, m => map[m]);
}
</script>

<style>
.choices {
    width: 100%;
}

.table-responsive {
    overflow-x: auto;
}

.table th {
    white-space: nowrap;
}

.card-header h6 {
    font-weight: 600;
}

.info-empesis {
    background-color: #d1ecf1;
    border: 1px solid #bee5eb;
    color: #0c5460;
}

/* Button icon add styling */
.btn-icon-add {
    min-width: 45px;
    height: calc(2.25rem + 2px); /* Sama dengan tinggi .form-select */
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    vertical-align: top;
}

.btn-icon-add i {
    font-size: 1.25rem;
    line-height: 1;
}

/* Hide number input spinner (arrows) */
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type="number"] {
    -moz-appearance: textfield; /* Firefox */
    appearance: textfield;
}

/* Flatpickr custom styles for better mobile/tablet experience */
.flatpickr-calendar {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.flatpickr-input {
    background-color: #fff !important;
    cursor: pointer;
}

.flatpickr-input[readonly] {
    cursor: pointer;
    background-color: #fff !important;
}

/* Ensure proper display on tablet */
@media (max-width: 1024px) and (min-width: 768px) {
    .flatpickr-calendar {
        font-size: 16px;
    }
    
    .flatpickr-day {
        height: 38px;
        line-height: 38px;
        max-width: 38px;
    }
}

@media (max-width: 768px) {
    .table {
        font-size: 0.85rem;
    }

    .btn-sm {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .flatpickr-calendar {
        font-size: 14px;
    }
    
    .flatpickr-day {
        height: 36px;
        line-height: 36px;
        max-width: 36px;
    }
}
</style>
