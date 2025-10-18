<?php
$title = 'Informasi Kendaraan';
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
                    <h4 class="mb-0"><i class="fa-solid fa-car me-2"></i>Informasi Kendaraan</h4>
                    <button type="button" class="btn" onclick="window.location.href='<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/dashboard'" title="Kembali" style="vertical-align: middle;">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <hr>
        
        <!-- Vehicle Selection Form -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card-body">
                    <?php if ($selected_vehicle): ?>
                        <!-- Selected Vehicle Display -->
                        <div class="row">
                            <div class="col-md-8">
                                <div class="selected-vehicle">
                                    <div class="vehicle-info">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Nama:</strong> <?php echo htmlspecialchars($selected_vehicle['name']); ?><br>
                                                <strong>No Polisi:</strong> <?php echo htmlspecialchars($selected_vehicle['no_polisi']); ?><br>
                                                <strong>Merek:</strong> <?php echo htmlspecialchars($selected_vehicle['merek']); ?>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Customer:</strong> <?php echo htmlspecialchars($selected_vehicle['customer']); ?><br>
                                                <strong>Kode:</strong> <?php echo htmlspecialchars($selected_vehicle['code']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-outline-primary" onclick="openVehicleModal()">
                                        <i class="fas fa-search me-2"></i>
                                        Ganti Kendaraan
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" onclick="clearVehicle()">
                                        <i class="fas fa-times me-2"></i>
                                        Reset Informasi
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Vehicle Selection -->
                        <div class="text-center py-4">
                            <i class="fas fa-car fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada kendaraan yang dipilih</h5>
                            <p class="text-muted">Klik tombol di bawah untuk mencari dan memilih kendaraan</p>
                            <button type="button" class="btn btn-primary" onclick="openVehicleModal()">
                                <i class="fas fa-search me-2"></i>
                                Pilih Kendaraan
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php if ($selected_vehicle): ?>
            <!-- Vehicle Transactions -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card-header">
                        <h5 class="mb-2">
                            <i class="fas fa-list me-2"></i>
                            Daftar Transaksi Kendaraan
                        </h5>
                    </div>
                    <div id="transactionsContainer">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Memuat data transaksi...</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Work Order Details -->
            <div class="row">
                <div class="col-12">
                    <h5 class="mb-0">
                        <i class="fas fa-wrench me-2"></i>
                        Detail Work Order
                    </h5>
                    <div id="workorderContainer">
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-mouse-pointer fa-2x mb-2"></i>
                            <p>Klik salah satu transaksi di atas untuk melihat detail work order</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Vehicle Search Modal -->
<div class="modal fade" id="vehicleModal" tabindex="-1" aria-labelledby="vehicleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width: 90%; width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vehicleModalLabel">
                    <i class="fas fa-search me-2"></i>
                    Pilih Kendaraan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Search Form -->
                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label">Kendaraan <span class="text-danger">*</span></label>
                        <select class="form-select" id="selectVehicleModal" name="KodeKendaraan" required>
                            <option value="">Pilih Kendaraan...</option>
                        </select>
                        <div class="mb-2" style="margin-top: -15px;" id="vehicleInfoModal" style="display: none;">
                            <div class="info-empesis p-2 rounded">
                                <small class="text-muted">
                                    <div>
                                        <strong>No Polisi:</strong>
                                        <span id="vehicleNoPolisiModal">-</span>
                                    </div>
                                    <div>
                                        <strong>Tipe:</strong>
                                        <span id="vehicleTipeModal">-</span>
                                    </div>
                                    <div>
                                        <strong>Tahun:</strong>
                                        <span id="vehicleTahunModal">-</span> |
                                        <strong>Warna:</strong>
                                        <span id="vehicleWarnaModal">-</span>
                                    </div>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="btnSelectVehicleModal">Pilih Kendaraan</button>
            </div>
        </div>
    </div>
</div>

<!-- Choices.js CSS -->
<link href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/assets/css/choices.min.css" rel="stylesheet">

<!-- Choices.js JS -->
<script src="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/assets/js/choices.min.js"></script>

<script>
let selectedVehicleCode = '<?php echo $selected_vehicle['code'] ?? ''; ?>';
let vehicleChoice;

// Initialize Choices.js for vehicle modal
function initializeVehicleModal() {
    const selectElement = document.getElementById('selectVehicleModal');
    if (selectElement && !vehicleChoice) {
        vehicleChoice = new Choices(selectElement, {
            searchEnabled: true,
            itemSelectText: '',
            searchResultLimit: 50,
            renderChoiceLimit: 50,
            noResultsText: 'Tidak ada kendaraan ditemukan',
            noChoicesText: 'Masukkan kata kunci untuk mencari kendaraan'
        });
    }
}

// Open vehicle modal
function openVehicleModal() {
    initializeVehicleModal();
    const modal = new bootstrap.Modal(document.getElementById('vehicleModal'));
    modal.show();
}

// Load vehicles on search
function loadVehicles(searchTerm) {
    if (searchTerm.length >= 2) {
        fetch(`?ajax=search_vehicle&search=${encodeURIComponent(searchTerm)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data && data.length > 0) {
                    const choices = data.map(vehicle => ({
                        value: vehicle.KodeKendaraan,
                        label: vehicle.NamaKendaraan + (vehicle.NoPolisi ? ' - ' + vehicle.NoPolisi : ''),
                        customProperties: vehicle
                    }));
                    vehicleChoice.clearChoices();
                    vehicleChoice.setChoices(choices, 'value', 'label', true);
                } else {
                    vehicleChoice.clearChoices();
                }
            })
            .catch(error => {
                console.error('Error loading vehicles:', error);
                vehicleChoice.clearChoices();
            });
    }
}

// Select vehicle from modal
function selectVehicleFromModal() {
    const selectedValue = vehicleChoice.getValue().value;
    if (selectedValue) {
        // Get the selected choice data
        const selectedChoice = vehicleChoice._currentState.choices.find(choice => choice.value === selectedValue);
        const vehicleData = selectedChoice ? selectedChoice.customProperties : null;
        
        if (!vehicleData) {
            console.error('Vehicle data not found');
            return;
        }
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '?action=select_vehicle';
        
        const fields = {
            'vehicle_code': vehicleData.KodeKendaraan,
            'vehicle_name': vehicleData.NamaKendaraan,
            'vehicle_no_polisi': vehicleData.NoPolisi,
            'vehicle_merek': vehicleData.NamaMerek || '',
            'vehicle_customer': vehicleData.NamaCustomer || ''
        };
        
        Object.keys(fields).forEach(key => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = fields[key];
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Clear vehicle selection
function clearVehicle() {
    window.location.href = '?action=clear_vehicle';
}

// Load vehicle transactions
function loadVehicleTransactions() {
    if (selectedVehicleCode) {
        const container = document.getElementById('transactionsContainer');
        
        fetch(`?ajax=get_vehicle_transactions&vehicle=${encodeURIComponent(selectedVehicleCode)}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    let html = `
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover main-table">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No Order</th>
                                        <th>Tanggal Order</th>
                                        <th>Nama Kendaraan</th>
                                        <th>No Polisi</th>
                                        <th>KM Awal</th>
                                        <th>Warna</th>
                                        <th>Marketing</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    
                    data.forEach(transaction => {
                        html += `
                            <tr onclick="loadWorkOrderDetails('${transaction.NoOrder}', '${selectedVehicleCode}')" style="cursor: pointer;">
                                <td>${transaction.NoOrder}</td>
                                <td>${formatDate(transaction.TanggalOrder)}</td>
                                <td>${transaction.NamaKendaraan}</td>
                                <td><span class="badge bg-primary">${transaction.NoPolisi}</span></td>
                                <td>${transaction.KMAwal || '-'}</td>
                                <td>${transaction.Warna}</td>
                                <td>${transaction.NamaMarketing}</td>
                            </tr>
                        `;
                    });
                    
                    html += `
                                </tbody>
                            </table>
                        </div>
                    `;
                    container.innerHTML = html;
                } else {
                    container.innerHTML = `
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>Tidak ada transaksi kendaraan ditemukan</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                container.innerHTML = `
                    <div class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <p>Terjadi kesalahan saat memuat data transaksi</p>
                    </div>
                `;
            });
    }
}

