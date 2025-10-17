<?php
class WorkOrderModel {
    private $pdo;
    
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }
    
    /**
     * Get work orders with filters
     */
    public function getWorkOrders($filters, $limit, $offset) {
        try {
            // Query with filters - SQL Server pagination using ROW_NUMBER()
            $sql = "SELECT * FROM (
                        SELECT ROW_NUMBER() OVER (ORDER BY H.NoOrder DESC) as RowNum,
                               H.NoOrder, H.TanggalOrder, H.StatusOrder, H.KodeCustomer, H.KodeKendaraan,
                               C.NamaCustomer, C.AlamatCustomer, C.NoTelepon,
                               K.NoPolisi, K.NamaKendaraan
                        FROM HeaderOrder H
                        LEFT JOIN FileCustomer C ON H.KodeCustomer = C.KodeCustomer
                        LEFT JOIN FileKendaraan K ON H.KodeKendaraan = K.KodeKendaraan
                        WHERE H.TanggalOrder >= ? AND H.TanggalOrder <= ?";
            
            $params = [];
            
            // Date filters
            $startDate = $filters['start_date'] ?? date('Y-m-d');
            $endDate = $filters['end_date'] ?? date('Y-m-d');
            
            $params[] = $startDate;
            $params[] = $endDate . ' 23:59:59';
            
            // Status filter
            if (!empty($filters['status'])) {
                $sql .= " AND H.StatusOrder = ?";
                $params[] = $filters['status'];
            }
            
            // Customer filter
            if (!empty($filters['customer'])) {
                $sql .= " AND H.KodeCustomer = ?";
                $params[] = $filters['customer'];
            }
            
            // Vehicle filter (by NoPolisi)
            if (!empty($filters['no_polisi'])) {
                $sql .= " AND K.NoPolisi = ?";
                $params[] = $filters['no_polisi'];
            }
            
            // Search filter
            if (!empty($filters['search'])) {
                $sql .= " AND (H.NoOrder LIKE ? OR C.NamaCustomer LIKE ? OR K.NoPolisi LIKE ? OR K.NamaKendaraan LIKE ?)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            // Close subquery and add pagination
            $sql .= ") AS PaginatedResults WHERE RowNum > ? AND RowNum <= ?";
            $params[] = $offset;
            $params[] = $offset + $limit;
            
            // Debug: Log the query and parameters
            error_log("SQL Query: " . $sql);
            error_log("Parameters: " . print_r($params, true));
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Debug: Log results count
            error_log("Results count: " . count($results));
            
            
            // Ensure all required fields exist in each result
            foreach ($results as &$row) {
                $row['NamaCustomer'] = $row['NamaCustomer'] ?? 'N/A';
                $row['AlamatCustomer'] = $row['AlamatCustomer'] ?? 'N/A';
                $row['NoTelepon'] = $row['NoTelepon'] ?? 'N/A';
                $row['NoPolisi'] = $row['NoPolisi'] ?? 'N/A';
                $row['NamaKendaraan'] = $row['NamaKendaraan'] ?? 'N/A';
                $row['StatusOrder'] = $row['StatusOrder'] ?? '0';
            }
            
            // Add status text
            foreach ($results as &$row) {
                $row['StatusText'] = $this->getStatusText($row['StatusOrder']);
            }
            
            return $results;
            
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get total work orders count with filters
     */
    public function getTotalWorkOrders($filters) {
        try {
            // Count query with all filters
            $sql = "SELECT COUNT(*) as total 
                    FROM HeaderOrder H
                    LEFT JOIN FileCustomer C ON H.KodeCustomer = C.KodeCustomer
                    LEFT JOIN FileKendaraan K ON H.KodeKendaraan = K.KodeKendaraan
                    WHERE H.TanggalOrder >= ? AND H.TanggalOrder <= ?";
            
            $params = [];
            
            // Date filters
            $startDate = $filters['start_date'] ?? date('Y-m-d');
            $endDate = $filters['end_date'] ?? date('Y-m-d');
            
            $params[] = $startDate;
            $params[] = $endDate . ' 23:59:59';
            
            // Status filter
            if (!empty($filters['status'])) {
                $sql .= " AND H.StatusOrder = ?";
                $params[] = $filters['status'];
            }
            
            // Customer filter
            if (!empty($filters['customer'])) {
                $sql .= " AND H.KodeCustomer = ?";
                $params[] = $filters['customer'];
            }
            
            // Vehicle filter (by NoPolisi)
            if (!empty($filters['no_polisi'])) {
                $sql .= " AND K.NoPolisi = ?";
                $params[] = $filters['no_polisi'];
            }
            
            // Search filter
            if (!empty($filters['search'])) {
                $sql .= " AND (H.NoOrder LIKE ? OR C.NamaCustomer LIKE ? OR K.NoPolisi LIKE ? OR K.NamaKendaraan LIKE ?)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $total = $result['total'] ?? 0;
            
            
            return $total;
            
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    /**
     * Get all customers who have work orders
     */
    public function getCustomers() {
        try {
            $sql = "SELECT DISTINCT C.KodeCustomer, C.NamaCustomer 
                    FROM FileCustomer C
                    INNER JOIN HeaderOrder H ON C.KodeCustomer = H.KodeCustomer
                    ORDER BY C.NamaCustomer";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            
            return $results;
            
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get all vehicles that have work orders
     */
    public function getVehicles() {
        try {
            $sql = "SELECT DISTINCT K.KodeKendaraan, K.NoPolisi, K.NamaKendaraan
                    FROM FileKendaraan K
                    INNER JOIN HeaderOrder H ON K.KodeKendaraan = H.KodeKendaraan
                    ORDER BY K.NoPolisi";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            
            return $results;
            
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Search customers by name
     */
    public function searchCustomers($search) {
        try {
            $sql = "SELECT DISTINCT C.KodeCustomer, C.NamaCustomer 
                    FROM FileCustomer C
                    INNER JOIN HeaderOrder H ON C.KodeCustomer = H.KodeCustomer
                    WHERE C.NamaCustomer LIKE ?
                    ORDER BY C.NamaCustomer
                    LIMIT 20";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['%' . $search . '%']);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Search vehicles by license plate or vehicle name
     */
    public function searchVehicles($search) {
        try {
            $sql = "SELECT DISTINCT K.KodeKendaraan, K.NoPolisi, K.NamaKendaraan
                    FROM FileKendaraan K
                    INNER JOIN HeaderOrder H ON K.KodeKendaraan = H.KodeKendaraan
                    WHERE K.NoPolisi LIKE ? OR K.NamaKendaraan LIKE ?
                    ORDER BY K.NoPolisi
                    LIMIT 20";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['%' . $search . '%', '%' . $search . '%']);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            return [];
        }
    }
    
    
    /**
     * Get status text from status code
     */
    private function getStatusText($status) {
        $statusMap = [
            '0' => 'Belum diproses',
            '1' => 'Sedang diproses',
            '2' => 'Proses Selesai',
            '3' => 'Faktur dibuat',
            '4' => 'Telah dibayar',
            '5' => 'Dibatalkan'
        ];
        
        return $statusMap[$status] ?? 'Tidak diketahui';
    }
}
?>
