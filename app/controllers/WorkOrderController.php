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
        // Check if Dompdf library exists
        $dompdfPath = BASE_PATH . '/libs/dompdf/autoload.inc.php';
        
        if (!file_exists($dompdfPath)) {
            echo "Error: Dompdf library not found! Please install Dompdf to generate PDF.";
            exit;
        }
        
        // Generate PDF using Dompdf
        $this->generatePDFWithDompdf($detail);
    }
    
    /**
     * Generate PDF using Dompdf library (Auto Download)
     */
    private function generatePDFWithDompdf($detail) {
        require_once BASE_PATH . '/libs/dompdf/autoload.inc.php';
        
        $header = $detail['header'];
        $services = $detail['services'] ?? [];
        $items = $detail['items'] ?? [];
        
        // Create HTML content
        $html = $this->getPDFHTMLContent($header, $services, $items);
        
        // Initialize Dompdf
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');
        
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Output PDF for download
        $filename = 'WorkOrder_' . $header['NoOrder'] . '_' . date('Ymd_His') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }
    
    /**
     * Get PDF HTML Content (for Dompdf)
     */
    private function getPDFHTMLContent($header, $services, $items) {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
            <title>Work Order - <?php echo htmlspecialchars($header['NoOrder']); ?></title>
            <style>
                @page {
                    margin: 15mm;
                }
                body {
                    font-family: 'DejaVu Sans', Arial, sans-serif;
                    font-size: 10pt;
                    color: #000;
                    margin: 0;
                    padding: 0;
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                    border-bottom: 2px solid #000;
                    padding-bottom: 10px;
                }
                .header h1 {
                    margin: 0 0 5px 0;
                    font-size: 18pt;
                    font-weight: bold;
                }
                .header p {
                    margin: 3px 0;
                    font-size: 10pt;
                }
                .info-section {
                    margin-bottom: 15px;
                }
                .info-table {
                    width: 100%;
                    border-collapse: collapse;
                }
                .info-table td {
                    padding: 2px;
                    vertical-align: top;
                    font-size: 9pt;
                }
                .info-table td:first-child {
                    width: 30%;
                    font-weight: bold;
                }
                .section-title {
                    font-size: 12pt;
                    font-weight: bold;
                    margin-top: 15px;
                    margin-bottom: 5px;
                    color: #333;
                    padding-bottom: 3px;
                }
                .data-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 15px;
                }
                .data-table th {
                    background-color: #717578;
                    color: white;
                    padding: 6px 4px;
                    text-align: center;
                    border: 1px solid #000;
                    font-size: 9pt;
                    font-weight: bold;
                }
                .data-table td {
                    padding: 5px 4px;
                    border: 1px solid #666;
                    font-size: 9pt;
                }
                .text-right {
                    text-align: right;
                }
                .text-center {
                    text-align: center;
                }
                .total-section {
                    margin-top: 10px;
                    float: right;
                    width: 300px;
                }
                .total-section table {
                    width: 100%;
                }
                .total-section td {
                    padding: 4px 8px;
                    font-size: 9pt;
                }
                .grand-total {
                    font-weight: bold;
                    font-size: 11pt;
                    border-top: 2px solid #000;
                }
                .grand-total td {
                    padding-top: 8px !important;
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
            <div class="section-title">JASA/SERVICE</div>
            <table class="data-table">
                <thead class="teble-dark">
                    <tr>
                        <th align="center" style="width: 5%;">No</th>
                        <th align="center" style="width: 45%;">Nama Jasa</th>
                        <th align="center" style="width: 25%;">Mekanik</th>
                        <th align="center" style="width: 10%;" class="text-center">QTY</th>
                        <th align="center" style="width: 15%;" class="text-right">Tarif</th>
                        <th align="center" style="width: 22%;" class="text-right">Total</th>
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
            <div class="section-title">BARANG/SPARE PART</div>
            <table class="data-table">
                <thead class="teble-dark">
                    <tr>
                        <th align="center" style="width: 5%;">No</th>
                        <th align="center" style="width: 31%;">Nama Barang</th>
                        <th align="center" style="width: 15%;">Merek</th>
                        <th align="center" style="width: 8%;" class="text-center">Satuan</th>
                        <th align="center" style="width: 10%;" class="text-center">QTY</th>
                        <th align="center" style="width: 15%;" class="text-right">Harga</th>
                        <th align="center" style="width: 18%;" class="text-right">Total</th>
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
                        <td style="width: 10px;">:</td>
                        <td class="text-right"><strong>Rp <?php echo number_format($header['TotalJasa'] ?? 0, 0, ',', '.'); ?></strong></td>
                    </tr>
                    <tr>
                        <td>Total Barang</td>
                        <td>:</td>
                        <td class="text-right"><strong>Rp <?php echo number_format($header['TotalBarang'] ?? 0, 0, ',', '.'); ?></strong></td>
                    </tr>
                    <tr class="grand-total">
                        <td>TOTAL ORDER</td>
                        <td>:</td>
                        <td class="text-right" style="color: #c00;">Rp <?php echo number_format($header['TotalOrder'] ?? 0, 0, ',', '.'); ?></td>
                    </tr>
                </table>
            </div>
            
            <div style="clear: both;"></div>
        </body>
        </html>
        <?php
        return ob_get_clean();
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
