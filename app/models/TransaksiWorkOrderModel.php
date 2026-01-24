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
     * Get vehicle by KodeCustomer
     */
    public function getVehicleByCustomerCode($kodeCustomer) {
        try {
            $sql = "SELECT K.KodeKendaraan, K.NamaKendaraan, K.NoPolisi, K.Tipe, 
                           K.Tahun, K.Warna, K.KodeMerek, K.KodeJenis, K.KodeCustomer,
                           M.NamaMerek, J.NamaJenis
                    FROM FileKendaraan K
                    LEFT JOIN TabelMerekKendaraan M ON K.KodeMerek = M.KodeMerek
                    LEFT JOIN TabelJenisKendaraan J ON K.KodeJenis = J.KodeJenis
                    WHERE K.KodeCustomer = ? AND K.Status = 1";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$kodeCustomer]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error getting vehicle by customer code: " . $e->getMessage());
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
     * Get default picker from TipeUser table based on UserID
     * For TipeUser = 1, return KodePicker if exists
     */
    public function getDefaultPickerByUser($userID) {
        try {
            $sql = "SELECT tu.KodePicker, tu.TipeUser, fp.NamaPicker
                    FROM TipeUser tu
                    LEFT JOIN FilePicker fp ON tu.KodePicker = fp.KodePicker
                    WHERE tu.UserID = ? AND tu.TipeUser = 1 AND tu.KodePicker IS NOT NULL AND tu.KodePicker != ''";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userID]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && !empty($result['KodePicker'])) {
                // Get full picker details
                return $this->getPickerByCode($result['KodePicker']);
            }
            
            return null;
            
        } catch (PDOException $e) {
            error_log("Error getting default picker by user: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get Kota List from TabelKota (only active cities)
     */
    public function getKotaList() {
        try {
            $sql = "SELECT Kota FROM TabelKota WHERE Status = 1 ORDER BY Kota";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting kota list: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Generate new KodeCustomer (CSXXXXXX format)
     */
    public function generateKodeCustomer() {
        try {
            $sql = "SELECT TOP 1 KodeCustomer FROM FileCustomer 
                    WHERE KodeCustomer LIKE 'CS%' 
                    ORDER BY KodeCustomer DESC";
            $stmt = $this->pdo->query($sql);
            $lastCode = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($lastCode) {
                // Extract number from CSXXXXXX
                $lastNumber = intval(substr($lastCode['KodeCustomer'], 2));
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            
            // Format: CSXXXXXX (6 digits)
            return 'CS' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
            
        } catch (PDOException $e) {
            error_log("Error generating KodeCustomer: " . $e->getMessage());
            return 'CS000001';
        }
    }
    
    /**
     * Save new customer (with transaction for FileCustomer and FileJenisCustomer)
     */
    public function saveNewCustomer($data) {
        try {
            // Start transaction
            $this->pdo->beginTransaction();
            
            // Generate KodeCustomer
            $kodeCustomer = $this->generateKodeCustomer();
            
            // Insert into FileCustomer (sesuai struktur table asli)
            $sql = "INSERT INTO FileCustomer (
                        KodeCustomer, NamaCustomer, AlamatCustomer, Kota, 
                        NoTelepon, KontakPerson, 
                        PKP, NPWP, NamaWP, AlamatWP, 
                        KodeTransaksi, KodeTermin, KodeKendaraan, 
                        SaldoPiutang, UserID, Status, NIK
                    ) VALUES (
                        ?, ?, ?, ?, 
                        ?, ?, 
                        0, '', '', '', 
                        '', '', '', 
                        0, ?, 1, ''
                    )";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $kodeCustomer,
                $data['NamaCustomer'],
                $data['AlamatCustomer'],
                $data['Kota'],
                $data['NoTelepon'],
                $data['PIC'] ?? '',  // PIC dari form akan masuk ke KontakPerson
                $_SESSION['user_id'] ?? 'SYSTEM'
            ]);
            
            // Insert into FileJenisCustomer
            $sqlJenis = "INSERT INTO FileJenisCustomer (KodeCustomer, JenisCustomer) VALUES (?, ?)";
            $stmtJenis = $this->pdo->prepare($sqlJenis);
            $stmtJenis->execute([
                $kodeCustomer,
                $data['JenisCustomer']
            ]);
            
            // Commit transaction
            $this->pdo->commit();
            
            return [
                'success' => true,
                'kodeCustomer' => $kodeCustomer,
                'namaCustomer' => $data['NamaCustomer']
            ];
            
        } catch (PDOException $e) {
            // Rollback on error
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log("Error saving new customer: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get Merek List from TabelMerekKendaraan
     */
    public function getMerekList() {
        try {
            $sql = "SELECT KodeMerek, NamaMerek FROM TabelMerekKendaraan WHERE Status = 1 ORDER BY NamaMerek";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting merek list: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get Model (Jenis) List from TabelJenisKendaraan
     */
    public function getModelList() {
        try {
            $sql = "SELECT KodeJenis, NamaJenis FROM TabelJenisKendaraan WHERE Status = 1 ORDER BY NamaJenis";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting model list: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Generate new KodeKendaraan (KDRXXXXX format - 5 digit counter)
     */
    public function generateKodeKendaraan() {
        try {
            $sql = "SELECT TOP 1 KodeKendaraan FROM FileKendaraan 
                    WHERE KodeKendaraan LIKE 'KDR%' 
                    ORDER BY KodeKendaraan DESC";
            $stmt = $this->pdo->query($sql);
            $lastCode = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($lastCode) {
                // Extract number from KDRXXXXX
                $lastNumber = intval(substr($lastCode['KodeKendaraan'], 3));
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            
            // Format: KDRXXXXX (5 digits)
            return 'KDR' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
            
        } catch (PDOException $e) {
            error_log("Error generating KodeKendaraan: " . $e->getMessage());
            return 'KDR00001';
        }
    }
    
    /**
     * Save new vehicle to FileKendaraan
     */
    public function saveNewVehicle($data) {
        try {
            // Start transaction
            $this->pdo->beginTransaction();
            
            // Generate KodeKendaraan
            $kodeKendaraan = $this->generateKodeKendaraan();
            
            // Insert into FileKendaraan
            $sql = "INSERT INTO FileKendaraan (
                        KodeKendaraan, NamaKendaraan, KodeJenis, KodeMerek, 
                        Tipe, Warna, Tahun, Silinder, BahanBakar, 
                        KodeCustomer, NoPolisi, Status
                    ) VALUES (
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, ?, 
                        NULL, ?, 1
                    )";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $kodeKendaraan,
                $data['NamaKendaraan'],
                $data['KodeJenis'],
                $data['KodeMerek'],
                $data['Tipe'],
                $data['Warna'],
                $data['Tahun'],
                $data['Silinder'],
                $data['BahanBakar'],
                strtoupper($data['NoPolisi'])  // Convert to uppercase
            ]);
            
            // Commit transaction
            $this->pdo->commit();
            
            // Get full vehicle data
            $vehicleData = $this->getVehicleByCode($kodeKendaraan);
            
            return [
                'success' => true,
                'kodeKendaraan' => $kodeKendaraan,
                'namaKendaraan' => $data['NamaKendaraan'],
                'noPolisi' => strtoupper($data['NoPolisi']),
                'vehicleData' => $vehicleData
            ];
            
        } catch (PDOException $e) {
            // Rollback on error
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log("Error saving new vehicle: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
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
     * Get stock available for barang (StokAkhir - reserved in StokOrder)
     */
    public function getStokBarang($kodeBarang) {
        try {
            $sql = "SELECT S.StokAkhir - ISNULL((SELECT SUM(O.Jumlah) FROM StokOrder O WHERE O.KodeBarang = S.KodeBarang),0) AS StokAkhir
                    FROM StokBarang S
                    WHERE S.KodeBarang = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$kodeBarang]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Return stock as integer, default to 0 if not found
            return $result ? (int)$result['StokAkhir'] : 0;
            
        } catch (PDOException $e) {
            error_log("Error getting stok barang: " . $e->getMessage());
            return 0;
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
            
            // Insert signature into HeaderOrderKonfirmasi table
            if (!empty($data['TandaTangan'])) {
                $sqlKonfirmasi = "INSERT INTO HeaderOrderKonfirmasi 
                                 (NoOrder, TandaTanganCustomer, TanggalKonfirmasi, UserID)
                                 VALUES (?, ?, GETDATE(), ?)";
                $stmtKonfirmasi = $this->pdo->prepare($sqlKonfirmasi);
                $stmtKonfirmasi->execute([
                    $noOrder,
                    $data['TandaTangan'],
                    $_SESSION['user_id']
                ]);
            }
            
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
            
            // 6. Insert KartuOrder with default values
            $sqlKartuOrder = "INSERT INTO KartuOrder 
                             (NoOrder, UserID, ProsesUserID, ProsesTanggal, SelesaiUserID, SelesaiTanggal, BatalUserID, BatalTanggal, 
                              FakturUserID, FakturTanggal, BayarUserID, BayarTanggal)
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmtKartuOrder = $this->pdo->prepare($sqlKartuOrder);
            $stmtKartuOrder->execute([
                $noOrder,
                $_SESSION['user_id'],           // User = UserID
                '',                             // ProsesUserID = ''
                '1900-01-01',                   // ProsesTanggal = '1900-01-01'
                '',                             // SelesaiUserID = ''
                '1900-01-01',                   // SelesaiTanggal = '1900-01-01'
                '',                             // BatalUserID = ''
                '1900/01/01',                   // BatalTanggal = '01/01/1900'
                '',                             // FakturUserID = ''
                '1900/01/01',                   // FakturTanggal = '01/01/1900'
                '',                             // BayarUserID = ''
                '1900/01/01'                    // BayarTanggal = '01/01/1900'
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
                $sql .= " AND H.UserID = ?";
                $params[] = $userID;
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
            // Get header data with signature from HeaderOrderKonfirmasi
            $sqlHeader = "SELECT 
                            H.NoOrder, H.TanggalOrder, H.KodeCustomer, H.KodeKendaraan,
                            H.KodeMontir, H.KodePicker, H.Keterangan, H.KMAwal, H.KMAkhir,
                            H.TotalJasa, H.TotalBarang, H.TotalOrder, H.StatusOrder,
                            C.NamaCustomer, C.AlamatCustomer, C.Kota, C.NoTelepon,
                            K.NamaKendaraan, K.NoPolisi, K.Tahun, K.Warna,
                            M.NamaMontir,
                            P.NamaPicker,
                            HK.TandaTanganCustomer, HK.TanggalKonfirmasi
                         FROM HeaderOrder H
                         LEFT JOIN FileCustomer C ON H.KodeCustomer = C.KodeCustomer
                         LEFT JOIN FileKendaraan K ON H.KodeKendaraan = K.KodeKendaraan
                         LEFT JOIN FileMontir M ON H.KodeMontir = M.KodeMontir
                         LEFT JOIN FilePicker P ON H.KodePicker = P.KodePicker
                         LEFT JOIN HeaderOrderKonfirmasi HK ON H.NoOrder = HK.NoOrder
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
            // Get header data with signature from HeaderOrderKonfirmasi
            $sqlHeader = "SELECT 
                            H.NoOrder, H.TanggalOrder, H.KodeCustomer, H.KodeKendaraan,
                            H.KodeMontir, H.KodePicker, H.Keterangan, H.KMAwal, H.KMAkhir,
                            H.TotalJasa, H.TotalBarang, H.TotalOrder, H.StatusOrder, H.UserID,
                            C.NamaCustomer,
                            K.NamaKendaraan, K.NoPolisi,
                            M.NamaMontir,
                            P.NamaPicker,
                            HK.TandaTanganCustomer, HK.TanggalKonfirmasi
                         FROM HeaderOrder H
                         LEFT JOIN FileCustomer C ON H.KodeCustomer = C.KodeCustomer
                         LEFT JOIN FileKendaraan K ON H.KodeKendaraan = K.KodeKendaraan
                         LEFT JOIN FileMontir M ON H.KodeMontir = M.KodeMontir
                         LEFT JOIN FilePicker P ON H.KodePicker = P.KodePicker
                         LEFT JOIN HeaderOrderKonfirmasi HK ON H.NoOrder = HK.NoOrder
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
            
            // Update or insert signature in HeaderOrderKonfirmasi table
            if (!empty($data['TandaTangan'])) {
                // Check if konfirmasi already exists
                $sqlCheckKonfirmasi = "SELECT NoOrder FROM HeaderOrderKonfirmasi WHERE NoOrder = ?";
                $stmtCheckKonfirmasi = $this->pdo->prepare($sqlCheckKonfirmasi);
                $stmtCheckKonfirmasi->execute([$noOrder]);
                
                if ($stmtCheckKonfirmasi->fetch()) {
                    // Update existing konfirmasi
                    $sqlUpdateKonfirmasi = "UPDATE HeaderOrderKonfirmasi 
                                           SET TandaTanganCustomer = ?, 
                                               TanggalKonfirmasi = GETDATE(), 
                                               UserID = ?
                                           WHERE NoOrder = ?";
                    $stmtUpdateKonfirmasi = $this->pdo->prepare($sqlUpdateKonfirmasi);
                    $stmtUpdateKonfirmasi->execute([
                        $data['TandaTangan'],
                        $userID,
                        $noOrder
                    ]);
                } else {
                    // Insert new konfirmasi
                    $sqlInsertKonfirmasi = "INSERT INTO HeaderOrderKonfirmasi 
                                           (NoOrder, TandaTanganCustomer, TanggalKonfirmasi, UserID)
                                           VALUES (?, ?, GETDATE(), ?)";
                    $stmtInsertKonfirmasi = $this->pdo->prepare($sqlInsertKonfirmasi);
                    $stmtInsertKonfirmasi->execute([
                        $noOrder,
                        $data['TandaTangan'],
                        $userID
                    ]);
                }
            }
            
            // Delete existing details
            $this->pdo->prepare("DELETE FROM DetailOrderJasa WHERE NoOrder = ?")->execute([$noOrder]);
            $this->pdo->prepare("DELETE FROM DetailOrderBarang WHERE NoOrder = ?")->execute([$noOrder]);
            
            // Delete StokOrder with explicit check
            $sqlDeleteStok = "DELETE FROM StokOrder WHERE NoOrder = ?";
            $stmtDeleteStok = $this->pdo->prepare($sqlDeleteStok);
            $stmtDeleteStok->execute([$noOrder]);
            $deletedRows = $stmtDeleteStok->rowCount();
            error_log("Deleted $deletedRows rows from StokOrder for NoOrder: $noOrder");
            
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
            
            // Use MERGE statement to avoid duplicate key error in StokOrder
            $sqlStokOrder = "MERGE INTO StokOrder AS target
                            USING (SELECT ? AS NoOrder, ? AS KodeBarang, ? AS TahunProduksi, ? AS Jumlah) AS source
                            ON target.NoOrder = source.NoOrder 
                               AND target.KodeBarang = source.KodeBarang 
                               AND target.TahunProduksi = source.TahunProduksi
                            WHEN MATCHED THEN
                                UPDATE SET Jumlah = source.Jumlah
                            WHEN NOT MATCHED THEN
                                INSERT (NoOrder, KodeBarang, TahunProduksi, Jumlah)
                                VALUES (source.NoOrder, source.KodeBarang, source.TahunProduksi, source.Jumlah);";
            $stmtStokOrder = $this->pdo->prepare($sqlStokOrder);
            
            // Track processed items to prevent duplicates
            $processedItems = [];
            
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
                
                // Create unique key for this item
                $itemKey = $barang['KodeBarang'] . '-';
                
                // Only insert to StokOrder if not already processed (prevent duplicates)
                if (!isset($processedItems[$itemKey])) {
                    $processedItems[$itemKey] = $barang['Jumlah'];
                    
                    // Insert or update stok order using MERGE
                    $stmtStokOrder->execute([
                        $noOrder,
                        $barang['KodeBarang'],
                        '-',
                        $barang['Jumlah']
                    ]);
                } else {
                    // If duplicate item exists, accumulate quantity
                    $processedItems[$itemKey] += $barang['Jumlah'];
                    error_log("Duplicate item found: {$barang['KodeBarang']}, accumulating quantity");
                }
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

            // Update KartuOrder to ensure no NULL values
            $sqlKartuOrder = "UPDATE KartuOrder SET 
                                ProsesTanggal = ISNULL(ProsesTanggal, '1900-01-01'),
                                SelesaiUserID = ISNULL(SelesaiUserID, ''),
                                SelesaiTanggal = ISNULL(SelesaiTanggal, '1900-01-01')
                              WHERE NoOrder = ?";
            $this->pdo->prepare($sqlKartuOrder)->execute([$noOrder]);
            
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

    /**
     * Add work order to print queue (DataCetakOrder)
     */
    public function insertPrintQueue($noOrder) {
        try {
            // Check if entry already exists to prevent duplicates
            $sqlCheck = "SELECT COUNT(*) as count FROM DataCetakOrder WHERE NoOrder = ?";
            $stmtCheck = $this->pdo->prepare($sqlCheck);
            $stmtCheck->execute([$noOrder]);
            $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] > 0) {
                // Already exists, update status to 0
                $sqlUpdate = "UPDATE DataCetakOrder SET StatusCetak = 0 WHERE NoOrder = ?";
                $stmtUpdate = $this->pdo->prepare($sqlUpdate);
                $stmtUpdate->execute([$noOrder]);
            } else {
                // Insert new
                $sqlInsert = "INSERT INTO DataCetakOrder (NoOrder, StatusCetak) VALUES (?, 0)";
                $stmtInsert = $this->pdo->prepare($sqlInsert);
                $stmtInsert->execute([$noOrder]);
            }
            
            return true;
            
        } catch (PDOException $e) {
            error_log("Error inserting print queue: " . $e->getMessage());
            return false;
        }
    }
}
?>

