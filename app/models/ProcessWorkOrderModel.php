<?php
class ProcessWorkOrderModel {
    private $pdo;
    
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }
    
    /**
     * Get work orders with filters (StatusOrder < 2 only)
     * Only search filter, no date/customer/vehicle filters
     */
    public function getWorkOrders($search, $limit, $offset, $userID = null, $tipeUser = null) {
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
                        WHERE H.StatusOrder < 2";
            
            $params = [];
            
            // Filter by UserID if TipeUser is 0 or 1 (Operator/Staff)
            if (($tipeUser === 0 || $tipeUser === 1) && !empty($userID)) {
                $sql .= " AND H.UserID = ?";
                $params[] = $userID;
            }
            
            // Search filter only
            if (!empty($search)) {
                $sql .= " AND (H.NoOrder LIKE ? OR C.NamaCustomer LIKE ? OR K.NoPolisi LIKE ? OR K.NamaKendaraan LIKE ?)";
                $searchTerm = '%' . $search . '%';
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
     * Get total work orders count with filters (StatusOrder < 2 only)
     * Only search filter, no date/customer/vehicle filters
     */
    public function getTotalWorkOrders($search, $userID = null, $tipeUser = null) {
        try {
            // Count query with all filters
            $sql = "SELECT COUNT(*) as total 
                    FROM HeaderOrder H
                    LEFT JOIN FileCustomer C ON H.KodeCustomer = C.KodeCustomer
                    LEFT JOIN FileKendaraan K ON H.KodeKendaraan = K.KodeKendaraan
                    WHERE H.StatusOrder < 2";
            
            $params = [];
            
            // Filter by UserID if TipeUser is 0 or 1 (Operator/Staff)
            if (($tipeUser === 0 || $tipeUser === 1) && !empty($userID)) {
                $sql .= " AND H.UserID = ?";
                $params[] = $userID;
            }
            
            // Search filter only
            if (!empty($search)) {
                $sql .= " AND (H.NoOrder LIKE ? OR C.NamaCustomer LIKE ? OR K.NoPolisi LIKE ? OR K.NamaKendaraan LIKE ?)";
                $searchTerm = '%' . $search . '%';
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
            return null;
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
    
    /**
     * Proses Work Order - Update StatusOrder di HeaderOrder dan update KartuOrder
     */
    public function prosesWorkOrder($noOrder, $userID) {
        try {
            // Start transaction
            $this->pdo->beginTransaction();
            
            // 1. Update HeaderOrder - set StatusOrder = 1 (Sedang Diproses)
            $sqlHeader = "UPDATE HeaderOrder SET StatusOrder = 1 WHERE NoOrder = ?";
            $stmtHeader = $this->pdo->prepare($sqlHeader);
            $resultHeader = $stmtHeader->execute([$noOrder]);
            
            if (!$resultHeader || $stmtHeader->rowCount() === 0) {
                $this->pdo->rollBack();
                return ['success' => false, 'error' => 'Work Order tidak ditemukan atau sudah diproses'];
            }
            
            // 2. Update KartuOrder - set ProsesUserID dan ProsesTanggal
            $currentDate = date('Y-m-d H:i:s');
            $sqlKartu = "UPDATE KartuOrder 
                        SET ProsesUserID = ?, 
                            ProsesTanggal = ? 
                        WHERE NoOrder = ?";
            $stmtKartu = $this->pdo->prepare($sqlKartu);
            $resultKartu = $stmtKartu->execute([$userID, $currentDate, $noOrder]);
            
            // If KartuOrder doesn't exist, insert it
            if ($stmtKartu->rowCount() === 0) {
                $sqlInsertKartu = "INSERT INTO KartuOrder (NoOrder, ProsesUserID, ProsesTanggal) 
                                  VALUES (?, ?, ?)";
                $stmtInsertKartu = $this->pdo->prepare($sqlInsertKartu);
                $stmtInsertKartu->execute([$noOrder, $userID, $currentDate]);
            }
            
            // Commit transaction
            $this->pdo->commit();
            
            return ['success' => true];
            
        } catch (PDOException $e) {
            // Rollback on error
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['success' => false, 'error' => 'Gagal memproses Work Order'];
        }
    }
    
    /**
     * Selesai Work Order - Update StatusOrder di HeaderOrder dan update KartuOrder
     */
    public function selesaiWorkOrder($noOrder, $userID) {
        try {
            // Start transaction
            $this->pdo->beginTransaction();
            
            // 1. Update HeaderOrder - set StatusOrder = 2 (Selesai)
            $sqlHeader = "UPDATE HeaderOrder SET StatusOrder = 2 WHERE NoOrder = ?";
            $stmtHeader = $this->pdo->prepare($sqlHeader);
            $resultHeader = $stmtHeader->execute([$noOrder]);
            
            if (!$resultHeader || $stmtHeader->rowCount() === 0) {
                $this->pdo->rollBack();
                return ['success' => false, 'error' => 'Work Order tidak ditemukan atau sudah diselesaikan'];
            }
            
            // 2. Update KartuOrder - set SelesaiUserID dan SelesaiTanggal
            $currentDate = date('Y-m-d H:i:s');
            $sqlKartu = "UPDATE KartuOrder 
                        SET SelesaiUserID = ?, 
                            SelesaiTanggal = ? 
                        WHERE NoOrder = ?";
            $stmtKartu = $this->pdo->prepare($sqlKartu);
            $resultKartu = $stmtKartu->execute([$userID, $currentDate, $noOrder]);
            
            // If KartuOrder doesn't exist, insert it
            if ($stmtKartu->rowCount() === 0) {
                $sqlInsertKartu = "INSERT INTO KartuOrder (NoOrder, SelesaiUserID, SelesaiTanggal) 
                                  VALUES (?, ?, ?)";
                $stmtInsertKartu = $this->pdo->prepare($sqlInsertKartu);
                $stmtInsertKartu->execute([$noOrder, $userID, $currentDate]);
            }
            
            // Commit transaction
            $this->pdo->commit();
            
            return ['success' => true];
            
        } catch (PDOException $e) {
            // Rollback on error
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['success' => false, 'error' => 'Gagal menyelesaikan Work Order'];
        }
    }
    
    /**
     * Batal Work Order - Update StatusOrder di HeaderOrder, update KartuOrder, dan delete StokOrder
     */
    public function batalWorkOrder($noOrder, $userID) {
        try {
            // Start transaction
            $this->pdo->beginTransaction();
            
            // 1. Update HeaderOrder - set StatusOrder = 5 (Batal)
            $sqlHeader = "UPDATE HeaderOrder SET StatusOrder = 5 WHERE NoOrder = ?";
            $stmtHeader = $this->pdo->prepare($sqlHeader);
            $resultHeader = $stmtHeader->execute([$noOrder]);
            
            if (!$resultHeader || $stmtHeader->rowCount() === 0) {
                $this->pdo->rollBack();
                return ['success' => false, 'error' => 'Work Order tidak ditemukan atau sudah dibatalkan'];
            }
            
            // 2. Delete StokOrder - remove all stock records for this work order
            $sqlStok = "DELETE FROM StokOrder WHERE NoOrder = ?";
            $stmtStok = $this->pdo->prepare($sqlStok);
            $stmtStok->execute([$noOrder]);
            // Note: No need to check rowCount, it's OK if no records exist
            
            // 3. Update KartuOrder - set BatalUserID dan BatalTanggal
            $currentDate = date('Y-m-d H:i:s');
            $sqlKartu = "UPDATE KartuOrder 
                        SET BatalUserID = ?, 
                            BatalTanggal = ? 
                        WHERE NoOrder = ?";
            $stmtKartu = $this->pdo->prepare($sqlKartu);
            $resultKartu = $stmtKartu->execute([$userID, $currentDate, $noOrder]);
            
            // If KartuOrder doesn't exist, insert it
            if ($stmtKartu->rowCount() === 0) {
                $sqlInsertKartu = "INSERT INTO KartuOrder (NoOrder, BatalUserID, BatalTanggal) 
                                  VALUES (?, ?, ?)";
                $stmtInsertKartu = $this->pdo->prepare($sqlInsertKartu);
                $stmtInsertKartu->execute([$noOrder, $userID, $currentDate]);
            }
            
            // Commit transaction
            $this->pdo->commit();
            
            return ['success' => true];
            
        } catch (PDOException $e) {
            // Rollback on error
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['success' => false, 'error' => 'Gagal membatalkan Work Order'];
        }
    }
}