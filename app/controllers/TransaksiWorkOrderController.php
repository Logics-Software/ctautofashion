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
     * Helper: Redirect
     */
    private function redirect($path) {
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        $fullPath = $basePath . $path;
        header('Location: ' . $fullPath);
    }
}
?>