// Load work order details
function loadWorkOrderDetails(noOrder, vehicleCode) {
    const container = document.getElementById('workorderContainer');
    
    container.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Memuat detail work order...</p>
        </div>
    `;
    
    fetch(`?ajax=get_workorder&no_order=${encodeURIComponent(noOrder)}&vehicle=${encodeURIComponent(vehicleCode)}`)
        .then(response => response.json())
        .then(data => {
            let html = `
                <!-- Work Order Information -->
                <div class="row mt-2 mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <strong>Nomor WO:</strong> ${noOrder}
                                        </div>
                                        <div class="mb-2">
                                            <strong>Tanggal Order:</strong> ${data.workOrderInfo ? formatDate(data.workOrderInfo.TanggalOrder) : '-'}
                                        </div>
                                        <div class="mb-2">
                                            <strong>Kendaraan:</strong> ${data.workOrderInfo ? data.workOrderInfo.NamaKendaraan : '-'}
                                        </div>
                                        <div class="mb-2">
                                            <strong>Warna:</strong> ${data.workOrderInfo ? data.workOrderInfo.Warna : '-'}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <strong>Marketing:</strong> ${data.workOrderInfo ? data.workOrderInfo.NamaMarketing : '-'}
                                        </div>
                                        <div class="mb-2">
                                            <strong>No.Invoice:</strong> ${data.workOrderInfo ? data.workOrderInfo.NoPenjualan : '-'}
                                        </div>
                                        <div class="mb-2">
                                            <strong>Tgl.Invoice:</strong> ${data.workOrderInfo ? formatDate(data.workOrderInfo.TanggalPenjualan) : '-'}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Service Transactions
            if (data.service && data.service.length > 0) {
                html += `
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-success mb-2">
                                <i class="fas fa-wrench me-2"></i>
                                Transaksi Service
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover main-table">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Nama Jasa</th>
                                            <th>Mekanik/Montir</th>
                                            <th class="text-center">QTY</th>
                                            <th class="text-end">Tarif</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                `;
                
                data.service.forEach(service => {
                    html += `
                        <tr>
                            <td>${service.NamaJasa}</td>
                            <td>${service.NamaMontir || service.NamaMarketing || '-'}</td>
                            <td class="text-center"><span class="badge bg-primary">${Math.floor(service.Jumlah || service.Qty || 0)}</span></td>
                            <td class="text-end">${formatCurrency(service.HargaSatuan)}</td>
                            <td class="text-end"><strong>${formatCurrency(service.TotalHarga)}</strong></td>
                        </tr>
                    `;
                });
                
                html += `
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            // Parts Transactions
            if (data.parts && data.parts.length > 0) {
                html += `
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-warning mb-2">
                                <i class="fas fa-cogs me-2"></i>
                                Transaksi Barang
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover main-table">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Nama Barang</th>
                                            <th>Merek/Jenis</th>
                                            <th>Satuan</th>
                                            <th class="text-center">QTY</th>
                                            <th class="text-end">Harga</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                `;
                
                data.parts.forEach(part => {
                    html += `
                        <tr>
                            <td>${part.NamaBarang}</td>
                            <td>${part.MerekBarang}/${part.JenisBarang}</td>
                            <td>${part.Satuan}</td>
                            <td class="text-center"><span class="badge bg-success">${Math.floor(part.Jumlah || part.Qty || 0)}</span></td>
                            <td class="text-end">${formatCurrency(part.HargaSatuan)}</td>
                            <td class="text-end"><strong>${formatCurrency(part.TotalHarga)}</strong></td>
                        </tr>
                    `;
                });
                
                html += `
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            if ((!data.service || data.service.length === 0) && (!data.parts || data.parts.length === 0)) {
                html += `
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p>Tidak ada detail work order ditemukan</p>
                    </div>
                `;
            }
            
            container.innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            container.innerHTML = `
                <div class="text-center py-4 text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p>Terjadi kesalahan saat memuat detail work order</p>
                </div>
            `;
        });
}

