<?php
class ProductModel {
    private $pdo;
    
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }
    
    /**
     * Get products with search, pagination, and sorting
     */
    public function getProducts($search = '', $page = 1, $limit = 10, $sortBy = 'B.NamaBarang', $sortOrder = 'ASC') {
        try {
            $offset = ($page - 1) * $limit;
            
            // Build search condition
            $searchCondition = '';
            $params = [];
            
            if (!empty($search)) {
                $searchCondition = 'AND (B.NamaBarang LIKE ? OR M.NamaMerek LIKE ? OR J.NamaJenis LIKE ? OR K.NamaKelompok LIKE ?)';
                $searchParam = '%' . $search . '%';
                $params = [$searchParam, $searchParam, $searchParam, $searchParam];
            }
            
            // Validate sort column to prevent SQL injection
            $allowedSortColumns = [
                'B.NamaBarang' => 'Nama Barang',
                'B.Satuan' => 'Satuan', 
                'M.NamaMerek' => 'Nama Merek',
                'J.NamaJenis' => 'Nama Jenis',
                'B.NamaUkuran' => 'Nama Ukuran',
                'B.HargaJual' => 'HargaJual',
                'S.StokAkhir' => 'StokAkhir'
            ];
            
            if (!array_key_exists($sortBy, $allowedSortColumns)) {
                $sortBy = 'B.NamaBarang';
            }
            
            // Validate sort order
            $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';
            
            // Main query - using string concatenation for OFFSET and FETCH
            $sql = "SELECT B.KodeBarang, B.NamaBarang, B.Satuan, K.NamaKelompok, J.NamaJenis, M.NamaMerek, B.NamaUkuran, B.HargaBeli, 
                           B.DiscountBeli, B.HargaPokok, B.HargaJual, B.DiscountJual, B.Status, ISNULL(S.StokAkhir,0) AS StokAkhir
                    FROM FILEBARANG B 
                    LEFT JOIN StokBarang S ON B.KodeBarang = S.KodeBarang 
                    INNER JOIN TABELKELOMPOK K ON B.KodeKelompok = K.KodeKelompok 
                    INNER JOIN TABELJENIS J ON B.KodeJenis = J.KodeJenis 
                    INNER JOIN TABELMEREK M ON B.KodeMerek = M.KodeMerek 
                    WHERE B.Status = 1 AND 1=1 $searchCondition
                    ORDER BY $sortBy $sortOrder
                    OFFSET " . (int)$offset . " ROWS FETCH NEXT " . (int)$limit . " ROWS ONLY";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $results;
            
        } catch (PDOException $e) {
            error_log("ProductModel::getProducts error: " . $e->getMessage());
            error_log("ProductModel::getProducts error trace: " . $e->getTraceAsString());
            return [];
        }
    }
    
    /**
     * Get total count of products for pagination
     */
    public function getTotalProducts($search = '') {
        try {
            $searchCondition = '';
            $params = [];
            
            if (!empty($search)) {
                $searchCondition = 'AND (B.NamaBarang LIKE ? OR M.NamaMerek LIKE ? OR J.NamaJenis LIKE ? OR K.NamaKelompok LIKE ?)';
                $searchParam = '%' . $search . '%';
                $params = [$searchParam, $searchParam, $searchParam, $searchParam];
            }
            
            $sql = "SELECT COUNT(*) as total
                    FROM FILEBARANG B 
                    LEFT JOIN StokBarang S ON B.KodeBarang = S.KodeBarang 
                    INNER JOIN TABELKELOMPOK K ON B.KodeKelompok = K.KodeKelompok 
                    INNER JOIN TABELJENIS J ON B.KodeJenis = J.KodeJenis 
                    INNER JOIN TABELMEREK M ON B.KodeMerek = M.KodeMerek 
                    WHERE B.Status = 1 AND 1=1 $searchCondition";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
            
        } catch (PDOException $e) {
            error_log("ProductModel::getTotalProducts error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get product by KodeBarang
     */
    public function getProductByCode($kodeBarang) {
        try {
            $sql = "SELECT B.KodeBarang, B.NamaBarang, B.Satuan, K.NamaKelompok, J.NamaJenis, M.NamaMerek, B.NamaUkuran, B.HargaBeli, 
                           B.DiscountBeli, B.HargaPokok, B.HargaJual, B.DiscountJual, B.Status, ISNULL(S.StokAkhir,0) AS StokAkhir
                    FROM FILEBARANG B 
                    LEFT JOIN StokBarang S ON B.KodeBarang = S.KodeBarang 
                    INNER JOIN TABELKELOMPOK K ON B.KodeKelompok = K.KodeKelompok 
                    INNER JOIN TABELJENIS J ON B.KodeJenis = J.KodeJenis 
                    INNER JOIN TABELMEREK M ON B.KodeMerek = M.KodeMerek 
                    WHERE B.KodeBarang = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$kodeBarang]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("ProductModel::getProductByCode error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all available sort columns
     */
    public function getSortColumns() {
        return [
            'B.NamaBarang' => 'Nama Barang',
            'B.Satuan' => 'Satuan',
            'M.NamaMerek' => 'Nama Merek', 
            'J.NamaJenis' => 'Nama Jenis',
            'B.NamaUkuran' => 'Nama Ukuran',
            'B.HargaJual' => 'Harga Jual',
            'S.StokAkhir' => 'Stok Akhir'
        ];
    }
}
?>
