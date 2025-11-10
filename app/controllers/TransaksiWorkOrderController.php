<?php
require_once 'app/models/TransaksiWorkOrderModel.php';

class TransaksiWorkOrderController {
    private $model;
    
    public function __construct() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            exit;
        }
        
        $this->model = new TransaksiWorkOrderModel();
    }
    
    /**
     * Main index page - show form and list
     */
    public function index() {
        try {
            // Pagination
            $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
            $limit = 20;
            $offset = ($page - 1) * $limit;
            
            // Filters
            $dateInput = $_GET['date'] ?? '';
            
            // Convert YYYY-MM-DD to DD/MM/YYYY for display
            $displayDate = '';
            if (!empty($dateInput) && preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $dateInput, $matches)) {
                $displayDate = $matches[3] . '/' . $matches[2] . '/' . $matches[1]; // DD/MM/YYYY
            }
            
            $filters = [
                'search' => $_GET['search'] ?? '',
                'date' => $dateInput,        // YYYY-MM-DD untuk query database
                'display_date' => $displayDate  // DD/MM/YYYY untuk display
            ];
            
            // Get user info for filtering
            $userID = $_SESSION['user_id'] ?? null;
            $tipeUser = isset($_SESSION['tipe_user']) ? (int)$_SESSION['tipe_user'] : null;
            
            // Get work orders
            $workOrders = $this->model->getWorkOrders($filters, $limit, $offset, $userID, $tipeUser);
            $totalWorkOrders = $this->model->getTotalWorkOrders($filters, $userID, $tipeUser);
            $totalPages = ceil($totalWorkOrders / $limit);
            
            // Get default picker if TipeUser = 1
            $defaultPicker = null;
            if ($tipeUser === 1 && $userID) {
                $defaultPicker = $this->model->getDefaultPickerByUser($userID);
            }
            
            // Prepare data for view
            $data = [
                'workOrders' => $workOrders,
                'filters' => $filters,
                'page' => $page,
                'totalPages' => $totalPages,
                'totalWorkOrders' => $totalWorkOrders,
                'defaultPicker' => $defaultPicker  // Pass default picker to view
            ];
            
            $this->renderView('transaksiworkorder/index', $data);
            
        } catch (Exception $e) {
            error_log("Error in TransaksiWorkOrderController::index: " . $e->getMessage());
            $_SESSION['error'] = 'Terjadi kesalahan saat memuat halaman';
            $this->redirect('/dashboard');
            exit();
        }
    }
    
    /**
     * AJAX: Search customers
     */
    public function searchCustomers() {
        header('Content-Type: application/json');
        
        // Check if user is logged in (for AJAX, return JSON error)
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['results' => [], 'error' => 'Not authenticated']);
            exit;
        }
        
        try {
            $searchTerm = $_GET['term'] ?? '';
            $customers = $this->model->searchCustomers($searchTerm);
            
            // Format for Select2
            $results = array_map(function($customer) {
                return [
                    'id' => $customer['KodeCustomer'],
                    'text' => $customer['NamaCustomer'] . ' - ' . $customer['NoTelepon'],
                    'data' => $customer
                ];
            }, $customers);
            
            echo json_encode(['results' => $results]);
            
        } catch (Exception $e) {
            error_log("Error searching customers: " . $e->getMessage());
            echo json_encode(['results' => []]);
        }
        exit();
    }
    
    /**
     * AJAX: Get customer by code
     */
    public function getCustomer() {
        header('Content-Type: application/json');
        
        try {
            $kodeCustomer = $_GET['code'] ?? '';
            $customer = $this->model->getCustomerByCode($kodeCustomer);
            
            echo json_encode($customer ?: []);
            
        } catch (Exception $e) {
            error_log("Error getting customer: " . $e->getMessage());
            echo json_encode([]);
        }
        exit();
    }
    
    /**
     * AJAX: Search vehicles
     */
    public function searchVehicles() {
        header('Content-Type: application/json');
        
        // Check if user is logged in (for AJAX, return JSON error)
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['results' => [], 'error' => 'Not authenticated']);
            exit;
        }
        
        try {
            $searchTerm = $_GET['term'] ?? '';
            $vehicles = $this->model->searchVehicles($searchTerm);
            
            // Format for Select2
            $results = array_map(function($vehicle) {
                return [
                    'id' => $vehicle['KodeKendaraan'],
                    'text' => $vehicle['NamaKendaraan'] . ' - ' . $vehicle['NoPolisi'],
                    'data' => $vehicle
                ];
            }, $vehicles);
            
            echo json_encode(['results' => $results]);
            
        } catch (Exception $e) {
            error_log("Error searching vehicles: " . $e->getMessage());
            echo json_encode(['results' => []]);
        }
        exit();
    }
    
    /**
     * AJAX: Get vehicle by code
     */
    public function getVehicle() {
        header('Content-Type: application/json');
        
        try {
            $kodeKendaraan = $_GET['code'] ?? '';
            $vehicle = $this->model->getVehicleByCode($kodeKendaraan);
            
            echo json_encode($vehicle ?: []);
            
        } catch (Exception $e) {
            error_log("Error getting vehicle: " . $e->getMessage());
            echo json_encode([]);
        }
        exit();
    }
    
    /**
     * AJAX: Get vehicle by customer code
     */
    public function getVehicleByCustomer() {
        header('Content-Type: application/json');
        
        try {
            $kodeCustomer = $_GET['customer_code'] ?? '';
            $vehicle = $this->model->getVehicleByCustomerCode($kodeCustomer);
            
            echo json_encode($vehicle ?: []);
            
        } catch (Exception $e) {
            error_log("Error getting vehicle by customer: " . $e->getMessage());
            echo json_encode([]);
        }
        exit();
    }
    
    /**
     * AJAX: Search jasa
     */
    public function searchJasa() {
        header('Content-Type: application/json');
        
        try {
            $searchTerm = $_GET['term'] ?? '';
            $jasaList = $this->model->searchJasa($searchTerm);
            
            // Format for Select2
            $results = array_map(function($jasa) {
                return [
                    'id' => $jasa['KodeJasa'],
                    'text' => $jasa['NamaJasa'],
                    'data' => $jasa
                ];
            }, $jasaList);
            
            echo json_encode(['results' => $results]);
            
        } catch (Exception $e) {
            error_log("Error searching jasa: " . $e->getMessage());
            echo json_encode(['results' => []]);
        }
        exit();
    }
    
    /**
     * AJAX: Get jasa by code
     */
    public function getJasa() {
        header('Content-Type: application/json');
        
        try {
            $kodeJasa = $_GET['code'] ?? '';
            $jasa = $this->model->getJasaByCode($kodeJasa);
            
            echo json_encode($jasa ?: []);
            
        } catch (Exception $e) {
            error_log("Error getting jasa: " . $e->getMessage());
            echo json_encode([]);
        }
        exit();
    }
    
    /**
     * AJAX: Search barang
     */
    public function searchBarang() {
        header('Content-Type: application/json');
        
        try {
            $searchTerm = $_GET['term'] ?? '';
            $barangList = $this->model->searchBarang($searchTerm);
            
            // Format for Select2
            $results = array_map(function($barang) {
                return [
                    'id' => $barang['KodeBarang'],
                    'text' => $barang['NamaBarang'],
                    'data' => $barang
                ];
            }, $barangList);
            
            echo json_encode(['results' => $results]);
            
        } catch (Exception $e) {
            error_log("Error searching barang: " . $e->getMessage());
            echo json_encode(['results' => []]);
        }
        exit();
    }
    
    /**
     * AJAX: Get barang by code
     */
    public function getBarang() {
        header('Content-Type: application/json');
        
        try {
            $kodeBarang = $_GET['code'] ?? '';
            $barang = $this->model->getBarangByCode($kodeBarang);
            
            echo json_encode($barang ?: []);
            
        } catch (Exception $e) {
            error_log("Error getting barang: " . $e->getMessage());
            echo json_encode([]);
        }
        exit();
    }
    
    /**
     * AJAX: Get stock available for barang
     */
    public function getStokBarang() {
        header('Content-Type: application/json');
        
        try {
            $kodeBarang = $_GET['code'] ?? '';
            
            if (empty($kodeBarang)) {
                echo json_encode([
                    'success' => false,
                    'stok' => 0,
                    'message' => 'Kode barang tidak boleh kosong'
                ]);
                exit();
            }
            
            $stok = $this->model->getStokBarang($kodeBarang);
            
            echo json_encode([
                'success' => true,
                'stok' => $stok
            ]);
            
        } catch (Exception $e) {
            error_log("Error getting stok barang: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'stok' => 0,
                'message' => 'Terjadi kesalahan saat mengambil data stok'
            ]);
        }
        exit();
    }
    
    /**
     * AJAX: Search montir
     */
    public function searchMontir() {
        header('Content-Type: application/json');
        
        // Check if user is logged in (for AJAX, return JSON error)
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['results' => [], 'error' => 'Not authenticated']);
            exit;
        }
        
        try {
            $searchTerm = $_GET['term'] ?? '';
            $montirList = $this->model->searchMontir($searchTerm);
            
            // Format for Choices.js
            $results = array_map(function($montir) {
                return [
                    'id' => $montir['KodeMontir'],
                    'text' => $montir['NamaMontir'],
                    'data' => $montir
                ];
            }, $montirList);
            
            echo json_encode(['results' => $results]);
            
        } catch (Exception $e) {
            error_log("Error searching montir: " . $e->getMessage());
            echo json_encode(['results' => []]);
        }
        exit();
    }
    
    /**
     * AJAX: Get montir by code
     */
    public function getMontir() {
        header('Content-Type: application/json');
        
        try {
            $kodeMontir = $_GET['code'] ?? '';
            $montir = $this->model->getMontirByCode($kodeMontir);
            
            echo json_encode($montir ?: []);
            
        } catch (Exception $e) {
            error_log("Error getting montir: " . $e->getMessage());
            echo json_encode([]);
        }
        exit();
    }
    
    /**
     * AJAX: Search picker
     */
    public function searchPicker() {
        header('Content-Type: application/json');
        
        // Check if user is logged in (for AJAX, return JSON error)
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['results' => [], 'error' => 'Not authenticated']);
            exit;
        }
        
        try {
            $searchTerm = $_GET['term'] ?? '';
            $pickerList = $this->model->searchPicker($searchTerm);
            
            // Format for Choices.js
            $results = array_map(function($picker) {
                return [
                    'id' => $picker['KodePicker'],
                    'text' => $picker['NamaPicker'],
                    'data' => $picker
                ];
            }, $pickerList);
            
            echo json_encode(['results' => $results]);
            
        } catch (Exception $e) {
            error_log("Error searching picker: " . $e->getMessage());
            echo json_encode(['results' => []]);
        }
        exit();
    }
    
    /**
     * AJAX: Get picker by code
     */
    public function getPicker() {
        header('Content-Type: application/json');
        
        try {
            $kodePicker = $_GET['code'] ?? '';
            $picker = $this->model->getPickerByCode($kodePicker);
            
            echo json_encode($picker ?: []);
            
        } catch (Exception $e) {
            error_log("Error getting picker: " . $e->getMessage());
            echo json_encode([]);
        }
        exit();
    }
    
    /**
     * Save work order
     */
    public function save() {
        header('Content-Type: application/json');
        
        try {
            // Validate request method
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }
            
            // Get JSON data
            $jsonData = file_get_contents('php://input');
            $data = json_decode($jsonData, true);
            
            if (!$data) {
                throw new Exception('Invalid JSON data');
            }
            
            // Validate required fields
            $requiredFields = ['KodeCustomer', 'KodeKendaraan', 'KodeMontir', 'KodePicker'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    throw new Exception("Field $field is required");
                }
            }
            
            // Validate at least one detail (jasa or barang)
            if (empty($data['DetailJasa']) && empty($data['DetailBarang'])) {
                throw new Exception('Minimal harus ada 1 detail jasa atau barang');
            }
            
            // Save to database
            $result = $this->model->saveWorkOrder($data);
            
            echo json_encode($result);
            
        } catch (Exception $e) {
            error_log("Error saving work order: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        exit();
    }
    
    /**
     * Get work order detail for modal
     */
    public function getDetail() {
        header('Content-Type: application/json');
        
        try {
            $noOrder = $_GET['noorder'] ?? '';
            
            if (empty($noOrder)) {
                echo json_encode(['error' => 'NoOrder tidak boleh kosong']);
                exit();
            }
            
            $detail = $this->model->getWorkOrderDetail($noOrder);
            echo json_encode($detail);
            
        } catch (Exception $e) {
            error_log("Error getting work order detail: " . $e->getMessage());
            echo json_encode(['error' => 'Terjadi kesalahan saat mengambil data']);
        }
        exit();
    }
    
    /**
     * Helper: Render view
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
            
            // Include layout
            $title = 'Transaksi Work Order';
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
     * Get work order data for edit
     */
    public function getDataForEdit() {
        header('Content-Type: application/json');
        
        try {
            $noOrder = $_GET['noorder'] ?? '';
            
            if (empty($noOrder)) {
                echo json_encode(['error' => 'NoOrder tidak boleh kosong']);
                exit();
            }
            
            // Get work order data
            $data = $this->model->getWorkOrderForEdit($noOrder);
            
            if (isset($data['error'])) {
                echo json_encode($data);
                exit();
            }
            
            // Check permission
            $userID = $_SESSION['user_id'] ?? null;
            $tipeUser = isset($_SESSION['tipe_user']) ? (int)$_SESSION['tipe_user'] : null;
            $statusOrder = (int)$data['header']['StatusOrder'];
            $orderUserID = $data['header']['UserID'];
            
            // Check if status < 2
            if ($statusOrder >= 2) {
                echo json_encode(['error' => 'Work Order dengan status Selesai atau Dibatalkan tidak dapat diedit']);
                exit();
            }
            
            // Check user permission
            if ($tipeUser < 2 && $orderUserID != $userID) {
                echo json_encode(['error' => 'Anda tidak memiliki akses untuk mengedit Work Order ini']);
                exit();
            }
            
            echo json_encode($data);
            
        } catch (Exception $e) {
            error_log("Error getting work order for edit: " . $e->getMessage());
            echo json_encode(['error' => 'Terjadi kesalahan saat mengambil data']);
        }
        exit();
    }
    
    /**
     * Update work order
     */
    public function update() {
        header('Content-Type: application/json');
        
        try {
            // Get JSON input
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Invalid JSON data'
                ]);
                exit();
            }
            
            $noOrder = $data['NoOrder'] ?? '';
            
            // Validate NoOrder
            if (empty($noOrder)) {
                echo json_encode([
                    'success' => false,
                    'error' => 'NoOrder tidak boleh kosong'
                ]);
                exit();
            }
            
            // Validate required fields
            $requiredFields = ['KodeCustomer', 'KodeKendaraan', 'KodeMontir', 'KodePicker'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    echo json_encode([
                        'success' => false,
                        'error' => 'Field ' . $field . ' harus diisi'
                    ]);
                    exit();
                }
            }
            
            // Validate details
            if (empty($data['DetailJasa']) && empty($data['DetailBarang'])) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Minimal harus ada 1 detail jasa atau barang'
                ]);
                exit();
            }
            
            // Check permission
            $userID = $_SESSION['user_id'] ?? null;
            $tipeUser = isset($_SESSION['tipe_user']) ? (int)$_SESSION['tipe_user'] : null;
            
            // Get work order to check permission
            $existingData = $this->model->getWorkOrderForEdit($noOrder);
            if (isset($existingData['error'])) {
                echo json_encode([
                    'success' => false,
                    'error' => $existingData['error']
                ]);
                exit();
            }
            
            $statusOrder = (int)$existingData['header']['StatusOrder'];
            $orderUserID = $existingData['header']['UserID'];
            
            // Check if status < 2
            if ($statusOrder >= 2) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Work Order dengan status Selesai atau Dibatalkan tidak dapat diedit'
                ]);
                exit();
            }
            
            // Check user permission
            if ($tipeUser < 2 && $orderUserID != $userID) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Anda tidak memiliki akses untuk mengedit Work Order ini'
                ]);
                exit();
            }
            
            // Update work order
            $result = $this->model->updateWorkOrder($noOrder, $data, $userID);
            
            echo json_encode($result);
            
        } catch (Exception $e) {
            error_log("Error updating work order: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Terjadi kesalahan saat mengupdate work order'
            ]);
        }
        exit();
    }
    
    /**
     * AJAX: Get Kota List
     */
    public function getKotaList() {
        header('Content-Type: application/json');
        
        try {
            $kotaList = $this->model->getKotaList();
            
            echo json_encode([
                'success' => true,
                'kota' => $kotaList
            ]);
        } catch (Exception $e) {
            error_log("Error getting kota list: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Gagal mengambil data kota'
            ]);
        }
        exit();
    }
    
    /**
     * AJAX: Save New Customer
     */
    public function saveCustomer() {
        header('Content-Type: application/json');
        
        try {
            // Get JSON input
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            
            // Validate required fields
            if (empty($data['NamaCustomer']) || empty($data['Kota']) || 
                empty($data['NoTelepon']) || !isset($data['JenisCustomer'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Data tidak lengkap'
                ]);
                exit();
            }
            
            // Save customer
            $result = $this->model->saveNewCustomer($data);
            
            echo json_encode($result);
            
        } catch (Exception $e) {
            error_log("Error saving customer: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan customer'
            ]);
        }
        exit();
    }
    
    /**
     * AJAX: Get Merek List
     */
    public function getMerekList() {
        header('Content-Type: application/json');
        
        try {
            $merekList = $this->model->getMerekList();
            
            echo json_encode([
                'success' => true,
                'merek' => $merekList
            ]);
        } catch (Exception $e) {
            error_log("Error getting merek list: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Gagal mengambil data merek'
            ]);
        }
        exit();
    }
    
    /**
     * AJAX: Get Model (Jenis) List
     */
    public function getModelList() {
        header('Content-Type: application/json');
        
        try {
            $modelList = $this->model->getModelList();
            
            echo json_encode([
                'success' => true,
                'model' => $modelList
            ]);
        } catch (Exception $e) {
            error_log("Error getting model list: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Gagal mengambil data model'
            ]);
        }
        exit();
    }
    
    /**
     * AJAX: Save New Vehicle
     */
    public function saveVehicle() {
        header('Content-Type: application/json');
        
        try {
            // Get JSON input
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            
            // Validate required fields
            if (empty($data['KodeMerek']) || empty($data['KodeJenis']) || 
                empty($data['Tipe']) || empty($data['Warna']) || 
                empty($data['Tahun']) || empty($data['Silinder']) ||
                empty($data['BahanBakar']) || empty($data['NamaKendaraan']) ||
                empty($data['NoPolisi'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Data tidak lengkap'
                ]);
                exit();
            }
            
            // Save vehicle
            $result = $this->model->saveNewVehicle($data);
            
            echo json_encode($result);
            
        } catch (Exception $e) {
            error_log("Error saving vehicle: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan kendaraan'
            ]);
        }
        exit();
    }
    
    /**
     * Download PDF Nota Transaksi Work Order
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
            $detail = $this->model->getWorkOrderDetail($noOrder);
            
            if (!$detail || isset($detail['error'])) {
                echo "Data Work Order tidak ditemukan";
                exit;
            }
            
            // Generate PDF content
            $this->generatePDF($detail);
            
        } catch (Exception $e) {
            error_log("[downloadPDF] Exception: " . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            echo "Terjadi kesalahan: " . $e->getMessage();
            exit;
        } catch (\Throwable $t) {
            error_log("[downloadPDF] Error: " . $t->getMessage() . ' in ' . $t->getFile() . ':' . $t->getLine());
            echo "Terjadi kesalahan: " . $t->getMessage();
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
            die("Library FPDF tidak ditemukan. Install dengan `composer require setasign/fpdf` atau letakkan library pada `libs/fpdf`.");
        }

        if (!class_exists('\FPDF') && !class_exists('FPDF')) {
            die("Kelas FPDF tidak tersedia. Pastikan library sudah ter-load.");
        }
        
        $header = $detail['header'];
        $jasa = $detail['jasa'] ?? [];
        $barang = $detail['barang'] ?? [];

        $this->generatePDFUsingFPDF($header, $jasa, $barang);
    }

    /**
     * Convert UTF-8 text to Latin compatible encoding for FPDF
     */
    private function convertToLatin($text) {
        $text = $text ?? '';
        $converted = @iconv('UTF-8', 'windows-1252//TRANSLIT', $text);
        if ($converted === false) {
            return $text;
        }
        return $converted;
    }

    /**
     * Resolve signature path for embedding into PDF
     */
    private function resolveSignaturePath($path) {
        if (empty($path)) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        $normalized = ltrim($path, '/');
        $fullPath = BASE_PATH . '/' . $normalized;
        if (file_exists($fullPath)) {
            return $fullPath;
        }

        return null;
    }

    /**
     * Generate PDF using FPDF library
     */
    private function generatePDFUsingFPDF($header, $jasa, $barang) {
        $fpdfClass = class_exists('\FPDF') ? '\FPDF' : 'FPDF';

        try {
            $pdf = new $fpdfClass();
        } catch (\Throwable $e) {
            die("Gagal menginisialisasi FPDF: " . $e->getMessage());
        }

        $pdf->SetTitle($this->convertToLatin('Nota Work Order ' . ($header['NoOrder'] ?? '')));
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
        $pdf->Ln(3);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 7, $this->convertToLatin('Informasi Customer & Kendaraan'), 0, 1);
        $pdf->Ln(1);

        $pdf->SetFont('Arial', '', 9);
        $infoPairs = [
            'Customer' => $header['NamaCustomer'] ?? '-',
            'Alamat' => $header['AlamatCustomer'] ?? '-',
            'No. Telepon' => $header['NoTelepon'] ?? '-',
            'Kendaraan' => $header['NamaKendaraan'] . ' - ' . $header['NoPolisi'] ?? '-',
            'Marketing' => $header['NamaPicker'] . '       Mekanik: ' . $header['NamaMontir'] ?? '-',
        ];

        foreach ($infoPairs as $label => $value) {
            $pdf->Cell(40, 6, $this->convertToLatin($label), 0, 0);
            $pdf->Cell(3, 6, ':', 0, 0);
            $pdf->MultiCell(0, 6, $this->convertToLatin($value));
        }

        if (!empty($jasa)) {
            $pdf->Ln(3);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(0, 7, $this->convertToLatin('JASA / SERVICE'), 0, 1);

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(113, 117, 120);
            $pdf->SetTextColor(255);
            $pdf->Cell(10, 7, 'No', 1, 0, 'C', true);
            $pdf->Cell(65, 7, $this->convertToLatin('Nama Jasa'), 1, 0, 'L', true);
            $pdf->Cell(30, 7, $this->convertToLatin('Kategori'), 1, 0, 'L', true);
            $pdf->Cell(15, 7, $this->convertToLatin('Sat.'), 1, 0, 'C', true);
            $pdf->Cell(15, 7, $this->convertToLatin('Qty'), 1, 0, 'C', true);
            $pdf->Cell(25, 7, $this->convertToLatin('Harga'), 1, 0, 'R', true);
            $pdf->Cell(30, 7, $this->convertToLatin('Total'), 1, 1, 'R', true);

            $pdf->SetFont('Arial', '', 9);
            $pdf->SetTextColor(0);
            $no = 1;
            foreach ($jasa as $item) {
                $this->renderWrappedRow($pdf, [
                    ['width' => 10, 'text' => $no++, 'align' => 'C'],
                    ['width' => 65, 'text' => $item['NamaJasa'] ?? '-', 'align' => 'L'],
                    ['width' => 30, 'text' => $item['NamaKategori'] ?? '-', 'align' => 'L'],
                    ['width' => 15, 'text' => $item['Satuan'] ?? '-', 'align' => 'C'],
                    ['width' => 15, 'text' => (int) ($item['Jumlah'] ?? 0), 'align' => 'C'],
                    ['width' => 25, 'text' => number_format($item['HargaSatuan'] ?? 0, 0, ',', '.'), 'align' => 'R'],
                    ['width' => 30, 'text' => number_format($item['TotalHarga'] ?? 0, 0, ',', '.'), 'align' => 'R'],
                ],5);
            }
        }

        if (!empty($barang)) {
            $pdf->Ln(3);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 7, $this->convertToLatin('BARANG / SPARE PART'), 0, 1);

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(113, 117, 120);
            $pdf->SetTextColor(255);
            $pdf->Cell(10, 7, 'No', 1, 0, 'C', true);
            $pdf->Cell(70, 7, $this->convertToLatin('Nama Barang'), 1, 0, 'L', true);
            $pdf->Cell(25, 7, $this->convertToLatin('Merek'), 1, 0, 'L', true);
            $pdf->Cell(15, 7, $this->convertToLatin('Sat.'), 1, 0, 'C', true);
            $pdf->Cell(15, 7, $this->convertToLatin('Qty'), 1, 0, 'C', true);
            $pdf->Cell(25, 7, $this->convertToLatin('Harga'), 1, 0, 'R', true);
            $pdf->Cell(30, 7, $this->convertToLatin('Total'), 1, 1, 'R', true);

            $pdf->SetFont('Arial', '', 9);
            $pdf->SetTextColor(0);
            $no = 1;
            foreach ($barang as $item) {
                $this->renderWrappedRow($pdf, [
                    ['width' => 10, 'text' => $no++, 'align' => 'C'],
                    ['width' => 70, 'text' => $item['NamaBarang'] ?? '-', 'align' => 'L'],
                    ['width' => 25, 'text' => $item['NamaMerek'] ?? '-', 'align' => 'L'],
                    ['width' => 15, 'text' => $item['Satuan'] ?? '-', 'align' => 'C'],
                    ['width' => 15, 'text' => (int) ($item['Jumlah'] ?? 0), 'align' => 'C'],
                    ['width' => 25, 'text' => number_format($item['HargaSatuan'] ?? 0, 0, ',', '.'), 'align' => 'R'],
                    ['width' => 30, 'text' => number_format($item['TotalHarga'] ?? 0, 0, ',', '.'), 'align' => 'R'],
                ],5);
            }
        }

        $pdf->Ln(4);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 7, $this->convertToLatin('Ringkasan Total'), 0, 1);

        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(50, 7, $this->convertToLatin('Total Jasa'), 1, 0);
        $pdf->Cell(0, 7, $this->convertToLatin('Rp ' . number_format($header['TotalJasa'] ?? 0, 0, ',', '.')), 1, 1, 'R');
        $pdf->Cell(50, 7, $this->convertToLatin('Total Barang'), 1, 0);
        $pdf->Cell(0, 7, $this->convertToLatin('Rp ' . number_format($header['TotalBarang'] ?? 0, 0, ',', '.')), 1, 1, 'R');

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(50, 7, $this->convertToLatin('TOTAL ORDER'), 1, 0);
        $pdf->Cell(0, 7, $this->convertToLatin('Rp ' . number_format($header['TotalOrder'] ?? 0, 0, ',', '.')), 1, 1, 'R');

        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(0, 6, $this->convertToLatin('Menyetujui,'), 0, 1, 'R');
        $pdf->Ln(10);

        $sig = $header['TandaTanganCustomer'] ?? null;
        $signaturePrinted = false;
        if (!empty($sig)) {
            $signaturePath = $this->resolveSignaturePath($sig);
            if ($signaturePath) {
                try {
                    $currentX = $pdf->GetX();
                    $currentY = $pdf->GetY();
                    $pdf->Image($signaturePath, $currentX + 130, $currentY - 18, 40);
                    $signaturePrinted = true;
                } catch (\Throwable $e) {
                    // Ignore image errors and fallback to text
                }
            }
        }

        if (!$signaturePrinted) {
            $pdf->Cell(0, 6, $this->convertToLatin(''), 0, 1, 'R');
        }

        $pdf->Ln(10);
        $pdf->Cell(0, 6, $this->convertToLatin('Customer'), 0, 1, 'R');

        $filename = 'NotaWorkOrder_' . $header['NoOrder'] . '_' . date('Ymd_His') . '.pdf';
        $pdf->Output($filename, 'D');
        exit;
    }

    /**
     * Render a table row with wrapped cells and uniform height
     */
    private function renderWrappedRow($pdf, array $columns, $lineHeight = 7)
    {
        $prepared = [];
        $maxLines = 1;

        foreach ($columns as $column) {
            $width = $column['width'] ?? 0;
            $align = $column['align'] ?? 'L';
            $text = $this->convertToLatin((string) ($column['text'] ?? ''));

            $lineCount = $this->calculateLineCount($pdf, $width, $text);
            $maxLines = max($maxLines, $lineCount);

            $prepared[] = [
                'width' => $width,
                'align' => $align,
                'text' => $text,
            ];
        }

        $rowHeight = $lineHeight * $maxLines;
        $bottomMargin = $this->getPrivateValue($pdf, 'bMargin', 0);
        $pageHeight = method_exists($pdf, 'GetPageHeight') ? $pdf->GetPageHeight() : 297; // default A4 height
        $pageBreak = $this->getPrivateValue($pdf, 'PageBreakTrigger', $pageHeight - $bottomMargin);

        if ($pdf->GetY() + $rowHeight > $pageBreak) {
            $pdf->AddPage($pdf->GetPageOrientation());
        }

        foreach ($prepared as $spec) {
            $x = $pdf->GetX();
            $y = $pdf->GetY();

            $pdf->Rect($x, $y, $spec['width'], $rowHeight);
            $pdf->MultiCell($spec['width'], $lineHeight, $spec['text'], 0, $spec['align']);
            $pdf->SetXY($x + $spec['width'], $y);
        }

        $pdf->Ln($rowHeight);
    }

    /**
     * Calculate number of lines required for given text and width
     */
    private function calculateLineCount($pdf, $width, $text)
    {
        if ($width <= 0) {
            return 1;
        }

        $text = str_replace("\r", '', $text);
        $len = strlen($text);
        if ($len > 0 && $text[$len - 1] === "\n") {
            $len--;
        }

        $currentFont = $this->getPrivateValue($pdf, 'CurrentFont', []);
        $cw = is_array($currentFont) && isset($currentFont['cw']) ? $currentFont['cw'] : [];

        $cMargin = $this->getPrivateValue($pdf, 'cMargin', 0);
        $fontSize = $this->getPrivateValue($pdf, 'FontSize', 12);
        $wMax = ($width - 2 * $cMargin) * 1000 / max(1, $fontSize);

        $sep = -1;
        $i = 0;
        $j = 0;
        $lineLength = 0;
        $numLines = 1;

        while ($i < $len) {
            $char = $text[$i];
            if ($char === "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $lineLength = 0;
                $numLines++;
                continue;
            }

            if ($char === ' ') {
                $sep = $i;
            }

            $lineLength += $cw[$char] ?? 0;

            if ($lineLength > $wMax) {
                if ($sep === -1) {
                    if ($i === $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $lineLength = 0;
                $numLines++;
            } else {
                $i++;
            }
        }

        return max(1, $numLines);
    }
    
    private function getPrivateValue($object, $property, $default = null)
    {
        try {
            $reflection = new \ReflectionClass($object);
            if ($reflection->hasProperty($property)) {
                $prop = $reflection->getProperty($property);
                $prop->setAccessible(true);
                return $prop->getValue($object);
            }
        } catch (\Throwable $ignored) {
        }
        return $default;
    }

    /**
     * Helper: Redirect
     */
    private function redirect($path) {
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        $fullPath = $basePath . $path;
        header('Location: ' . $fullPath);
    }
}
