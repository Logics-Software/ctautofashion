-- ========================================
-- SQL SERVER OPTIMIZATION SCRIPT
-- Database: ctautofashion
-- Purpose: Mengatasi delay dan meningkatkan performance
-- ========================================

USE ctautofashion;
GO

PRINT 'Starting SQL Server Optimization...';
PRINT '';

-- ========================================
-- 1. CREATE INDEXES (PALING PENTING!)
-- ========================================
PRINT '1. Creating Indexes...';
PRINT '-----------------------------------';

-- Index untuk HeaderOrder
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_HeaderOrder_TanggalOrder')
BEGIN
    CREATE NONCLUSTERED INDEX IX_HeaderOrder_TanggalOrder 
    ON HeaderOrder(TanggalOrder DESC)
    INCLUDE (NoOrder, KodeCustomer, KodeKendaraan, TotalOrder, StatusOrder);
    PRINT '✓ Created IX_HeaderOrder_TanggalOrder';
END
ELSE
    PRINT '- IX_HeaderOrder_TanggalOrder already exists';

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_HeaderOrder_KodeCustomer')
BEGIN
    CREATE NONCLUSTERED INDEX IX_HeaderOrder_KodeCustomer 
    ON HeaderOrder(KodeCustomer)
    INCLUDE (NoOrder, TanggalOrder, TotalOrder);
    PRINT '✓ Created IX_HeaderOrder_KodeCustomer';
END
ELSE
    PRINT '- IX_HeaderOrder_KodeCustomer already exists';

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_HeaderOrder_KodeKendaraan')
BEGIN
    CREATE NONCLUSTERED INDEX IX_HeaderOrder_KodeKendaraan 
    ON HeaderOrder(KodeKendaraan)
    INCLUDE (NoOrder, TanggalOrder);
    PRINT '✓ Created IX_HeaderOrder_KodeKendaraan';
END
ELSE
    PRINT '- IX_HeaderOrder_KodeKendaraan already exists';

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_HeaderOrder_StatusOrder')
BEGIN
    CREATE NONCLUSTERED INDEX IX_HeaderOrder_StatusOrder 
    ON HeaderOrder(StatusOrder)
    INCLUDE (NoOrder, TanggalOrder, KodeCustomer);
    PRINT '✓ Created IX_HeaderOrder_StatusOrder';
END
ELSE
    PRINT '- IX_HeaderOrder_StatusOrder already exists';

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_HeaderOrder_UserID')
BEGIN
    CREATE NONCLUSTERED INDEX IX_HeaderOrder_UserID 
    ON HeaderOrder(UserID)
    INCLUDE (NoOrder, TanggalOrder, StatusOrder);
    PRINT '✓ Created IX_HeaderOrder_UserID';
END
ELSE
    PRINT '- IX_HeaderOrder_UserID already exists';

-- Index untuk DetailOrderJasa
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_DetailOrderJasa_NoOrder')
BEGIN
    CREATE NONCLUSTERED INDEX IX_DetailOrderJasa_NoOrder 
    ON DetailOrderJasa(NoOrder)
    INCLUDE (KodeJasa, NamaJasa, TotalHarga, NoUrut);
    PRINT '✓ Created IX_DetailOrderJasa_NoOrder';
END
ELSE
    PRINT '- IX_DetailOrderJasa_NoOrder already exists';

-- Index untuk DetailOrderBarang
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_DetailOrderBarang_NoOrder')
BEGIN
    CREATE NONCLUSTERED INDEX IX_DetailOrderBarang_NoOrder 
    ON DetailOrderBarang(NoOrder)
    INCLUDE (KodeBarang, NamaBarang, TotalHarga, NoUrut);
    PRINT '✓ Created IX_DetailOrderBarang_NoOrder';
END
ELSE
    PRINT '- IX_DetailOrderBarang_NoOrder already exists';

-- Index untuk FileCustomer
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_FileCustomer_Status')
BEGIN
    CREATE NONCLUSTERED INDEX IX_FileCustomer_Status 
    ON FileCustomer(Status)
    INCLUDE (KodeCustomer, NamaCustomer, NoTelepon, AlamatCustomer, Kota);
    PRINT '✓ Created IX_FileCustomer_Status';
END
ELSE
    PRINT '- IX_FileCustomer_Status already exists';

