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
            // Render printable HTML view (use browser Print to create PDF)
            $viewFile = BASE_PATH . '/app/views/workorder/print.php';
            if (file_exists($viewFile)) {
                extract(['detail' => $detail]);
                include $viewFile;
                exit;
            } else {
                echo "View not found: workorder/print";
                exit;
            }
            
        } catch (Exception $e) {
            echo "Terjadi kesalahan: " . $e->getMessage();
            exit;
        }
    }
    
    /**
     * Generate PDF from work order data
     */
    private function generatePDF($detail) {
        $autoloadPath = BASE_PATH . '/vendor/autoload.php';
        $fpdfLibPath = BASE_PATH . '/libs/fpdf/fpdf.php';

        if (file_exists($autoloadPath)) {
            require_once $autoloadPath;
        } elseif (file_exists($fpdfLibPath)) {
            require_once $fpdfLibPath;
        } else {
            echo "Error: Library FPDF tidak ditemukan. Install dengan `composer require setasign/fpdf` atau letakkan library pada `libs/fpdf`.";
            exit;
        }

        if (!class_exists('\\FPDF') && !class_exists('FPDF')) {
            echo "Error: Kelas FPDF tidak tersedia. Pastikan library sudah ter-load.";
            exit;
        }

        $header = $detail['header'] ?? [];
        $services = $detail['services'] ?? [];
        $items = $detail['items'] ?? [];

        $this->generatePDFUsingFPDF($header, $services, $items);
    }

    private function generatePDFUsingFPDF($header, $services, $items) {
        $fpdfClass = class_exists('\\FPDF') ? '\\FPDF' : 'FPDF';

        try {
            $pdf = new $fpdfClass();
        } catch (\Throwable $e) {
            echo "Error: Gagal menginisialisasi FPDF: " . $e->getMessage();
            exit;
        }

        $pdf->SetTitle($this->convertToLatin('Work Order ' . ($header['NoOrder'] ?? '-')));
        $pdf->SetAuthor('ctautofashion');
        $pdf->SetCreator('ctautofashion');
        $pdf->SetMargins(10, 10, 10);
        $pdf->AddPage();

        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, $this->convertToLatin('WORK ORDER'), 0, 1, 'C');

        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 6, $this->convertToLatin('No. Order: ' . ($header['NoOrder'] ?? '-')), 0, 1, 'C');
        $tanggalOrder = !empty($header['TanggalOrder']) ? date('d/m/Y', strtotime($header['TanggalOrder'])) : '-';
        $pdf->Cell(0, 6, $this->convertToLatin('Tanggal: ' . $tanggalOrder), 0, 1, 'C');
        $pdf->Ln(4);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 7, $this->convertToLatin('Informasi Customer & Kendaraan'), 0, 1);
        $pdf->Ln(1);

        $pdf->SetFont('Arial', '', 10);
        $infoPairs = [
            'Customer' => $header['NamaCustomer'] ?? '-',
            'Alamat' => $header['AlamatCustomer'] ?? '-',
            'No. Telepon' => $header['NoTelepon'] ?? '-',
            'Kendaraan' => $header['NamaKendaraan'] ?? '-',
            'No. Polisi' => $header['NoPolisi'] ?? '-',
            'Warna' => $header['Warna'] ?? '-',
            'Marketing' => $header['Marketing'] ?? '-',
        ];

        foreach ($infoPairs as $label => $value) {
            $pdf->Cell(35, 6, $this->convertToLatin($label), 0, 0);
            $pdf->Cell(4, 6, ':', 0, 0);
            $pdf->MultiCell(0, 6, $this->convertToLatin($value));
        }

        if (!empty($services)) {
            $pdf->Ln(4);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 7, $this->convertToLatin('JASA / SERVICE'), 0, 1);

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetFillColor(113, 117, 120);
            $pdf->SetTextColor(255);
            $pdf->Cell(10, 7, 'No', 1, 0, 'C', true);
            $pdf->Cell(70, 7, $this->convertToLatin('Nama Jasa'), 1, 0, 'L', true);
            $pdf->Cell(40, 7, $this->convertToLatin('Mekanik'), 1, 0, 'L', true);
            $pdf->Cell(15, 7, $this->convertToLatin('Qty'), 1, 0, 'C', true);
            $pdf->Cell(27, 7, $this->convertToLatin('Tarif'), 1, 0, 'R', true);
            $pdf->Cell(28, 7, $this->convertToLatin('Total'), 1, 1, 'R', true);

            $pdf->SetFont('Arial', '', 10);
            $pdf->SetTextColor(0);
            $no = 1;
            foreach ($services as $service) {
                $pdf->Cell(10, 7, $no++, 1, 0, 'C');
                $pdf->Cell(70, 7, $this->convertToLatin($service['NamaJasa'] ?? '-'), 1, 0);
                $pdf->Cell(40, 7, $this->convertToLatin($service['Mekanik'] ?? '-'), 1, 0);
                $pdf->Cell(15, 7, (string) ($service['Qty'] ?? 0), 1, 0, 'C');
                $pdf->Cell(27, 7, $this->convertToLatin($this->formatCurrency($service['Tarif'] ?? 0)), 1, 0, 'R');
                $pdf->Cell(28, 7, $this->convertToLatin($this->formatCurrency($service['Total'] ?? 0)), 1, 1, 'R');
            }
        }

        if (!empty($items)) {
            $pdf->Ln(4);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 7, $this->convertToLatin('BARANG / SPARE PART'), 0, 1);

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetFillColor(113, 117, 120);
            $pdf->SetTextColor(255);
            $pdf->Cell(10, 7, 'No', 1, 0, 'C', true);
            $pdf->Cell(60, 7, $this->convertToLatin('Nama Barang'), 1, 0, 'L', true);
            $pdf->Cell(30, 7, $this->convertToLatin('Merek'), 1, 0, 'L', true);
            $pdf->Cell(15, 7, $this->convertToLatin('Sat.'), 1, 0, 'C', true);
            $pdf->Cell(15, 7, $this->convertToLatin('Qty'), 1, 0, 'C', true);
            $pdf->Cell(30, 7, $this->convertToLatin('Harga'), 1, 0, 'R', true);
            $pdf->Cell(30, 7, $this->convertToLatin('Total'), 1, 1, 'R', true);

            $pdf->SetFont('Arial', '', 10);
            $pdf->SetTextColor(0);
            $no = 1;
            foreach ($items as $item) {
                $pdf->Cell(10, 7, $no++, 1, 0, 'C');
                $pdf->Cell(60, 7, $this->convertToLatin($item['NamaBarang'] ?? '-'), 1, 0);
                $pdf->Cell(30, 7, $this->convertToLatin($item['MerekBarang'] ?? '-'), 1, 0);
                $pdf->Cell(15, 7, $this->convertToLatin($item['Satuan'] ?? '-'), 1, 0, 'C');
                $pdf->Cell(15, 7, (string) ($item['Qty'] ?? 0), 1, 0, 'C');
                $pdf->Cell(30, 7, $this->convertToLatin($this->formatCurrency($item['Harga'] ?? 0)), 1, 0, 'R');
                $pdf->Cell(30, 7, $this->convertToLatin($this->formatCurrency($item['Total'] ?? 0)), 1, 1, 'R');
            }
        }

        $pdf->Ln(6);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 7, $this->convertToLatin('Ringkasan Total'), 0, 1);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(50, 7, $this->convertToLatin('Total Jasa'), 1, 0);
        $pdf->Cell(0, 7, $this->convertToLatin('Rp ' . $this->formatCurrency($header['TotalJasa'] ?? 0)), 1, 1, 'R');

        $pdf->Cell(50, 7, $this->convertToLatin('Total Barang'), 1, 0);
        $pdf->Cell(0, 7, $this->convertToLatin('Rp ' . $this->formatCurrency($header['TotalBarang'] ?? 0)), 1, 1, 'R');

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(50, 7, $this->convertToLatin('TOTAL ORDER'), 1, 0);
        $pdf->Cell(0, 7, $this->convertToLatin('Rp ' . $this->formatCurrency($header['TotalOrder'] ?? 0)), 1, 1, 'R');

        $filename = 'WorkOrder_' . ($header['NoOrder'] ?? 'UNKNOWN') . '_' . date('Ymd_His') . '.pdf';
        $pdf->Output($filename, 'D');
        exit;
    }

    private function convertToLatin($text) {
        $text = $text ?? '';
        $converted = @iconv('UTF-8', 'windows-1252//TRANSLIT', $text);
        if ($converted === false) {
            return $text;
        }
        return $converted;
    }

    private function formatCurrency($value) {
        return number_format((float) $value, 0, ',', '.');
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
