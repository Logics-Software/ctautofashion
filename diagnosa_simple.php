<?php
/**
 * Script Diagnosa SQL Server - Simple Version
 */

$serverName = "127.0.0.1";
$database = "ctautofashion";
$username = "bengkel";
$password = "Logics051199";

echo "========================================\n";
echo "DIAGNOSA SQL SERVER DELAY\n";
echo "========================================\n\n";

try {
    // Test Connection Speed
    echo "1. CONNECTION SPEED TEST\n";
    echo "-----------------------------------\n";
    $startTime = microtime(true);
    
    $connectionString = "sqlsrv:server=$serverName;Database=$database;TrustServerCertificate=yes;Encrypt=no";
    $pdo = new PDO($connectionString, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $connectionTime = round((microtime(true) - $startTime) * 1000, 2);
    echo "Connection Time: {$connectionTime} ms\n";
    
    if ($connectionTime > 100) {
        echo "Status: ❌ LAMBAT! (Normal: < 50ms)\n";
        echo "Rekomendasi: Enable persistent connection\n";
    } else if ($connectionTime > 50) {
        echo "Status: ⚠️  SEDANG (Normal: < 50ms)\n";
        echo "Rekomendasi: Optimasi koneksi\n";
    } else {
        echo "Status: ✅ CEPAT\n";
    }
    echo "\n";
    
    // Test Query Speed
    echo "2. QUERY SPEED TEST\n";
    echo "-----------------------------------\n";
    
    // Test simple query
    $startTime = microtime(true);
    $stmt = $pdo->query("SELECT TOP 1 * FROM HeaderOrder ORDER BY NoOrder DESC");
    $queryTime1 = round((microtime(true) - $startTime) * 1000, 2);
    echo "Simple Query (TOP 1): {$queryTime1} ms ";
    echo ($queryTime1 < 10 ? "✅\n" : "❌ LAMBAT!\n");
    
    // Test join query
    $startTime = microtime(true);
    $sql = "SELECT TOP 10 H.*, C.NamaCustomer, K.NamaKendaraan 
            FROM HeaderOrder H
            LEFT JOIN FileCustomer C ON H.KodeCustomer = C.KodeCustomer
            LEFT JOIN FileKendaraan K ON H.KodeKendaraan = K.KodeKendaraan
            ORDER BY H.NoOrder DESC";
    $stmt = $pdo->query($sql);
    $queryTime2 = round((microtime(true) - $startTime) * 1000, 2);
    echo "Join Query (TOP 10): {$queryTime2} ms ";
    echo ($queryTime2 < 50 ? "✅\n" : "❌ LAMBAT!\n");
    
    // Test full scan query
    $startTime = microtime(true);
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM HeaderOrder");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $queryTime3 = round((microtime(true) - $startTime) * 1000, 2);
    echo "Count Query ({$result['total']} rows): {$queryTime3} ms ";
    echo ($queryTime3 < 20 ? "✅\n" : "❌ LAMBAT!\n");
    echo "\n";
    
    // Check Database Size
    echo "3. DATABASE INFO\n";
    echo "-----------------------------------\n";
    $stmt = $pdo->query("SELECT DB_NAME() as dbname");
    $db = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Database: {$db['dbname']}\n";
    
    $stmt = $pdo->query("SELECT @@VERSION as version");
    $ver = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Version: " . substr($ver['version'], 0, 80) . "...\n\n";
    
    // Check Table Counts
    echo "4. TABLE ROW COUNTS\n";
    echo "-----------------------------------\n";
    $tables = ['HeaderOrder', 'DetailOrderJasa', 'DetailOrderBarang', 
               'FileCustomer', 'FileKendaraan', 'FileJasa', 'FileBarang'];
    
    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM $table");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        $formatted = number_format($count['cnt']);
        echo "- $table: $formatted rows";
        
        if ($count['cnt'] > 10000) {
            echo " ⚠️  BANYAK DATA";
        }
        echo "\n";
    }
    echo "\n";
    
    // Check Indexes
    echo "5. INDEX CHECK\n";
    echo "-----------------------------------\n";
    $sql = "SELECT 
                OBJECT_NAME(object_id) AS TableName,
                COUNT(*) as IndexCount
            FROM sys.indexes
            WHERE OBJECT_NAME(object_id) IN 
                ('HeaderOrder', 'DetailOrderJasa', 'DetailOrderBarang')
            AND name IS NOT NULL
            GROUP BY OBJECT_NAME(object_id)";
    
    $stmt = $pdo->query($sql);
    $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($indexes) > 0) {
        foreach ($indexes as $idx) {
            echo "- {$idx['TableName']}: {$idx['IndexCount']} indexes\n";
        }
    } else {
        echo "❌ TIDAK ADA INDEX CUSTOM!\n";
        echo "Ini adalah penyebab utama delay!\n";
    }
    echo "\n";
    
    // Recommendations
    echo "========================================\n";
    echo "PENYEBAB DELAY & SOLUSI\n";
    echo "========================================\n\n";
    
    $problems = [];
    
    if ($connectionTime > 50) {
        $problems[] = "Connection lambat";
    }
    if ($queryTime2 > 50) {
        $problems[] = "Join query lambat";
    }
    if (count($indexes) == 0) {
        $problems[] = "Tidak ada index";
    }
    
    if (count($problems) > 0) {
        echo "MASALAH TERDETEKSI:\n";
        foreach ($problems as $i => $problem) {
            echo ($i + 1) . ". $problem\n";
        }
        echo "\n";
    }
    
    echo "SOLUSI RECOMMENDED:\n\n";
    
    echo "A. OPTIMASI KONEKSI DATABASE\n";
    echo "   Update app/config/database.php:\n";
    echo "   -----------------------------------\n";
    echo "   \$options = [\n";
    echo "       PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,\n";
    echo "       PDO::ATTR_PERSISTENT => true,  // Enable persistent connection\n";
    echo "       PDO::SQLSRV_ATTR_ENCODING => PDO::SQLSRV_ENCODING_UTF8\n";
    echo "   ];\n";
    echo "   \$pdo = new PDO(\$connectionString, \$username, \$password, \$options);\n\n";
    
    echo "B. CREATE INDEXES (PALING PENTING!)\n";
    echo "   Jalankan di SQL Server Management Studio:\n";
    echo "   -----------------------------------\n";
    echo "   USE ctautofashion;\n";
    echo "   GO\n\n";
    echo "   -- Index untuk HeaderOrder\n";
    echo "   CREATE NONCLUSTERED INDEX IX_HeaderOrder_TanggalOrder \n";
    echo "   ON HeaderOrder(TanggalOrder DESC);\n\n";
    echo "   CREATE NONCLUSTERED INDEX IX_HeaderOrder_KodeCustomer \n";
    echo "   ON HeaderOrder(KodeCustomer);\n\n";
    echo "   CREATE NONCLUSTERED INDEX IX_HeaderOrder_StatusOrder \n";
    echo "   ON HeaderOrder(StatusOrder);\n\n";
    echo "   -- Index untuk Detail\n";
    echo "   CREATE NONCLUSTERED INDEX IX_DetailOrderJasa_NoOrder \n";
    echo "   ON DetailOrderJasa(NoOrder);\n\n";
    echo "   CREATE NONCLUSTERED INDEX IX_DetailOrderBarang_NoOrder \n";
    echo "   ON DetailOrderBarang(NoOrder);\n\n";
    
    echo "C. MAINTENANCE RUTIN\n";
    echo "   Jalankan setiap bulan:\n";
    echo "   -----------------------------------\n";
    echo "   -- Update Statistics\n";
    echo "   EXEC sp_updatestats;\n\n";
    echo "   -- Rebuild Indexes\n";
    echo "   ALTER INDEX ALL ON HeaderOrder REBUILD;\n";
    echo "   ALTER INDEX ALL ON DetailOrderJasa REBUILD;\n";
    echo "   ALTER INDEX ALL ON DetailOrderBarang REBUILD;\n\n";
    
    echo "D. QUICK FIX (SEKARANG)\n";
    echo "   Jalankan ini untuk hasil instant:\n";
    echo "   -----------------------------------\n";
    echo "   EXEC sp_updatestats;\n";
    echo "   DBCC FREEPROCCACHE;\n\n";
    
    echo "========================================\n";
    echo "ESTIMATED IMPROVEMENT:\n";
    echo "========================================\n";
    echo "Setelah optimasi:\n";
    echo "- Connection: 20-30ms (dari {$connectionTime}ms)\n";
    echo "- Join Query: 10-20ms (dari {$queryTime2}ms)\n";
    echo "- Overall: 3-5x lebih cepat\n\n";
    
} catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n\n";
    echo "TROUBLESHOOTING:\n";
    echo "1. Pastikan SQL Server service running\n";
    echo "2. Check username/password di database.php\n";
    echo "3. Pastikan TCP/IP enabled\n";
    echo "4. Check firewall (port 1433)\n";
}

echo "========================================\n";
echo "DIAGNOSA SELESAI\n";
echo "========================================\n";
?>

