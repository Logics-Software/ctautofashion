<?php
// Database configuration for MSSQL
$serverName = "127.0.0.1"; // Change to your SQL Server instance
$database = "ctautofashion"; // Change to your database name
$username = "bengkel"; // Change to your username
$password = "Logics051199"; // Change to your password

try {
    // Create connection using PDO with SSL options for ODBC Driver 18
    $connectionString = "sqlsrv:server=$serverName;Database=$database;TrustServerCertificate=yes;Encrypt=no";
    
    // PDO Options untuk optimasi performance
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::SQLSRV_ATTR_ENCODING => PDO::SQLSRV_ENCODING_UTF8  // UTF-8 encoding
    ];
    
    $pdo = new PDO($connectionString, $username, $password, $options);
    
    // Additional optimizations
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);  // Fetch as associative array by default
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Make PDO connection globally available
$GLOBALS['pdo'] = $pdo;
