<?php
class WorkOrderController {
    
    public function __construct() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            exit;
        }
    }
    
    /**
     * Display work order information page
     */
    public function index() {
        // Handle download PDF action
        if (isset($_GET['action']) && $_GET['action'] === 'download_pdf') {
            $this->downloadPDF();
            return;
        }
        
        // Handle AJAX requests first
        if (isset($_GET['ajax'])) {
            $this->handleAjax();
            return;
        }
        
        $user_data = $_SESSION['user_data'] ?? [];
        $user_name = $user_data['UserName'] ?? $user_data['Name'] ?? $user_data['user_name'] ?? $_SESSION['user_id'];
        
        // Get filter parameters
        $filters = [
            'start_date' => $_GET['start_date'] ?? date('Y-m-d'),
            'end_date' => $_GET['end_date'] ?? date('Y-m-d'),
            'status' => $_GET['status'] ?? '',
            'customer' => $_GET['customer'] ?? '',
            'no_polisi' => $_GET['no_polisi'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];
        
        // Pagination parameters
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = (int)($_GET['limit'] ?? 25);
        $offset = ($page - 1) * $limit;
        
        // Initialize WorkOrderModel
        $workOrderModel = new WorkOrderModel();
        
        // Get user type from session
        $tipeUser = isset($_SESSION['tipe_user']) ? (int)$_SESSION['tipe_user'] : null;
        $userID = $_SESSION['user_id'] ?? null;
        
        // Get work orders with filters (with role-based access)
        $workOrders = $workOrderModel->getWorkOrders($filters, $limit, $offset, $userID, $tipeUser);
        $totalWorkOrders = $workOrderModel->getTotalWorkOrders($filters, $userID, $tipeUser);
        
        // Get filter options
        $customers = $workOrderModel->getCustomers();
        $vehicles = $workOrderModel->getVehicles();
        
        // Status options
        $statusOptions = [
            '0' => 'Belum diproses',
            '1' => 'Sedang diproses', 
            '2' => 'Proses Selesai',
            '3' => 'Faktur dibuat',
            '4' => 'Telah dibayar',
            '5' => 'Dibatalkan'
        ];
        
        // Pagination calculation
        $totalPages = ceil($totalWorkOrders / $limit);
        $paginationOptions = [10, 25, 50, 100];
        
        $data = [
            'user_id' => $_SESSION['user_id'],
            'user_name' => $user_name,
            'user_data' => $user_data,
            'filters' => $filters,
            'workOrders' => $workOrders,
            'totalWorkOrders' => $totalWorkOrders,
            'customers' => $customers,
            'vehicles' => $vehicles,
            'statusOptions' => $statusOptions,
            'page' => $page,
            'limit' => $limit,
            'totalPages' => $totalPages,
            'paginationOptions' => $paginationOptions
        ];
        
        $this->renderView('workorder/index', $data);
    }
    
    /**
     * Handle AJAX requests
     */
    public function handleAjax() {
        if (isset($_GET['ajax'])) {
            switch ($_GET['ajax']) {
                case 'get_customers':
                    $this->searchCustomers();
                    break;
                case 'get_vehicles':
                    $this->searchVehicles();
                    break;
                case 'get_detail':
                    $this->getWorkOrderDetail();
                    break;
                default:
                    http_response_code(404);
                    echo json_encode(['error' => 'AJAX action not found']);
                    exit;
            }
        }
    }
    
    /**
     * Handle AJAX request for work order detail
     */
    public function getWorkOrderDetail() {
        // Clear any previous output
        if (ob_get_level()) {
            ob_clean();
        }
        
        $noOrder = $_GET['noorder'] ?? '';
        
        if (empty($noOrder)) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'No Order tidak ditemukan']);
            exit;
        }
        
        try {
            $workOrderModel = new WorkOrderModel();
            $detail = $workOrderModel->getWorkOrderDetail($noOrder);
            
            if ($detail) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode($detail, JSON_UNESCAPED_UNICODE);
            } else {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['error' => 'Data tidak ditemukan']);
            }
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Terjadi kesalahan saat mengambil data']);
            exit;
        }
    }
    
    /**
     * Handle AJAX requests for customer search
     */
    public function searchCustomers() {
        // Clear any previous output
        if (ob_get_level()) {
            ob_clean();
        }
        
        $search = $_GET['search'] ?? '';
        
        try {
            $workOrderModel = new WorkOrderModel();
            $customers = $workOrderModel->searchCustomers($search);
            
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($customers, JSON_UNESCAPED_UNICODE);
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Terjadi kesalahan saat mencari customer']);
            exit;
        }
    }
    
    /**
     * Handle AJAX requests for vehicle search
     */
    public function searchVehicles() {
        // Clear any previous output
        if (ob_get_level()) {
            ob_clean();
        }
        
        $search = $_GET['search'] ?? '';
        
        try {
            $workOrderModel = new WorkOrderModel();
            $vehicles = $workOrderModel->searchVehicles($search);
            
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($vehicles, JSON_UNESCAPED_UNICODE);
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Terjadi kesalahan saat mencari kendaraan']);
            exit;
        }
    }
    
    /**
     * Download Work Order as PDF
     */
    public function downloadPDF() {
        // Clear any previous output
        if (ob_get_level()) {
            ob_clean();
        }
        
        $noOrder = $_GET['noorder'] ?? '';
        
        if (empty($noOrder)) {
            echo "No Order tidak ditemukan";
            exit;
        }
        
        try {
            $workOrderModel = new WorkOrderModel();
            $detail = $workOrderModel->getWorkOrderDetail($noOrder);
            
            if (!$detail) {
                echo "Data Work Order tidak ditemukan";
                exit;
            }
            
            // Generate PDF content
            $this->generatePDF($detail);
            
        } catch (Exception $e) {
            echo "Terjadi kesalahan: " . $e->getMessage();
            exit;
        }
    }
    
    /**
     * Generate PDF from work order data
     */
    private function generatePDF($detail) {
        $header = $detail['header'];
        $services = $detail['services'] ?? [];
        $items = $detail['items'] ?? [];
        
        // Set HTML headers for print view
        header('Content-Type: text/html; charset=UTF-8');
        
        // Start HTML for PDF (using browser's print to PDF functionality)
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Work Order - <?php echo htmlspecialchars($header['NoOrder']); ?></title>
            <style>
                @page {
                    size: A4;
                    margin: 15mm;
                }
                body {
                    font-family: Arial, sans-serif;
                    font-size: 11pt;
                    color: #000;
                    margin: 0;
                    padding: 20px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                    border-bottom: 2px solid #000;
                    padding-bottom: 10px;
                }
                .header h1 {
                    margin: 0;
                    font-size: 20pt;
                }
                .header p {
                    margin: 5px 0;
                }
                .info-section {
                    margin-bottom: 20px;
                }
                .info-table {
                    width: 100%;
                    border-collapse: collapse;
                }
                .info-table td {
                    padding: 5px;
                    vertical-align: top;
                }
                .info-table td:first-child {
                    width: 30%;
                    font-weight: bold;
                }
                .data-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }
                .data-table th {
                    background-color: #333;
                    color: white;
                    padding: 8px;
                    text-align: left;
                    border: 1px solid #000;
                }
                .data-table td {
                    padding: 6px;
                    border: 1px solid #000;
                }
                .section-title {
                    font-size: 14pt;
                    font-weight: bold;
                    margin-top: 20px;
                    margin-bottom: 10px;
                    color: #333;
                }
                .text-right {
                    text-align: right;
                }
                .text-center {
                    text-align: center;
                }
                .total-section {
                    margin-top: 20px;
                    text-align: right;
                }
                .total-section table {
                    margin-left: auto;
                    min-width: 300px;
                }
                .total-section td {
                    padding: 5px 10px;
                }
                .grand-total {
                    font-weight: bold;
                    font-size: 14pt;
                    border-top: 2px solid #000;
                }
                @media print {
                    body {
                        margin: 0;
                        padding: 10mm;
                    }
                    .no-print {
                        display: none;
                    }
                }
            </style>
        </head>
        <body>
            <!-- Header -->
            <div class="header">
                <h1>WORK ORDER</h1>
                <p><strong>No. Order: <?php echo htmlspecialchars($header['NoOrder']); ?></strong></p>
                <p>Tanggal: <?php echo date('d/m/Y', strtotime($header['TanggalOrder'])); ?></p>
            </div>
            
            <!-- Customer & Vehicle Information -->
            <div class="info-section">
                <table class="info-table">
                    <tr>
                        <td colspan="2" style="font-size: 12pt; font-weight: bold; padding-bottom: 10px;">INFORMASI CUSTOMER & KENDARAAN</td>
                    </tr>
                    <tr>
                        <td>Customer</td>
                        <td>: <?php echo htmlspecialchars($header['NamaCustomer'] ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>: <?php echo htmlspecialchars($header['AlamatCustomer'] ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <td>No. Telepon</td>
                        <td>: <?php echo htmlspecialchars($header['NoTelepon'] ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <td>Kendaraan</td>
                        <td>: <?php echo htmlspecialchars($header['NamaKendaraan'] ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <td>No. Polisi</td>
                        <td>: <?php echo htmlspecialchars($header['NoPolisi'] ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <td>Warna</td>
                        <td>: <?php echo htmlspecialchars($header['Warna'] ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <td>Marketing</td>
                        <td>: <?php echo htmlspecialchars($header['Marketing'] ?? '-'); ?></td>
                    </tr>
                </table>
            </div>
            
            <!-- Service Transactions -->
            <?php if (!empty($services)): ?>
            <div class="section-title">TRANSAKSI SERVICE</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 40%;">Nama Jasa</th>
                        <th style="width: 25%;">Mekanik</th>
                        <th style="width: 10%;" class="text-center">QTY</th>
                        <th style="width: 20%;" class="text-right">Tarif</th>
                        <th style="width: 20%;" class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    foreach ($services as $service): 
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($service['NamaJasa'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($service['Mekanik'] ?? '-'); ?></td>
                        <td class="text-center"><?php echo (int)($service['Qty'] ?? 0); ?></td>
                        <td class="text-right"><?php echo number_format($service['Tarif'] ?? 0, 0, ',', '.'); ?></td>
                        <td class="text-right"><?php echo number_format($service['Total'] ?? 0, 0, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
            
            <!-- Item Transactions -->
            <?php if (!empty($items)): ?>
            <div class="section-title">TRANSAKSI BARANG</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 35%;">Nama Barang</th>
                        <th style="width: 20%;">Merek</th>
                        <th style="width: 10%;" class="text-center">Satuan</th>
                        <th style="width: 10%;" class="text-center">QTY</th>
                        <th style="width: 20%;" class="text-right">Harga</th>
                        <th style="width: 20%;" class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    foreach ($items as $item): 
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($item['NamaBarang'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($item['MerekBarang'] ?? '-'); ?></td>
                        <td class="text-center"><?php echo htmlspecialchars($item['Satuan'] ?? '-'); ?></td>
                        <td class="text-center"><?php echo (int)($item['Qty'] ?? 0); ?></td>
                        <td class="text-right"><?php echo number_format($item['Harga'] ?? 0, 0, ',', '.'); ?></td>
                        <td class="text-right"><?php echo number_format($item['Total'] ?? 0, 0, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
            
            <!-- Total Section -->
            <div class="total-section">
                <table>
                    <tr>
                        <td>Total Jasa</td>
                        <td style="width: 20px;">:</td>
                        <td class="text-right" style="min-width: 150px;"><strong>Rp <?php echo number_format($header['TotalJasa'] ?? 0, 0, ',', '.'); ?></strong></td>
                    </tr>
                    <tr>
                        <td>Total Barang</td>
                        <td>:</td>
                        <td class="text-right"><strong>Rp <?php echo number_format($header['TotalBarang'] ?? 0, 0, ',', '.'); ?></strong></td>
                    </tr>
                    <tr class="grand-total">
                        <td style="padding-top: 10px;">TOTAL ORDER</td>
                        <td style="padding-top: 10px;">:</td>
                        <td class="text-right" style="padding-top: 10px; color: #c00;">Rp <?php echo number_format($header['TotalOrder'] ?? 0, 0, ',', '.'); ?></td>
                    </tr>
                </table>
            </div>
            
            <div class="no-print" style="margin-top: 30px; text-align: center;">
                <button onclick="window.print()" style="padding: 10px 30px; font-size: 14pt; cursor: pointer; background: #007bff; color: white; border: none; border-radius: 5px;">
                    Print / Save as PDF
                </button>
                <button onclick="window.close()" style="padding: 10px 30px; font-size: 14pt; cursor: pointer; background: #6c757d; color: white; border: none; border-radius: 5px; margin-left: 10px;">
                    Close
                </button>
            </div>
            
            <script>
                // Auto print when page loads (optional)
                // window.onload = function() { window.print(); }
            </script>
        </body>
        </html>
        <?php
        exit;
    }
    
    /**
     * Render view template
     */
    private function renderView($view, $data = []) {
        $viewFile = BASE_PATH . '/app/views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            // Extract data array to variables
            extract($data);
            
            // Start output buffering
            ob_start();
            include $viewFile;
            $content = ob_get_clean();
            
            // Include layout if it exists
            $layoutFile = BASE_PATH . '/app/views/layouts/main.php';
            if (file_exists($layoutFile)) {
                include $layoutFile;
            } else {
                echo $content;
            }
        } else {
            echo "View not found: " . $view;
        }
    }
    
    /**
     * Redirect helper method
     */
    private function redirect($path) {
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        $fullPath = $basePath . $path;
        header('Location: ' . $fullPath);
    }
}
?>
