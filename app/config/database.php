<?php
// Database configuration for MSSQL
$serverName = "127.0.0.1"; // Change to your SQL Server instance
$database = "ctautofashion"; // Change to your database name
$username = "sa"; // Change to your username
$password = "051199"; // Change to your password

try {
    // Create connection using PDO with SSL options for ODBC Driver 18
    $connectionString = "sqlsrv:server=$serverName;Database=$database;TrustServerCertificate=yes;Encrypt=no";
    $pdo = new PDO($connectionString, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Test connection
    if ($pdo) {
        echo "<!-- Database connection successful -->";
    }
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Make PDO connection globally available
$GLOBALS['pdo'] = $pdo;
?>
