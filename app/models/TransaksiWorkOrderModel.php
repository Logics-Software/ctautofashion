<?php
class TransaksiWorkOrderModel {
    private $pdo;
    
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }
    
    /**
     * Generate NoOrder otomatis dengan format SP-YYMM?????
     */
    public function generateNoOrder() {
        try {
            $yearMonth = date('ym'); // Format YYMM
            $prefix = "SP-$yearMonth";
            
            // Cari nomor terakhir dengan prefix yang sama
            $sql = "SELECT TOP 1 NoOrder 
                    FROM HeaderOrder 
                    WHERE NoOrder LIKE ? 
                    ORDER BY NoOrder DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$prefix . '%']);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                // Ambil 5 digit terakhir dan tambah 1
                $lastNumber = (int)substr($result['NoOrder'], -5);
                $newNumber = $lastNumber + 1;
            } else {
                // Mulai dari 1 jika belum ada
                $newNumber = 1;
            }
            
            // Format dengan 5 digit (padding dengan 0)
            $noOrder = $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
            
            return $noOrder;
            
        } catch (PDOException $e) {
            error_log("Error generating NoOrder: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Search customers by KodeCustomer, NamaCustomer, or NoTelepon
     */
    public function searchCustomers($searchTerm = '') {
        try {
            $sql = "SELECT TOP 50 
                        KodeCustomer, NamaCustomer, AlamatCustomer, 
                        Kota, NoTelepon, KontakPerson, KodeKendaraan
                    FROM FileCustomer
                    WHERE Status = 1 
                      AND (KodeCustomer LIKE ? OR NamaCustomer LIKE ? OR NoTelepon LIKE ?)
                    ORDER BY NamaCustomer";
            
            $searchParam = '%' . $searchTerm . '%';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$searchParam, $searchParam, $searchParam]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error searching customers: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get customer by KodeCustomer
     */
    public function getCustomerByCode($kodeCustomer) {
        try {
            $sql = "SELECT KodeCustomer, NamaCustomer, AlamatCustomer, 
                           Kota, NoTelepon, KontakPerson, KodeKendaraan
                    FROM FileCustomer
                    WHERE KodeCustomer = ? AND Status = 1";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$kodeCustomer]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error getting customer: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Search vehicles by NamaKendaraan or NoPolisi
     */
    public function searchVehicles($searchTerm = '') {
        try {
            $sql = "SELECT TOP 50 
                        K.KodeKendaraan, K.NamaKendaraan, K.NoPolisi, K.Tipe, 
                        K.Tahun, K.Warna, K.KodeMerek, K.KodeJenis, K.KodeCustomer,
                        M.NamaMerek, J.NamaJenis
                    FROM FileKendaraan K
                    LEFT JOIN TabelMerekKendaraan M ON K.KodeMerek = M.KodeMerek
                    LEFT JOIN TabelJenisKendaraan J ON K.KodeJenis = J.KodeJenis
                    WHERE K.Status = 1 
                      AND (K.NamaKendaraan LIKE ? 
                           OR REPLACE(K.NoPolisi, ' ', '') LIKE ?)
                    ORDER BY K.NamaKendaraan";
            
            $searchParam = '%' . $searchTerm . '%';
            // Remove spaces from search term for NoPolisi matching
            $searchParamNoSpace = '%' . str_replace(' ', '', $searchTerm) . '%';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$searchParam, $searchParamNoSpace]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error searching vehicles: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get vehicle by KodeKendaraan
     */
    public function getVehicleByCode($kodeKendaraan) {
        try {
            $sql = "SELECT K.KodeKendaraan, K.NamaKendaraan, K.NoPolisi, K.Tipe, 
                           K.Tahun, K.Warna, K.KodeMerek, K.KodeJenis, K.KodeCustomer,
                           M.NamaMerek, J.NamaJenis
                    FROM FileKendaraan K
                    LEFT JOIN TabelMerekKendaraan M ON K.KodeMerek = M.KodeMerek
                    LEFT JOIN TabelJenisKendaraan J ON K.KodeJenis = J.KodeJenis
                    WHERE K.KodeKendaraan = ? AND K.Status = 1";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$kodeKendaraan]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error getting vehicle: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Search montir by KodeMontir or NamaMontir
     */
    public function searchMontir($searchTerm = '') {
        try {
            $sql = "SELECT TOP 50 
                        KodeMontir, NamaMontir, AlamatMontir, NoTelepon
                    FROM FileMontir
                    WHERE Status = 1 
                      AND (KodeMontir LIKE ? OR NamaMontir LIKE ?)
                    ORDER BY NamaMontir";
            
            $searchParam = '%' . $searchTerm . '%';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$searchParam, $searchParam]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error searching montir: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get montir by KodeMontir
     */
    public function getMontirByCode($kodeMontir) {
        try {
            $sql = "SELECT KodeMontir, NamaMontir, AlamatMontir, NoTelepon
                    FROM FileMontir
                    WHERE KodeMontir = ? AND Status = 1";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$kodeMontir]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error getting montir: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Search picker by KodePicker or NamaPicker
     */
    public function searchPicker($searchTerm = '') {
        try {
            $sql = "SELECT TOP 50 
                        KodePicker, NamaPicker, AlamatPicker, NoTelepon
                    FROM FilePicker
                    WHERE Status = 1 
                      AND (KodePicker LIKE ? OR NamaPicker LIKE ?)
                    ORDER BY NamaPicker";
            
            $searchParam = '%' . $searchTerm . '%';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$searchParam, $searchParam]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error searching picker: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get picker by KodePicker
     */
    public function getPickerByCode($kodePicker) {
        try {
            $sql = "SELECT KodePicker, NamaPicker, AlamatPicker, NoTelepon
                    FROM FilePicker
                    WHERE KodePicker = ? AND Status = 1";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$kodePicker]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error getting picker: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Search jasa by KodeJasa, NamaJasa, KodeKategori, or NamaKategori
     */
    public function searchJasa($searchTerm = '') {
        try {
            $sql = "SELECT TOP 50 
                        FJ.KodeJasa, FJ.NamaJasa, FJ.Satuan, FJ.KodeJenis,
                        TK.KodeKategori, TK.NamaKategori, 
                        FT.Harga, FT.Discount
                    FROM FileJasa FJ
                    LEFT JOIN FileTarif FT ON FJ.KodeJasa = FT.KodeJasa
                    LEFT JOIN TabelKategoriJasa TK ON FT.KodeKategori = TK.KodeKategori
                    WHERE FJ.Status = 1 
                      AND TK.Standart = 1
                      AND (FJ.KodeJasa LIKE ? 
                           OR FJ.NamaJasa LIKE ? 
                           OR TK.KodeKategori LIKE ? 
                           OR TK.NamaKategori LIKE ?)
                    ORDER BY FJ.NamaJasa";
            
            $searchParam = '%' . $searchTerm . '%';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$searchParam, $searchParam, $searchParam, $searchParam]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error searching jasa: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get jasa by KodeJasa with standard tariff
     */
    public function getJasaByCode($kodeJasa) {
        try {
            $sql = "SELECT FJ.KodeJasa, FJ.NamaJasa, FJ.Satuan, FJ.KodeJenis,
                           TK.KodeKategori, TK.NamaKategori, 
                           FT.Harga, FT.Discount
                    FROM FileJasa FJ
                    LEFT JOIN FileTarif FT ON FJ.KodeJasa = FT.KodeJasa
                    LEFT JOIN TabelKategoriJasa TK ON FT.KodeKategori = TK.KodeKategori
                    WHERE FJ.KodeJasa = ? 
                      AND FJ.Status = 1 
                      AND TK.Standart = 1";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$kodeJasa]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error getting jasa: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Search barang by KodeBarang, NamaBarang, Merek, or Jenis
     */
    public function searchBarang($searchTerm = '') {
        try {
            $sql = "SELECT TOP 50 
                        B.KodeBarang, B.NamaBarang, B.Satuan, B.HargaJual,
                        B.DiscountJual, B.KodeMerek, B.KodeJenis,
                        M.NamaMerek, J.NamaJenis
                    FROM FileBarang B
                    LEFT JOIN TabelMerek M ON B.KodeMerek = M.KodeMerek
                    LEFT JOIN TabelJenis J ON B.KodeJenis = J.KodeJenis
                    WHERE B.Status = 1 
                      AND (B.KodeBarang LIKE ? 
                           OR B.NamaBarang LIKE ? 
                           OR M.NamaMerek LIKE ? 
                           OR J.NamaJenis LIKE ?)
                    ORDER BY B.NamaBarang";
            
            $searchParam = '%' . $searchTerm . '%';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$searchParam, $searchParam, $searchParam, $searchParam]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error searching barang: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get barang by KodeBarang
     */
    public function getBarangByCode($kodeBarang) {
        try {
            $sql = "SELECT B.KodeBarang, B.NamaBarang, B.Satuan, B.HargaJual,
                           B.DiscountJual, B.KodeMerek, B.KodeJenis,
                           M.NamaMerek, J.NamaJenis
                    FROM FileBarang B
                    LEFT JOIN TabelMerek M ON B.KodeMerek = M.KodeMerek
                    LEFT JOIN TabelJenis J ON B.KodeJenis = J.KodeJenis
                    WHERE B.KodeBarang = ? AND B.Status = 1";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$kodeBarang]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error getting barang: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Save work order (Header, Detail Jasa, Detail Barang)
     */
    public function saveWorkOrder($data) {
        try {
            // Start transaction
            $this->pdo->beginTransaction();
            
            // Generate NoOrder
            $noOrder = $this->generateNoOrder();
            if (!$noOrder) {
                throw new Exception("Gagal generate NoOrder");
            }
            
            // 1. Insert HeaderOrder
            $sqlHeader = "INSERT INTO HeaderOrder 
                         (NoOrder, TanggalOrder, KodeCustomer, KodeKendaraan, KodeMontir, 
                          KodePicker, Keterangan, KMAwal, KMAkhir, TotalJasa, TotalBarang, 
                          TotalOrder, StatusOrder, UserID)
                         VALUES (?, GETDATE(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?)";
            
            $stmtHeader = $this->pdo->prepare($sqlHeader);
            $stmtHeader->execute([
                $noOrder,
                $data['KodeCustomer'],
                $data['KodeKendaraan'],
                $data['KodeMontir'],
                $data['KodePicker'],
                $data['Keterangan'] ?? '',
                $data['KMAwal'] ?? 0,
                $data['KMAkhir'] ?? 0,
                $data['TotalJasa'] ?? 0,
                $data['TotalBarang'] ?? 0,
                $data['TotalOrder'] ?? 0,
                $_SESSION['user_id']
            ]);
            
            // 2. Insert DetailOrderJasa
            if (!empty($data['DetailJasa'])) {
                $sqlJasa = "INSERT INTO DetailOrderJasa 
                           (NoOrder, KodeJasa, NamaJasa, KodeKategori, Satuan, Jumlah, 
                            HargaSatuan, Discount, DiscountRupiah, TotalHarga, NoUrut)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmtJasa = $this->pdo->prepare($sqlJasa);
                
                foreach ($data['DetailJasa'] as $index => $jasa) {
                    $stmtJasa->execute([
                        $noOrder,
                        $jasa['KodeJasa'],
                        $jasa['NamaJasa'],
                        $jasa['KodeKategori'],
                        $jasa['Satuan'],
                        $jasa['Jumlah'],
                        $jasa['HargaSatuan'],
                        $jasa['Discount'] ?? 0,
                        $jasa['DiscountRupiah'] ?? 0,
                        $jasa['TotalHarga'],
                        $index + 1
                    ]);
                }
            }
            
            // 3. Insert DetailOrderBarang
            if (!empty($data['DetailBarang'])) {
                $sqlBarang = "INSERT INTO DetailOrderBarang 
                             (NoOrder, KodeBarang, NamaBarang, Satuan, TahunProduksi, Jumlah, 
                              HargaSatuan, Discount, DiscountRupiah, TotalHarga, NoUrut)
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmtBarang = $this->pdo->prepare($sqlBarang);
                
                // Prepare statement for StokOrder
                $sqlStokOrder = "INSERT INTO StokOrder 
                                (NoOrder, KodeBarang, TahunProduksi, Jumlah)
                                VALUES (?, ?, ?, ?)";
                $stmtStokOrder = $this->pdo->prepare($sqlStokOrder);
                
                foreach ($data['DetailBarang'] as $index => $barang) {
                    // Insert DetailOrderBarang
                    $stmtBarang->execute([
                        $noOrder,
                        $barang['KodeBarang'],
                        $barang['NamaBarang'],
                        $barang['Satuan'],
                        $barang['TahunProduksi'] ?? '-',
                        $barang['Jumlah'],
                        $barang['HargaSatuan'],
                        $barang['Discount'] ?? 0,
                        $barang['DiscountRupiah'] ?? 0,
                        $barang['TotalHarga'],
                        $index + 1
                    ]);
                    
                    // Insert StokOrder
                    $stmtStokOrder->execute([
                        $noOrder,
                        $barang['KodeBarang'],
                        '-', // TahunProduksi
                        $barang['Jumlah']
                    ]);
                }
            }
            
            // 4. Update FileKendaraan - set KodeCustomer
            $sqlUpdateKendaraan = "UPDATE FileKendaraan 
                                  SET KodeCustomer = ? 
                                  WHERE KodeKendaraan = ?";
            $stmtUpdateKendaraan = $this->pdo->prepare($sqlUpdateKendaraan);
            $stmtUpdateKendaraan->execute([
                $data['KodeCustomer'],
                $data['KodeKendaraan']
            ]);
            
            // 5. Update FileCustomer - set KodeKendaraan
            $sqlUpdateCustomer = "UPDATE FileCustomer 
                                 SET KodeKendaraan = ? 
                                 WHERE KodeCustomer = ?";
            $stmtUpdateCustomer = $this->pdo->prepare($sqlUpdateCustomer);
            $stmtUpdateCustomer->execute([
                $data['KodeKendaraan'],
                $data['KodeCustomer']
            ]);
            
            // Commit transaction
            $this->pdo->commit();
            
            return [
                'success' => true,
                'NoOrder' => $noOrder
            ];
            
        } catch (Exception $e) {
            // Rollback on error
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log("Error saving work order: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get work order list with pagination
     */
    public function getWorkOrders($filters, $limit, $offset, $userID = null, $tipeUser = null) {
        try {
            // Debug logging
            error_log("getWorkOrders - UserID: " . ($userID ?? 'null') . ", TipeUser: " . ($tipeUser ?? 'null'));
            
            $sql = "SELECT * FROM (
                        SELECT ROW_NUMBER() OVER (ORDER BY H.NoOrder DESC) as RowNum,
                               H.NoOrder, H.TanggalOrder, H.StatusOrder, 
                               H.KodeCustomer, H.KodeKendaraan, H.TotalOrder, H.UserID,
                               C.NamaCustomer, K.NoPolisi, K.NamaKendaraan,
                               M.NamaMontir, P.NamaPicker
                        FROM HeaderOrder H
                        LEFT JOIN FileCustomer C ON H.KodeCustomer = C.KodeCustomer
                        LEFT JOIN FileKendaraan K ON H.KodeKendaraan = K.KodeKendaraan
                        LEFT JOIN FileMontir M ON H.KodeMontir = M.KodeMontir
                        LEFT JOIN FilePicker P ON H.KodePicker = P.KodePicker
                        WHERE 1=1";
            
            $params = [];
            
            // Filter berdasarkan UserID jika TipeUser < 2
            if ($tipeUser !== null && $tipeUser < 2 && $userID !== null) {
                error_log("Applying UserID filter: TipeUser={$tipeUser}, UserID={$userID}");
                $sql .= " AND H.UserID = ?";
                $params[] = $userID;
            } else {
                error_log("NOT applying UserID filter - TipeUser: " . ($tipeUser ?? 'null') . " >= 2 or UserID is null");
            }
            
            // Search filter
            if (!empty($filters['search'])) {
                $sql .= " AND (H.NoOrder LIKE ? OR C.NamaCustomer LIKE ? OR REPLACE(K.NoPolisi, ' ', '') LIKE ?)";
                $searchTerm = '%' . $filters['search'] . '%';
                // Remove spaces from search term for NoPolisi matching
                $searchTermNoSpace = '%' . str_replace(' ', '', $filters['search']) . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTermNoSpace;
            }
            
            // Date filter
            if (!empty($filters['date'])) {
                $sql .= " AND CAST(H.TanggalOrder AS DATE) = ?";
                $params[] = $filters['date'];
            }
            
            $sql .= ") AS PaginatedResults WHERE RowNum > ? AND RowNum <= ?";
            $params[] = $offset;
            $params[] = $offset + $limit;
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error getting work orders: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get total work orders count
     */
    public function getTotalWorkOrders($filters, $userID = null, $tipeUser = null) {
        try {
            $sql = "SELECT COUNT(*) as total 
                    FROM HeaderOrder H
                    LEFT JOIN FileCustomer C ON H.KodeCustomer = C.KodeCustomer
                    LEFT JOIN FileKendaraan K ON H.KodeKendaraan = K.KodeKendaraan
                    WHERE 1=1";
            
            $params = [];
            
            // Filter berdasarkan UserID jika TipeUser < 2
            if ($tipeUser !== null && $tipeUser < 2 && $userID !== null) {
                $sql .= " AND H.UserID = ?";
                $params[] = $userID;
            }
            
            if (!empty($filters['search'])) {
                $sql .= " AND (H.NoOrder LIKE ? OR C.NamaCustomer LIKE ? OR REPLACE(K.NoPolisi, ' ', '') LIKE ?)";
                $searchTerm = '%' . $filters['search'] . '%';
                // Remove spaces from search term for NoPolisi matching
                $searchTermNoSpace = '%' . str_replace(' ', '', $filters['search']) . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTermNoSpace;
            }
            
            if (!empty($filters['date'])) {
                $sql .= " AND CAST(H.TanggalOrder AS DATE) = ?";
                $params[] = $filters['date'];
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
            
        } catch (PDOException $e) {
            error_log("Error getting total work orders: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get work order detail by NoOrder
     */
    public function getWorkOrderDetail($noOrder) {
        try {
            // Get header data
            $sqlHeader = "SELECT 
                            H.NoOrder, H.TanggalOrder, H.KodeCustomer, H.KodeKendaraan,
                            H.KodeMontir, H.KodePicker, H.Keterangan, H.KMAwal, H.KMAkhir,
                            H.TotalJasa, H.TotalBarang, H.TotalOrder, H.StatusOrder,
                            C.NamaCustomer, C.AlamatCustomer, C.Kota, C.NoTelepon,
                            K.NamaKendaraan, K.NoPolisi, K.Tahun, K.Warna,
                            M.NamaMontir,
                            P.NamaPicker
                         FROM HeaderOrder H
                         LEFT JOIN FileCustomer C ON H.KodeCustomer = C.KodeCustomer
                         LEFT JOIN FileKendaraan K ON H.KodeKendaraan = K.KodeKendaraan
                         LEFT JOIN FileMontir M ON H.KodeMontir = M.KodeMontir
                         LEFT JOIN FilePicker P ON H.KodePicker = P.KodePicker
                         WHERE H.NoOrder = ?";
            
            $stmtHeader = $this->pdo->prepare($sqlHeader);
            $stmtHeader->execute([$noOrder]);
            $header = $stmtHeader->fetch(PDO::FETCH_ASSOC);
            
            if (!$header) {
                return ['error' => 'Work Order tidak ditemukan'];
            }
            
            // Get detail jasa
            $sqlJasa = "SELECT 
                            DOJ.KodeJasa, DOJ.NamaJasa, DOJ.KodeKategori, DOJ.Satuan,
                            DOJ.Jumlah, DOJ.HargaSatuan, DOJ.Discount, DOJ.DiscountRupiah,
                            DOJ.TotalHarga, DOJ.NoUrut,
                            TK.NamaKategori
                        FROM DetailOrderJasa DOJ
                        LEFT JOIN TabelKategoriJasa TK ON DOJ.KodeKategori = TK.KodeKategori
                        WHERE DOJ.NoOrder = ?
                        ORDER BY DOJ.NoUrut";
            
            $stmtJasa = $this->pdo->prepare($sqlJasa);
            $stmtJasa->execute([$noOrder]);
            $jasa = $stmtJasa->fetchAll(PDO::FETCH_ASSOC);
            
            // Get detail barang
            $sqlBarang = "SELECT 
                            DOB.KodeBarang, DOB.NamaBarang, DOB.Satuan, DOB.TahunProduksi,
                            DOB.Jumlah, DOB.HargaSatuan, DOB.Discount, DOB.DiscountRupiah,
                            DOB.TotalHarga, DOB.NoUrut,
                            B.KodeMerek,
                            M.NamaMerek
                          FROM DetailOrderBarang DOB
                          LEFT JOIN FileBarang B ON DOB.KodeBarang = B.KodeBarang
                          LEFT JOIN TabelMerek M ON B.KodeMerek = M.KodeMerek
                          WHERE DOB.NoOrder = ?
                          ORDER BY DOB.NoUrut";
            
            $stmtBarang = $this->pdo->prepare($sqlBarang);
            $stmtBarang->execute([$noOrder]);
            $barang = $stmtBarang->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'header' => $header,
                'jasa' => $jasa,
                'barang' => $barang
            ];
            
        } catch (PDOException $e) {
            error_log("Error getting work order detail: " . $e->getMessage());
            return ['error' => 'Terjadi kesalahan saat mengambil data'];
        }
    }
    
    /**
     * Get work order data for editing
     */
    public function getWorkOrderForEdit($noOrder) {
        try {
            // Get header data
            $sqlHeader = "SELECT 
                            H.NoOrder, H.TanggalOrder, H.KodeCustomer, H.KodeKendaraan,
                            H.KodeMontir, H.KodePicker, H.Keterangan, H.KMAwal, H.KMAkhir,
                            H.TotalJasa, H.TotalBarang, H.TotalOrder, H.StatusOrder, H.UserID,
                            C.NamaCustomer,
                            K.NamaKendaraan, K.NoPolisi,
                            M.NamaMontir,
                            P.NamaPicker
                         FROM HeaderOrder H
                         LEFT JOIN FileCustomer C ON H.KodeCustomer = C.KodeCustomer
                         LEFT JOIN FileKendaraan K ON H.KodeKendaraan = K.KodeKendaraan
                         LEFT JOIN FileMontir M ON H.KodeMontir = M.KodeMontir
                         LEFT JOIN FilePicker P ON H.KodePicker = P.KodePicker
                         WHERE H.NoOrder = ?";
            
            $stmtHeader = $this->pdo->prepare($sqlHeader);
            $stmtHeader->execute([$noOrder]);
            $header = $stmtHeader->fetch(PDO::FETCH_ASSOC);
            
            if (!$header) {
                return ['error' => 'Work Order tidak ditemukan'];
            }
            
            // Get detail jasa
            $sqlJasa = "SELECT 
                            DOJ.KodeJasa, DOJ.NamaJasa, DOJ.KodeKategori, DOJ.Satuan,
                            DOJ.Jumlah, DOJ.HargaSatuan, DOJ.Discount, DOJ.DiscountRupiah,
                            DOJ.TotalHarga, DOJ.NoUrut,
                            TK.NamaKategori
                        FROM DetailOrderJasa DOJ
                        LEFT JOIN TabelKategoriJasa TK ON DOJ.KodeKategori = TK.KodeKategori
                        WHERE DOJ.NoOrder = ?
                        ORDER BY DOJ.NoUrut";
            
            $stmtJasa = $this->pdo->prepare($sqlJasa);
            $stmtJasa->execute([$noOrder]);
            $jasa = $stmtJasa->fetchAll(PDO::FETCH_ASSOC);
            
            // Get detail barang
            $sqlBarang = "SELECT 
                            DOB.KodeBarang, DOB.NamaBarang, DOB.Satuan, DOB.TahunProduksi,
                            DOB.Jumlah, DOB.HargaSatuan, DOB.Discount, DOB.DiscountRupiah,
                            DOB.TotalHarga, DOB.NoUrut,
                            B.KodeMerek, B.KodeJenis,
                            M.NamaMerek,
                            J.NamaJenis
                          FROM DetailOrderBarang DOB
                          LEFT JOIN FileBarang B ON DOB.KodeBarang = B.KodeBarang
                          LEFT JOIN TabelMerek M ON B.KodeMerek = M.KodeMerek
                          LEFT JOIN TabelJenis J ON B.KodeJenis = J.KodeJenis
                          WHERE DOB.NoOrder = ?
                          ORDER BY DOB.NoUrut";
            
            $stmtBarang = $this->pdo->prepare($sqlBarang);
            $stmtBarang->execute([$noOrder]);
            $barang = $stmtBarang->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'header' => $header,
                'jasa' => $jasa,
                'barang' => $barang
            ];
            
        } catch (PDOException $e) {
            error_log("Error getting work order for edit: " . $e->getMessage());
            return ['error' => 'Terjadi kesalahan saat mengambil data'];
        }
    }
    
    /**
     * Update work order
     */
    public function updateWorkOrder($noOrder, $data, $userID) {
        try {
            $this->pdo->beginTransaction();
            
            // Update header
            $sqlHeader = "UPDATE HeaderOrder SET
                            KodeCustomer = ?,
                            KodeKendaraan = ?,
                            KodeMontir = ?,
                            KodePicker = ?,
                            Keterangan = ?,
                            KMAwal = ?,
                            KMAkhir = ?,
                            TotalJasa = ?,
                            TotalBarang = ?,
                            TotalOrder = ?
                          WHERE NoOrder = ?";
            
            $stmtHeader = $this->pdo->prepare($sqlHeader);
            $stmtHeader->execute([
                $data['KodeCustomer'],
                $data['KodeKendaraan'],
                $data['KodeMontir'],
                $data['KodePicker'],
                $data['Keterangan'],
                $data['KMAwal'],
                $data['KMAkhir'],
                $data['TotalJasa'],
                $data['TotalBarang'],
                $data['TotalOrder'],
                $noOrder
            ]);
            
            // Delete existing details
            $this->pdo->prepare("DELETE FROM DetailOrderJasa WHERE NoOrder = ?")->execute([$noOrder]);
            $this->pdo->prepare("DELETE FROM DetailOrderBarang WHERE NoOrder = ?")->execute([$noOrder]);
            $this->pdo->prepare("DELETE FROM StokOrder WHERE NoOrder = ?")->execute([$noOrder]);
            
            // Insert detail jasa
            $sqlJasa = "INSERT INTO DetailOrderJasa 
                        (NoOrder, KodeJasa, NamaJasa, Satuan, KodeKategori, Jumlah, HargaSatuan, Discount, DiscountRupiah, TotalHarga, NoUrut)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtJasa = $this->pdo->prepare($sqlJasa);
            
            foreach ($data['DetailJasa'] as $index => $jasa) {
                $discountRupiah = ($jasa['HargaSatuan'] * $jasa['Jumlah']) * ($jasa['Discount'] / 100);
                $stmtJasa->execute([
                    $noOrder,
                    $jasa['KodeJasa'],
                    $jasa['NamaJasa'],
                    $jasa['Satuan'],
                    $jasa['KodeKategori'],
                    $jasa['Jumlah'],
                    $jasa['HargaSatuan'],
                    $jasa['Discount'],
                    $discountRupiah,
                    $jasa['TotalHarga'],
                    $index + 1
                ]);
            }
            
            // Insert detail barang & stok order
            $sqlBarang = "INSERT INTO DetailOrderBarang 
                          (NoOrder, KodeBarang, NamaBarang, Satuan, TahunProduksi, Jumlah, HargaSatuan, Discount, DiscountRupiah, TotalHarga, NoUrut)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtBarang = $this->pdo->prepare($sqlBarang);
            
            $sqlStokOrder = "INSERT INTO StokOrder 
                            (NoOrder, KodeBarang, TahunProduksi, Jumlah)
                            VALUES (?, ?, ?, ?)";
            $stmtStokOrder = $this->pdo->prepare($sqlStokOrder);
            
            foreach ($data['DetailBarang'] as $index => $barang) {
                $discountRupiah = ($barang['HargaSatuan'] * $barang['Jumlah']) * ($barang['Discount'] / 100);
                
                // Insert detail barang
                $stmtBarang->execute([
                    $noOrder,
                    $barang['KodeBarang'],
                    $barang['NamaBarang'],
                    $barang['Satuan'],
                    '-',
                    $barang['Jumlah'],
                    $barang['HargaSatuan'],
                    $barang['Discount'],
                    $discountRupiah,
                    $barang['TotalHarga'],
                    $index + 1
                ]);
                
                // Insert stok order
                $stmtStokOrder->execute([
                    $noOrder,
                    $barang['KodeBarang'],
                    '-',
                    $barang['Jumlah']
                ]);
            }
            
            // Update FileKendaraan - KodeCustomer
            $sqlUpdateKendaraan = "UPDATE FileKendaraan SET KodeCustomer = ? WHERE KodeKendaraan = ?";
            $this->pdo->prepare($sqlUpdateKendaraan)->execute([
                $data['KodeCustomer'],
                $data['KodeKendaraan']
            ]);
            
            // Update FileCustomer - KodeKendaraan
            $sqlUpdateCustomer = "UPDATE FileCustomer SET KodeKendaraan = ? WHERE KodeCustomer = ?";
            $this->pdo->prepare($sqlUpdateCustomer)->execute([
                $data['KodeKendaraan'],
                $data['KodeCustomer']
            ]);
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'NoOrder' => $noOrder,
                'message' => 'Work Order berhasil diupdate'
            ];
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error updating work order: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Gagal mengupdate work order: ' . $e->getMessage()
            ];
        }
    }
}
?>

