<?php
class VehicleModel {
    private $pdo;
    
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }
    
    /**
     * Search vehicles by license plate, vehicle name, brand, or customer name
     */
    public function searchVehicles($searchTerm = '') {
        try {
            $searchCondition = '';
            $params = [];
            
            if (!empty($searchTerm)) {
                $searchCondition = 'AND (REPLACE(K.NoPolisi, \' \', \'\') LIKE ? OR K.NamaKendaraan LIKE ? OR M.NamaMerek LIKE ? OR C.NamaCustomer LIKE ?)';
                $searchParam = '%' . $searchTerm . '%';
                // Remove spaces from search term for NoPolisi matching
                $searchParamNoSpace = '%' . str_replace(' ', '', $searchTerm) . '%';
                $params = [$searchParamNoSpace, $searchParam, $searchParam, $searchParam];
            }
            
            $sql = "SELECT K.*, M.NamaMerek, C.NamaCustomer, C.KodeCustomer
                    FROM FileKendaraan K
                    LEFT JOIN TabelMerekKendaraan M ON K.KodeMerek = M.KodeMerek
                    LEFT JOIN FileCustomer C ON K.KodeCustomer = C.KodeCustomer
                    WHERE K.Status = 1 $searchCondition
                    ORDER BY K.NamaKendaraan";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("VehicleModel::searchVehicles error: " . $e->getMessage());
            error_log("VehicleModel::searchVehicles error trace: " . $e->getTraceAsString());
            return [];
        }
    }
    
    /**
     * Get vehicle transactions (service orders)
     */
    public function getVehicleTransactions($kodeKendaraan) {
        try {
            $sql = "SELECT H.*, C.*, ISNULL(P.NamaPicker,'') AS NamaMarketing, ISNULL(M.NamaMontir,'') AS NamaMontir, 
                           B.NamaKendaraan, B.Tipe, B.Tahun, B.Warna, B.NoPolisi, B.KodeMerek, TM.NamaMerek, 
                           B.KodeJenis, TJ.NamaJenis, B.*, J.NoPenjualan
                    FROM HeaderOrder H 
                    INNER JOIN FileCustomer C ON H.KodeCustomer = C.KodeCustomer 
                    INNER JOIN FilePicker P ON H.KodePicker = P.KodePicker
                    INNER JOIN FileKendaraan B ON H.KodeKendaraan = B.KodeKendaraan
                    INNER JOIN HeaderPenjualan J ON H.NoOrder = J.NoOrder
                    LEFT JOIN FileMontir M ON H.KodeMontir = M.KodeMontir
                    LEFT JOIN TabelJenisKendaraan TJ ON B.KodeJenis = TJ.KodeJenis 
                    LEFT JOIN TabelMerekKendaraan TM ON B.KodeMerek = TM.NamaMerek
                    WHERE H.KodeKendaraan = ?
                    ORDER BY H.TanggalOrder DESC, H.NoOrder DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$kodeKendaraan]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("VehicleModel::getVehicleTransactions error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get service transactions for specific work order
     */
    public function getServiceTransactions($noOrder, $kodeKendaraan) {
        try {
            $sql = "SELECT H.*, C.KodeCustomer, C.NamaCustomer, C.Kota, C.NoTelepon, C.KontakPerson, 
                           P.NamaPicker AS NamaMarketing, M.NamaMontir, B.NamaKendaraan, B.Tipe, 
                           B.Tahun, B.Warna, B.NoPolisi, B.KodeMerek, TM.NamaMerek, B.KodeJenis, 
                           TJ.NamaJenis, D.*, J.NamaJasa, J.Satuan 
                    FROM HeaderOrder HO
                    INNER JOIN HeaderPenjualan H ON HO.NoOrder = H.NoOrder
                    INNER JOIN DetailPenjualanJasa D ON H.NoPenjualan = D.NoPenjualan
                    INNER JOIN FileJasa J ON D.KodeJasa = J.KodeJasa
                    INNER JOIN FileCustomer C ON H.KodeCustomer = C.KodeCustomer 
                    INNER JOIN FilePicker P ON H.KodePicker = P.KodePicker
                    LEFT JOIN FileMontir M ON H.KodeMontir = M.KodeMontir
                    LEFT JOIN FileKendaraan B ON H.KodeKendaraan = B.KodeKendaraan
                    LEFT JOIN TabelJenisKendaraan TJ ON B.KodeJenis = TJ.KodeJenis 
                    LEFT JOIN TabelMerekKendaraan TM ON B.KodeMerek = TM.KodeMerek
                    WHERE HO.NoOrder = ? AND HO.KodeKendaraan = ?
                    ORDER BY H.TanggalPenjualan DESC, H.NoPenjualan DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$noOrder, $kodeKendaraan]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("VehicleModel::getServiceTransactions error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get parts transactions for specific work order
     */
    public function getPartsTransactions($noOrder, $kodeKendaraan) {
        try {
            $sql = "SELECT H.*, C.KodeCustomer, C.NamaCustomer, C.Kota, C.NoTelepon, C.KontakPerson, 
                           P.NamaPicker AS NamaMarketing, M.NamaMontir, B.NamaKendaraan, B.Tipe, 
                           B.Tahun, B.Warna, B.NoPolisi, B.KodeMerek, TM.NamaMerek, B.KodeJenis, 
                           TJ.NamaJenis, D.*, J.NamaBarang, J.Satuan, MEREK.NamaMerek AS MerekBarang,
                           JENIS.NamaJenis AS JenisBarang
                    FROM HeaderOrder HO
                    INNER JOIN HeaderPenjualan H ON HO.NoOrder = H.NoOrder
                    INNER JOIN DetailPenjualanBarang D ON H.NoPenjualan = D.NoPenjualan
                    INNER JOIN FILEBARANG J ON D.KodeBarang = J.KodeBarang
                    INNER JOIN FileCustomer C ON H.KodeCustomer = C.KodeCustomer 
                    INNER JOIN FilePicker P ON H.KodePicker = P.KodePicker
                    LEFT JOIN FileMontir M ON H.KodeMontir = M.KodeMontir
                    LEFT JOIN FileKendaraan B ON H.KodeKendaraan = B.KodeKendaraan
                    LEFT JOIN TabelJenisKendaraan TJ ON B.KodeJenis = TJ.KodeJenis 
                    LEFT JOIN TabelMerekKendaraan TM ON B.KodeMerek = TM.KodeMerek
                    LEFT JOIN TABELMEREK MEREK ON J.KodeMerek = MEREK.KodeMerek
                    LEFT JOIN TABELJENIS JENIS ON J.KodeJenis = JENIS.KodeJenis
                    WHERE HO.NoOrder = ? AND HO.KodeKendaraan = ?
                    ORDER BY H.TanggalPenjualan DESC, H.NoPenjualan DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$noOrder, $kodeKendaraan]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("VehicleModel::getPartsTransactions error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get work order information
     */
    public function getWorkOrderInfo($noOrder, $kodeKendaraan) {
        try {
            $sql = "SELECT H.NoOrder, H.TanggalOrder, B.NamaKendaraan, B.NoPolisi, B.Warna, P.NamaPicker AS NamaMarketing,
                           J.NoPenjualan, J.TanggalPenjualan
                    FROM HeaderOrder H 
                    INNER JOIN FileKendaraan B ON H.KodeKendaraan = B.KodeKendaraan
                    INNER JOIN FilePicker P ON H.KodePicker = P.KodePicker
                    LEFT JOIN HeaderPenjualan J ON H.NoOrder = J.NoOrder
                    WHERE H.NoOrder = ? AND H.KodeKendaraan = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$noOrder, $kodeKendaraan]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("VehicleModel::getWorkOrderInfo error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get vehicle by code
     */
    public function getVehicleByCode($kodeKendaraan) {
        try {
            $sql = "SELECT K.*, M.NamaMerek, C.NamaCustomer, C.KodeCustomer
                    FROM FileKendaraan K
                    LEFT JOIN TabelMerekKendaraan M ON K.KodeMerek = M.KodeMerek
                    LEFT JOIN FileCustomer C ON K.KodeCustomer = C.KodeCustomer
                    WHERE K.KodeKendaraan = ? AND K.Status = 1";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$kodeKendaraan]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("VehicleModel::getVehicleByCode error: " . $e->getMessage());
            return false;
        }
    }
}
?>