// Utility functions
function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    
    // Format: dd/mm/yyyy dengan padding 0
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    
    return `${day}/${month}/${year}`;
}

function formatCurrency(amount) {
    if (!amount) return 'Rp 0';
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    if (selectedVehicleCode) {
        loadVehicleTransactions();
    }
    
    // Initialize vehicle modal Choices.js
    initializeVehicleModal();
    
    // Add event listener for vehicle search
    document.getElementById('selectVehicleModal').addEventListener('search', function(e) {
        const searchTerm = e.detail.value;
        if (searchTerm.length >= 2) {
            loadVehicles(searchTerm);
        }
    });
    
    // Add event listener for vehicle selection
    document.getElementById('selectVehicleModal').addEventListener('choice', function(e) {
        const selectedChoice = e.detail.choice;
        const selectedData = selectedChoice.customProperties;
        if (selectedData) {
            // Show vehicle info
            document.getElementById('vehicleNoPolisiModal').textContent = selectedData.NoPolisi || '-';
            document.getElementById('vehicleTipeModal').textContent = selectedData.NamaKendaraan || '-';
            document.getElementById('vehicleTahunModal').textContent = selectedData.Tahun || '-';
            document.getElementById('vehicleWarnaModal').textContent = selectedData.Warna || '-';
            document.getElementById('vehicleInfoModal').style.display = 'block';
        }
    });
    
    // Add event listener for select button
    document.getElementById('btnSelectVehicleModal').addEventListener('click', function() {
        selectVehicleFromModal();
    });
});
</script>

<style>
.info-empesis {
    background-color: #d1ecf1;
    border: 1px solid #bee5eb;
    color: #0c5460;
}
</style>
