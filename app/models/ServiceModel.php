<?php
class ServiceModel {
    private $pdo;
    
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }
    
    /**
     * Search customers by name, phone, or license plate
     */
    public function searchCustomers($searchTerm = '') {
        try {
            $searchCondition = '';
            $params = [];
            
            if (!empty($searchTerm)) {
                $searchCondition = 'AND (C.NamaCustomer LIKE ? OR C.NoTelepon LIKE ? OR K.NoPolisi LIKE ?)';
                $searchParam = '%' . $searchTerm . '%';
                $params = [$searchParam, $searchParam, $searchParam];
            }
            
            $sql = "SELECT DISTINCT C.KodeCustomer, C.NamaCustomer, C.AlamatCustomer, C.Kota, C.NoTelepon, C.KontakPerson AS PIC
                    FROM FileCustomer C
                    LEFT JOIN FileKendaraan K ON C.KodeCustomer = K.KodeCustomer
                    WHERE C.Status = 1 $searchCondition
                    ORDER BY C.NamaCustomer";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("ServiceModel::searchCustomers error: " . $e->getMessage());
            error_log("ServiceModel::searchCustomers error trace: " . $e->getTraceAsString());
            return [];
        }
    }
    
    /**
     * Get customer transactions (service orders)
     */
    public function getCustomerTransactions($kodeCustomer) {
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
                    WHERE H.KodeCustomer = ?
                    ORDER BY H.TanggalOrder DESC, H.NoOrder DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$kodeCustomer]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("ServiceModel::getCustomerTransactions error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get service transactions for specific work order
     */
    public function getServiceTransactions($noOrder, $kodeCustomer) {
        try {
            $sql = "SELECT H.*, C.KodeCustomer, C.NamaCustomer, C.Kota, C.NoTelepon, C.KontakPerson, 
                           P.NamaPicker AS NamaMarketing, M.NamaMontir, B.NamaKendaraan, B.Tipe, 
                           B.Tahun, B.Warna, B.NoPolisi, B.KodeMerek, TM.NamaMerek, B.KodeJenis, 
                           TJ.NamaJenis, D.*, J.NamaJasa, J.Satuan 
                    FROM HeaderPenjualan H 
                    INNER JOIN DetailPenjualanJasa D ON H.NoPenjualan = D.NoPenjualan
                    INNER JOIN FileJasa J ON D.KodeJasa = J.KodeJasa
                    INNER JOIN FileCustomer C ON H.KodeCustomer = C.KodeCustomer 
                    INNER JOIN FilePicker P ON H.KodePicker = P.KodePicker
                    LEFT JOIN FileMontir M ON H.KodeMontir = M.KodeMontir
                    LEFT JOIN FileKendaraan B ON H.KodeKendaraan = B.KodeKendaraan
                    LEFT JOIN TabelJenisKendaraan TJ ON B.KodeJenis = TJ.KodeJenis 
                    LEFT JOIN TabelMerekKendaraan TM ON B.KodeMerek = TM.NamaMerek
                    WHERE H.KodeCustomer = ? AND H.NoOrder = ?
                    ORDER BY H.TanggalPenjualan DESC, H.NoPenjualan DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$kodeCustomer, $noOrder]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("ServiceModel::getServiceTransactions error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get parts transactions for specific work order
     */
    public function getPartsTransactions($noOrder, $kodeCustomer) {
        try {
            $sql = "SELECT H.*, C.KodeCustomer, C.NamaCustomer, C.Kota, C.NoTelepon, C.KontakPerson, 
                           P.NamaPicker AS NamaMarketing, M.NamaMontir, B.NamaKendaraan, B.Tipe, 
                           B.Tahun, B.Warna, B.NoPolisi, B.KodeMerek, TM.NamaMerek, B.KodeJenis, 
                           TJ.NamaJenis, D.*, J.NamaBarang, J.Satuan, MEREK.NamaMerek AS MerekBarang,
                           JENIS.NamaJenis AS JenisBarang
                    FROM HeaderPenjualan H 
                    INNER JOIN DetailPenjualanBarang D ON H.NoPenjualan = D.NoPenjualan
                    INNER JOIN FILEBARANG J ON D.KodeBarang = J.KodeBarang
                    INNER JOIN FileCustomer C ON H.KodeCustomer = C.KodeCustomer 
                    INNER JOIN FilePicker P ON H.KodePicker = P.KodePicker
                    LEFT JOIN FileMontir M ON H.KodeMontir = M.KodeMontir
                    LEFT JOIN FileKendaraan B ON H.KodeKendaraan = B.KodeKendaraan
                    LEFT JOIN TabelJenisKendaraan TJ ON B.KodeJenis = TJ.KodeJenis 
                    LEFT JOIN TabelMerekKendaraan TM ON B.KodeMerek = TM.NamaMerek
                    LEFT JOIN TABELMEREK MEREK ON J.KodeMerek = MEREK.KodeMerek
                    LEFT JOIN TABELJENIS JENIS ON J.KodeJenis = JENIS.KodeJenis
                    WHERE H.KodeCustomer = ? AND H.NoOrder = ?
                    ORDER BY H.TanggalPenjualan DESC, H.NoPenjualan DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$kodeCustomer, $noOrder]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("ServiceModel::getPartsTransactions error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get work order information
     */
    public function getWorkOrderInfo($noOrder, $kodeCustomer) {
        try {
            $sql = "SELECT H.NoOrder, H.TanggalOrder, B.NamaKendaraan, B.NoPolisi, B.Warna, P.NamaPicker AS NamaMarketing,
                           J.NoPenjualan, J.TanggalPenjualan
                    FROM HeaderOrder H 
                    INNER JOIN FileKendaraan B ON H.KodeKendaraan = B.KodeKendaraan
                    INNER JOIN FilePicker P ON H.KodePicker = P.KodePicker
                    LEFT JOIN HeaderPenjualan J ON H.NoOrder = J.NoOrder
                    WHERE H.NoOrder = ? AND H.KodeCustomer = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$noOrder, $kodeCustomer]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("ServiceModel::getWorkOrderInfo error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get customer by code
     */
    public function getCustomerByCode($kodeCustomer) {
        try {
            $sql = "SELECT C.KodeCustomer, C.NamaCustomer, C.AlamatCustomer, C.Kota, C.NoTelepon, C.KontakPerson AS PIC
                    FROM FileCustomer C
                    WHERE C.KodeCustomer = ? AND C.Status = 1";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$kodeCustomer]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("ServiceModel::getCustomerByCode error: " . $e->getMessage());
            return false;
        }
    }
}
?>