-- Index untuk FileKendaraan
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_FileKendaraan_Status')
BEGIN
    CREATE NONCLUSTERED INDEX IX_FileKendaraan_Status 
    ON FileKendaraan(Status)
    INCLUDE (KodeKendaraan, NamaKendaraan, NoPolisi, KodeMerek, KodeJenis);
    PRINT '✓ Created IX_FileKendaraan_Status';
END
ELSE
    PRINT '- IX_FileKendaraan_Status already exists';

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_FileKendaraan_KodeCustomer')
BEGIN
    CREATE NONCLUSTERED INDEX IX_FileKendaraan_KodeCustomer 
    ON FileKendaraan(KodeCustomer)
    INCLUDE (KodeKendaraan, NamaKendaraan, NoPolisi);
    PRINT '✓ Created IX_FileKendaraan_KodeCustomer';
END
ELSE
    PRINT '- IX_FileKendaraan_KodeCustomer already exists';

PRINT '';

-- ========================================
-- 2. UPDATE STATISTICS
-- ========================================
PRINT '2. Updating Statistics...';
PRINT '-----------------------------------';

UPDATE STATISTICS HeaderOrder WITH FULLSCAN;
PRINT '✓ Updated HeaderOrder statistics';

UPDATE STATISTICS DetailOrderJasa WITH FULLSCAN;
PRINT '✓ Updated DetailOrderJasa statistics';

UPDATE STATISTICS DetailOrderBarang WITH FULLSCAN;
PRINT '✓ Updated DetailOrderBarang statistics';

UPDATE STATISTICS FileCustomer WITH FULLSCAN;
PRINT '✓ Updated FileCustomer statistics';

UPDATE STATISTICS FileKendaraan WITH FULLSCAN;
PRINT '✓ Updated FileKendaraan statistics';

PRINT '';

-- ========================================
-- 3. REBUILD INDEXES
-- ========================================
PRINT '3. Rebuilding Indexes...';
PRINT '-----------------------------------';

ALTER INDEX ALL ON HeaderOrder REBUILD WITH (FILLFACTOR = 90, ONLINE = OFF);
PRINT '✓ Rebuilt HeaderOrder indexes';

ALTER INDEX ALL ON DetailOrderJasa REBUILD WITH (FILLFACTOR = 90, ONLINE = OFF);
PRINT '✓ Rebuilt DetailOrderJasa indexes';

ALTER INDEX ALL ON DetailOrderBarang REBUILD WITH (FILLFACTOR = 90, ONLINE = OFF);
PRINT '✓ Rebuilt DetailOrderBarang indexes';

PRINT '';

-- ========================================
-- 4. CLEAR CACHE (Optional)
-- ========================================
PRINT '4. Clearing Cache...';
PRINT '-----------------------------------';

-- Clear procedure cache
DBCC FREEPROCCACHE;
PRINT '✓ Procedure cache cleared';

-- Clear buffer pool (hati-hati di production!)
-- DBCC DROPCLEANBUFFERS;
-- PRINT '✓ Buffer pool cleared';

PRINT '';

-- ========================================
-- 5. VERIFICATION
-- ========================================
PRINT '5. Verification...';
PRINT '-----------------------------------';

-- Show index count
SELECT 
    OBJECT_NAME(object_id) AS TableName,
    COUNT(*) as IndexCount
FROM sys.indexes
WHERE OBJECT_NAME(object_id) IN 
    ('HeaderOrder', 'DetailOrderJasa', 'DetailOrderBarang', 
     'FileCustomer', 'FileKendaraan')
AND name IS NOT NULL
GROUP BY OBJECT_NAME(object_id)
ORDER BY TableName;

PRINT '';
PRINT '========================================';
PRINT 'OPTIMIZATION COMPLETED!';
PRINT '========================================';
PRINT '';
PRINT 'NEXT STEPS:';
PRINT '1. Update app/config/database.php (enable persistent connection)';
PRINT '2. Test aplikasi performance';
PRINT '3. Monitor query execution time';
PRINT '';
PRINT 'MAINTENANCE SCHEDULE:';
PRINT '- Run sp_updatestats: Weekly';
PRINT '- Rebuild indexes: Monthly';
PRINT '- Check fragmentation: Quarterly';
PRINT '';
GO

