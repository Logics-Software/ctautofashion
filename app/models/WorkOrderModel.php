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
    public function getWorkOrders($filters, $limit, $offset, $userID = null, $tipeUser = null) {
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
            
            // Filter by UserID if TipeUser is 0 or 1 (Operator/Staff)
            if (($tipeUser === 0 || $tipeUser === 1) && !empty($userID)) {
                $sql .= " AND H.UserID = ?";
                $params[] = $userID;
            }
            
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
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            
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
    public function getTotalWorkOrders($filters, $userID = null, $tipeUser = null) {
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
            
            // Filter by UserID if TipeUser is 0 or 1 (Operator/Staff)
            if (($tipeUser === 0 || $tipeUser === 1) && !empty($userID)) {
                $sql .= " AND H.UserID = ?";
                $params[] = $userID;
            }
            
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
     * Get work order detail by NoOrder
     */
    public function getWorkOrderDetail($noOrder) {
        try {
            // Trim whitespace from NoOrder
            $noOrder = trim($noOrder);
            
            // Get header information from HeaderOrder with HeaderPenjualan JOIN
            $sqlHeader = "SELECT H.NoOrder, H.TanggalOrder, H.StatusOrder, 
                                 H.KodeCustomer, H.KodeKendaraan, 
                                 ISNULL(J.NoPenjualan,'') AS NoInvoice, 
                                 J.TanggalPenjualan AS TglInvoice,
                                 C.NamaCustomer, C.AlamatCustomer, C.NoTelepon,
                                 K.NoPolisi, K.NamaKendaraan, K.Warna,
                                 P.NamaPicker as Marketing,
                                 H.TotalJasa, H.TotalBarang, H.TotalOrder
                          FROM HeaderOrder H
                          LEFT JOIN FilePicker P ON H.KodePicker = P.KodePicker
                          LEFT JOIN HeaderPenjualan J ON H.NoOrder = J.NoOrder
                          LEFT JOIN FileCustomer C ON H.KodeCustomer = C.KodeCustomer
                          LEFT JOIN FileKendaraan K ON H.KodeKendaraan = K.KodeKendaraan
                          WHERE H.NoOrder = ?";
            
            $stmtHeader = $this->pdo->prepare($sqlHeader);
            $stmtHeader->execute([$noOrder]);
            $header = $stmtHeader->fetch(PDO::FETCH_ASSOC);
            
            if (!$header) {
                return null;
            }
            
            // Get service transactions from DetailOrderJasa
            $sqlService = "SELECT DOJ.NamaJasa, ISNULL(M.NamaMontir,'') AS Mekanik,
                                DOJ.Jumlah AS Qty, 
                                DOJ.HargaSatuan AS Tarif,
                                DOJ.TotalHarga AS Total
                            FROM DetailOrderJasa DOJ
                            INNER JOIN HeaderOrder HO ON DOJ.NoOrder = HO.NoOrder
                            LEFT JOIN FileMontir M ON HO.KodeMontir = M.KodeMontir
                            WHERE DOJ.NoOrder = ?";
            
            $stmtService = $this->pdo->prepare($sqlService);
            $stmtService->execute([$noOrder]);
            $services = $stmtService->fetchAll(PDO::FETCH_ASSOC);
            
            // Get item transactions from DetailOrderBarang
            $sqlItem = "SELECT DOB.NamaBarang, 
                              M.NamaMerek AS MerekBarang,
							  J.NamaJenis AS JenisBarang,
                              DOB.Satuan, 
                              DOB.Jumlah AS Qty, 
                              DOB.HargaSatuan AS Harga,
                              DOB.TotalHarga AS Total
                       FROM DetailOrderBarang DOB
					   INNER JOIN FileBarang B ON DOB.KodeBarang = B.KodeBarang
					   INNER JOIN TabelMerek M ON B.KodeMerek = M.KodeMerek
					   INNER JOIN TabelJenis J ON B.KodeJenis = J.KodeJenis
                       WHERE DOB.NoOrder = ?";
            
            $stmtItem = $this->pdo->prepare($sqlItem);
            $stmtItem->execute([$noOrder]);
            $items = $stmtItem->fetchAll(PDO::FETCH_ASSOC);
            
            // Add status text
            $header['StatusText'] = $this->getStatusText($header['StatusOrder']);
            
            return [
                'header' => $header,
                'services' => $services,
                'items' => $items
            ];
            
        } catch (PDOException $e) {
            error_log("Error getting work order detail: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get order statistics by status with period filter
     */
    public function getOrderStatistics($period = 'today', $startDate = null, $endDate = null, $userID = null, $tipeUser = null) {
        try {
            // Determine date range based on period
            $dateCondition = "";
            $params = [];
            
            switch ($period) {
                case 'today':
                    $dateCondition = "AND CAST(H.TanggalOrder AS DATE) = CAST(GETDATE() AS DATE)";
                    break;
                    
                case 'yesterday':
                    $dateCondition = "AND CAST(H.TanggalOrder AS DATE) = CAST(DATEADD(DAY, -1, GETDATE()) AS DATE)";
                    break;
                    
                case 'this_week':
                    $dateCondition = "AND H.TanggalOrder >= DATEADD(WEEK, DATEDIFF(WEEK, 0, GETDATE()), 0) 
                                     AND H.TanggalOrder < DATEADD(WEEK, DATEDIFF(WEEK, 0, GETDATE()) + 1, 0)";
                    break;
                    
                case 'this_month':
                    $dateCondition = "AND MONTH(H.TanggalOrder) = MONTH(GETDATE()) 
                                     AND YEAR(H.TanggalOrder) = YEAR(GETDATE())";
                    break;
                    
                case 'this_year':
                    $dateCondition = "AND YEAR(H.TanggalOrder) = YEAR(GETDATE())";
                    break;
                    
                case 'custom':
                    if ($startDate && $endDate) {
                        $dateCondition = "AND H.TanggalOrder >= ? AND H.TanggalOrder <= ?";
                        $params[] = $startDate;
                        $params[] = $endDate . ' 23:59:59';
                    }
                    break;
                    
                default:
                    $dateCondition = "AND CAST(H.TanggalOrder AS DATE) = CAST(GETDATE() AS DATE)";
                    break;
            }
            
            // Add UserID filter if TipeUser is 0 or 1 (Operator/Staff)
            $userCondition = "";
            // Convert to int for comparison to handle both string and integer
            $tipeUserInt = (int)$tipeUser;
            if ($tipeUserInt === 0 || $tipeUserInt === 1) {
                if (!empty($userID)) {
                    $userCondition = "AND H.UserID = ?";
                    $params[] = $userID;
                }
            }
            
            $sql = "SELECT 
                        H.StatusOrder,
                        COUNT(H.NoOrder) as TotalOrder
                    FROM HeaderOrder H
                    WHERE 1=1 $dateCondition $userCondition
                    GROUP BY H.StatusOrder
                    ORDER BY H.StatusOrder";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Initialize all statuses with 0
            $statistics = [
                '0' => ['status' => 'Belum diproses', 'count' => 0, 'color' => 'secondary'],
                '1' => ['status' => 'Sedang diproses', 'count' => 0, 'color' => 'info'],
                '2' => ['status' => 'Proses Selesai', 'count' => 0, 'color' => 'warning'],
                '3' => ['status' => 'Faktur dibuat', 'count' => 0, 'color' => 'primary'],
                '4' => ['status' => 'Telah dibayar', 'count' => 0, 'color' => 'success'],
                '5' => ['status' => 'Dibatalkan', 'count' => 0, 'color' => 'danger']
            ];
            
            // Fill in the actual counts
            foreach ($results as $row) {
                $status = (string)$row['StatusOrder'];
                if (isset($statistics[$status])) {
                    $statistics[$status]['count'] = (int)$row['TotalOrder'];
                }
            }
            
            // Calculate total
            $total = array_sum(array_column($statistics, 'count'));
            
            // Force statistics to be encoded as JSON object, not array
            // Convert to stdClass to ensure JSON encoding as object
            $statisticsObject = new \stdClass();
            foreach ($statistics as $key => $value) {
                $statisticsObject->$key = $value;
            }
            
            return [
                'statistics' => $statisticsObject,
                'total' => $total
            ];
            
        } catch (PDOException $e) {
            error_log("Error getting order statistics: " . $e->getMessage());
            
            // Force statistics to be encoded as JSON object, not array
            $statisticsObject = new \stdClass();
            $statisticsObject->{'0'} = ['status' => 'Belum diproses', 'count' => 0, 'color' => 'secondary'];
            $statisticsObject->{'1'} = ['status' => 'Sedang diproses', 'count' => 0, 'color' => 'info'];
            $statisticsObject->{'2'} = ['status' => 'Proses Selesai', 'count' => 0, 'color' => 'warning'];
            $statisticsObject->{'3'} = ['status' => 'Faktur dibuat', 'count' => 0, 'color' => 'primary'];
            $statisticsObject->{'4'} = ['status' => 'Telah dibayar', 'count' => 0, 'color' => 'success'];
            $statisticsObject->{'5'} = ['status' => 'Dibatalkan', 'count' => 0, 'color' => 'danger'];
            
            return [
                'statistics' => $statisticsObject,
                'total' => 0
            ];
        }
    }
    
    /**
     * Get monthly revenue statistics for the last 12 months
     */
    public function getMonthlyRevenue() {
        try {
            // Query untuk total seluruh penjualan per bulan
            $sqlTotal = "SELECT 
                            FORMAT(J.TanggalPenjualan, 'yyyy-MM') as Bulan,
                            SUM(J.TotalPenjualan) as TotalPenjualan
                        FROM HeaderPenjualan J
                        WHERE J.TanggalPenjualan >= DATEADD(MONTH, -12, GETDATE())
                        GROUP BY FORMAT(J.TanggalPenjualan, 'yyyy-MM')
                        ORDER BY FORMAT(J.TanggalPenjualan, 'yyyy-MM')";
            
            $stmtTotal = $this->pdo->prepare($sqlTotal);
            $stmtTotal->execute();
            $totalResults = $stmtTotal->fetchAll(PDO::FETCH_ASSOC);
            
            // Query untuk penjualan customer perorangan (JenisCustomer = 0)
            $sqlPerorangan = "SELECT 
                                FORMAT(J.TanggalPenjualan, 'yyyy-MM') as Bulan,
                                SUM(J.TotalPenjualan) as TotalPenjualan
                            FROM HeaderPenjualan J
                            INNER JOIN FileCustomer C ON J.KodeCustomer = C.KodeCustomer
                            INNER JOIN FileJenisCustomer JC ON C.KodeCustomer = JC.KodeCustomer
                            WHERE J.TanggalPenjualan >= DATEADD(MONTH, -12, GETDATE())
                              AND JC.JenisCustomer = 0
                            GROUP BY FORMAT(J.TanggalPenjualan, 'yyyy-MM')
                            ORDER BY FORMAT(J.TanggalPenjualan, 'yyyy-MM')";
            
            $stmtPerorangan = $this->pdo->prepare($sqlPerorangan);
            $stmtPerorangan->execute();
            $peroranganResults = $stmtPerorangan->fetchAll(PDO::FETCH_ASSOC);
            
            // Query untuk penjualan customer perusahaan (JenisCustomer = 1)
            $sqlPerusahaan = "SELECT 
                                FORMAT(J.TanggalPenjualan, 'yyyy-MM') as Bulan,
                                SUM(J.TotalPenjualan) as TotalPenjualan
                            FROM HeaderPenjualan J
                            INNER JOIN FileCustomer C ON J.KodeCustomer = C.KodeCustomer
                            INNER JOIN FileJenisCustomer JC ON C.KodeCustomer = JC.KodeCustomer
                            WHERE J.TanggalPenjualan >= DATEADD(MONTH, -12, GETDATE())
                              AND JC.JenisCustomer = 1
                            GROUP BY FORMAT(J.TanggalPenjualan, 'yyyy-MM')
                            ORDER BY FORMAT(J.TanggalPenjualan, 'yyyy-MM')";
            
            $stmtPerusahaan = $this->pdo->prepare($sqlPerusahaan);
            $stmtPerusahaan->execute();
            $perusahaanResults = $stmtPerusahaan->fetchAll(PDO::FETCH_ASSOC);
            
            // Prepare data for chart - ensure we have 12 months
            $months = [];
            $totalRevenue = [];
            $peroranganRevenue = [];
            $perusahaanRevenue = [];
            
            // Generate last 12 months
            for ($i = 11; $i >= 0; $i--) {
                $date = date('Y-m', strtotime("-$i months"));
                $months[] = date('M Y', strtotime($date . '-01'));
                
                // Find matching total revenue
                $foundTotal = false;
                foreach ($totalResults as $row) {
                    if ($row['Bulan'] === $date) {
                        $totalRevenue[] = (float)$row['TotalPenjualan'];
                        $foundTotal = true;
                        break;
                    }
                }
                if (!$foundTotal) {
                    $totalRevenue[] = 0;
                }
                
                // Find matching perorangan revenue
                $foundPerorangan = false;
                foreach ($peroranganResults as $row) {
                    if ($row['Bulan'] === $date) {
                        $peroranganRevenue[] = (float)$row['TotalPenjualan'];
                        $foundPerorangan = true;
                        break;
                    }
                }
                if (!$foundPerorangan) {
                    $peroranganRevenue[] = 0;
                }
                
                // Find matching perusahaan revenue
                $foundPerusahaan = false;
                foreach ($perusahaanResults as $row) {
                    if ($row['Bulan'] === $date) {
                        $perusahaanRevenue[] = (float)$row['TotalPenjualan'];
                        $foundPerusahaan = true;
                        break;
                    }
                }
                if (!$foundPerusahaan) {
                    $perusahaanRevenue[] = 0;
                }
            }
            
            return [
                'months' => $months,
                'totalRevenue' => $totalRevenue,
                'peroranganRevenue' => $peroranganRevenue,
                'perusahaanRevenue' => $perusahaanRevenue
            ];
            
        } catch (PDOException $e) {
            error_log("Error getting monthly revenue: " . $e->getMessage());
            
            // Return empty data structure
            $months = [];
            for ($i = 11; $i >= 0; $i--) {
                $months[] = date('M Y', strtotime("-$i months"));
            }
            
            return [
                'months' => $months,
                'totalRevenue' => array_fill(0, 12, 0),
                'peroranganRevenue' => array_fill(0, 12, 0),
                'perusahaanRevenue' => array_fill(0, 12, 0)
            ];
        }
    }
    
    /**
     * Get work order statistics by month for the last 12 months
     */
    public function getMonthlyStatistics() {
        try {
            // Get data for the last 12 months
            $sql = "SELECT 
                        FORMAT(H.TanggalOrder, 'yyyy-MM') as Bulan,
                        COUNT(DISTINCT H.NoOrder) as TotalOrder,
                        SUM(CASE WHEN H.StatusOrder = '4' THEN 1 ELSE 0 END) as OrderSelesai,
                        SUM(CASE WHEN H.StatusOrder = '5' THEN 1 ELSE 0 END) as OrderBatal
                    FROM HeaderOrder H
                    WHERE H.TanggalOrder >= DATEADD(MONTH, -12, GETDATE())
                    GROUP BY FORMAT(H.TanggalOrder, 'yyyy-MM')
                    ORDER BY FORMAT(H.TanggalOrder, 'yyyy-MM')";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Prepare data for chart - ensure we have 12 months
            $months = [];
            $totalOrders = [];
            $completedOrders = [];
            $canceledOrders = [];
            
            // Generate last 12 months
            for ($i = 11; $i >= 0; $i--) {
                $date = date('Y-m', strtotime("-$i months"));
                $months[] = date('M Y', strtotime($date . '-01'));
                
                // Find matching data
                $found = false;
                foreach ($results as $row) {
                    if ($row['Bulan'] === $date) {
                        $totalOrders[] = (int)$row['TotalOrder'];
                        $completedOrders[] = (int)$row['OrderSelesai'];
                        $canceledOrders[] = (int)$row['OrderBatal'];
                        $found = true;
                        break;
                    }
                }
                
                // If no data for this month, set to 0
                if (!$found) {
                    $totalOrders[] = 0;
                    $completedOrders[] = 0;
                    $canceledOrders[] = 0;
                }
            }
            
            return [
                'months' => $months,
                'totalOrders' => $totalOrders,
                'completedOrders' => $completedOrders,
                'canceledOrders' => $canceledOrders
            ];
            
        } catch (PDOException $e) {
            error_log("Error getting monthly statistics: " . $e->getMessage());
            
            // Return empty data structure
            $months = [];
            for ($i = 11; $i >= 0; $i--) {
                $months[] = date('M Y', strtotime("-$i months"));
            }
            
            return [
                'months' => $months,
                'totalOrders' => array_fill(0, 12, 0),
                'completedOrders' => array_fill(0, 12, 0),
                'canceledOrders' => array_fill(0, 12, 0)
            ];
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
