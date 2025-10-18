<?php
/**
 * Script Diagnosa dan Optimasi SQL Server
 * Untuk mendeteksi penyebab delay dan memberikan rekomendasi
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "========================================\n";
echo "DIAGNOSA SQL SERVER PERFORMANCE\n";
echo "========================================\n\n";

// Database configuration
$serverName = "127.0.0.1";
$database = "ctautofashion";
$username = "bengkel";
$password = "Logics051199";

try {
    // Test 1: Connection Time
    echo "Test 1: Connection Speed\n";
    echo "-----------------------------------\n";
    $startTime = microtime(true);
    
    $connectionString = "sqlsrv:server=$serverName;Database=$database;TrustServerCertificate=yes;Encrypt=no";
    $pdo = new PDO($connectionString, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $connectionTime = round((microtime(true) - $startTime) * 1000, 2);
    echo "Connection Time: {$connectionTime} ms\n";
    
    if ($connectionTime > 100) {
        echo "❌ LAMBAT! (Normal: < 50ms)\n";
    } else if ($connectionTime > 50) {
        echo "⚠️  SEDANG (Normal: < 50ms)\n";
    } else {
        echo "✅ CEPAT\n";
    }
    echo "\n";
    
    // Test 2: Simple Query Performance
    echo "Test 2: Simple Query Performance\n";
    echo "-----------------------------------\n";
    $startTime = microtime(true);
    $stmt = $pdo->query("SELECT @@VERSION as version");
    $queryTime = round((microtime(true) - $startTime) * 1000, 2);
    echo "Query Time: {$queryTime} ms\n";
    echo "✅ Query executed\n\n";
    
    // Test 3: Database Size
    echo "Test 3: Database Size & Statistics\n";
    echo "-----------------------------------\n";
    $sql = "SELECT 
                DB_NAME() as DatabaseName,
                SUM(size * 8.0 / 1024) as SizeMB
            FROM sys.database_files";
    $stmt = $pdo->query($sql);
    $dbInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Database: {$dbInfo['DatabaseName']}\n";
    echo "Size: " . round($dbInfo['SizeMB'], 2) . " MB\n\n";
    
    // Test 4: Check Table Statistics
    echo "Test 4: Table Row Counts\n";
    echo "-----------------------------------\n";
    $sql = "SELECT 
                t.name AS TableName,
                p.rows AS RowCount
            FROM sys.tables t
            INNER JOIN sys.partitions p ON t.object_id = p.object_id
            WHERE p.index_id IN (0,1)
            AND t.name IN ('HeaderOrder', 'DetailOrderJasa', 'DetailOrderBarang', 
                           'FileCustomer', 'FileKendaraan', 'FileJasa', 'FileBarang')
            ORDER BY p.rows DESC";
    
    $stmt = $pdo->query($sql);
    $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($tables as $table) {
        $rowCount = number_format($table['RowCount']);
        echo "- {$table['TableName']}: {$rowCount} rows";
        
        if ($table['RowCount'] > 10000) {
            echo " ⚠️  BANYAK DATA - Perlu index!\n";
        } else {
            echo "\n";
        }
    }
    echo "\n";
    
    // Test 5: Check Indexes
    echo "Test 5: Index Analysis\n";
    echo "-----------------------------------\n";
    $sql = "SELECT 
                OBJECT_NAME(i.object_id) AS TableName,
                i.name AS IndexName,
                i.type_desc AS IndexType
            FROM sys.indexes i
            WHERE OBJECT_NAME(i.object_id) IN 
                ('HeaderOrder', 'DetailOrderJasa', 'DetailOrderBarang', 
                 'FileCustomer', 'FileKendaraan')
            AND i.name IS NOT NULL
            ORDER BY OBJECT_NAME(i.object_id), i.name";
    
    $stmt = $pdo->query($sql);
    $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($indexes) > 0) {
        foreach ($indexes as $index) {
            echo "- {$index['TableName']}.{$index['IndexName']} ({$index['IndexType']})\n";
        }
    } else {
        echo "❌ TIDAK ADA INDEX CUSTOM! Ini penyebab lambat!\n";
    }
    echo "\n";
    
    // Test 6: Memory Usage
    echo "Test 6: SQL Server Memory Usage\n";
    echo "-----------------------------------\n";
    $sql = "SELECT 
                (physical_memory_in_use_kb / 1024) AS MemoryUsedMB,
                (locked_page_allocations_kb / 1024) AS LockedPagesMB,
                (memory_utilization_percentage) AS MemoryUtilizationPct
            FROM sys.dm_os_process_memory";
    
    try {
        $stmt = $pdo->query($sql);
        $memory = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Memory Used: " . round($memory['MemoryUsedMB'], 2) . " MB\n";
        echo "Memory Utilization: {$memory['MemoryUtilizationPct']}%\n";
        
        if ($memory['MemoryUtilizationPct'] > 80) {
            echo "⚠️  MEMORY TINGGI! Consider increase SQL Server memory\n";
        }
    } catch (Exception $e) {
        echo "⚠️  Cannot get memory info (need VIEW SERVER STATE permission)\n";
    }
    echo "\n";
    
    // Test 7: Active Connections
    echo "Test 7: Active Connections\n";
    echo "-----------------------------------\n";
    $sql = "SELECT COUNT(*) as TotalConnections FROM sys.dm_exec_sessions WHERE is_user_process = 1";
    $stmt = $pdo->query($sql);
    $conn = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Active Connections: {$conn['TotalConnections']}\n";
    
    if ($conn['TotalConnections'] > 50) {
        echo "⚠️  BANYAK KONEKSI! Consider connection pooling\n";
    }
    echo "\n";
    
    // Test 8: Check for Blocking
    echo "Test 8: Blocking Sessions\n";
    echo "-----------------------------------\n";
    $sql = "SELECT 
                blocking_session_id,
                session_id,
                wait_type,
                wait_time,
                wait_resource
            FROM sys.dm_exec_requests
            WHERE blocking_session_id <> 0";
    
    $stmt = $pdo->query($sql);
    $blocks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($blocks) > 0) {
        echo "❌ ADA BLOCKING!\n";
        foreach ($blocks as $block) {
            echo "- Session {$block['session_id']} blocked by {$block['blocking_session_id']}\n";
            echo "  Wait: {$block['wait_type']} ({$block['wait_time']} ms)\n";
        }
    } else {
        echo "✅ No blocking detected\n";
    }
    echo "\n";
    
    // Test 9: Recent Slow Queries
    echo "Test 9: Slow Queries (Top 5)\n";
    echo "-----------------------------------\n";
    $sql = "SELECT TOP 5
                total_elapsed_time / execution_count / 1000.0 as avg_elapsed_time_ms,
                execution_count,
                SUBSTRING(st.text, (qs.statement_start_offset/2) + 1,
                    ((CASE statement_end_offset
                        WHEN -1 THEN DATALENGTH(st.text)
                        ELSE qs.statement_end_offset END
                        - qs.statement_start_offset)/2) + 1) AS query_text
            FROM sys.dm_exec_query_stats qs
            CROSS APPLY sys.dm_exec_sql_text(qs.sql_handle) st
            WHERE st.text LIKE '%ctautofashion%'
            ORDER BY avg_elapsed_time_ms DESC";
    
    try {
        $stmt = $pdo->query($sql);
        $slowQueries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($slowQueries) > 0) {
            foreach ($slowQueries as $i => $query) {
                $avgTime = round($query['avg_elapsed_time_ms'], 2);
                echo "\n" . ($i + 1) . ". Avg Time: {$avgTime} ms (Executed: {$query['execution_count']} times)\n";
                echo "   Query: " . substr($query['query_text'], 0, 100) . "...\n";
                
                if ($avgTime > 1000) {
                    echo "   ❌ SANGAT LAMBAT! Perlu optimasi!\n";
                } else if ($avgTime > 100) {
                    echo "   ⚠️  LAMBAT\n";
                }
            }
        } else {
            echo "✅ No slow queries detected in cache\n";
        }
    } catch (Exception $e) {
        echo "⚠️  Cannot get query stats (need VIEW SERVER STATE permission)\n";
    }
    echo "\n";
    
    // Summary & Recommendations
    echo "========================================\n";
    echo "RECOMMENDATIONS\n";
    echo "========================================\n\n";
    
    echo "1. ✅ ENABLE PERSISTENT CONNECTIONS\n";
    echo "   Di database.php, tambah:\n";
    echo "   \$pdo->setAttribute(PDO::ATTR_PERSISTENT, true);\n\n";
    
    echo "2. 🔍 CREATE INDEXES (PENTING!)\n";
    echo "   Jalankan query ini di SQL Server:\n";
    echo "   -- Index untuk HeaderOrder\n";
    echo "   CREATE INDEX IX_HeaderOrder_TanggalOrder ON HeaderOrder(TanggalOrder DESC);\n";
    echo "   CREATE INDEX IX_HeaderOrder_KodeCustomer ON HeaderOrder(KodeCustomer);\n";
    echo "   CREATE INDEX IX_HeaderOrder_StatusOrder ON HeaderOrder(StatusOrder);\n\n";
    
    echo "   -- Index untuk DetailOrderJasa\n";
    echo "   CREATE INDEX IX_DetailOrderJasa_NoOrder ON DetailOrderJasa(NoOrder);\n\n";
    
    echo "   -- Index untuk DetailOrderBarang\n";
    echo "   CREATE INDEX IX_DetailOrderBarang_NoOrder ON DetailOrderBarang(NoOrder);\n\n";
    
    echo "3. ⚡ OPTIMIZE SQL SERVER SETTINGS\n";
    echo "   - Max Server Memory: Set to 80% of RAM\n";
    echo "   - Min Server Memory: Set to 50% of RAM\n";
    echo "   - Cost Threshold for Parallelism: 5\n";
    echo "   - Max Degree of Parallelism: Number of CPU cores / 2\n\n";
    
    echo "4. 🧹 MAINTENANCE\n";
    echo "   - Update Statistics: sp_updatestats\n";
    echo "   - Rebuild Indexes: ALTER INDEX ALL ON [TableName] REBUILD\n";
    echo "   - Shrink Database (jika perlu): DBCC SHRINKDATABASE(ctautofashion)\n\n";
    
    echo "5. 📊 MONITOR PERFORMANCE\n";
    echo "   - Enable Query Store\n";
    echo "   - Check Execution Plans\n";
    echo "   - Monitor Wait Statistics\n\n";
    
    echo "========================================\n";
    echo "QUICK FIX SCRIPT\n";
    echo "========================================\n\n";
    echo "Jalankan script ini di SQL Server Management Studio:\n\n";
    echo "-- Update all statistics\n";
    echo "EXEC sp_updatestats;\n\n";
    echo "-- Clear procedure cache (reset)\n";
    echo "DBCC FREEPROCCACHE;\n\n";
    echo "-- Clear buffer pool (hati-hati di production!)\n";
    echo "-- DBCC DROPCLEANBUFFERS;\n\n";
    
} catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "\nTroubleshooting:\n";
    echo "1. Pastikan SQL Server service running\n";
    echo "2. Pastikan TCP/IP enabled di SQL Server Configuration Manager\n";
    echo "3. Pastikan port 1433 tidak di-block firewall\n";
    echo "4. Check SQL Server Error Log\n";
}

echo "\n========================================\n";
echo "DIAGNOSA SELESAI\n";
echo "========================================\n";
?>

