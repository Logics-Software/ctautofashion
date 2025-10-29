<?php
// Database configuration for MSSQL
$serverName = "127.0.0.1"; // Change to your SQL Server instance
$database = "ctautofashion"; // Change to your database name
$username = "sa"; // Change to your username
$password = "051199"; // Change to your password

try {
    // Check if SQL Server PDO driver is available
    if (!in_array('sqlsrv', PDO::getAvailableDrivers())) {
        error_log("SQL Server PDO Driver Not Available");
        die("Error: SQL Server PDO Driver Not Available. Please install pdo_sqlsrv extension.");
    }
    
    // Create connection using PDO with SSL options for ODBC Driver 18
    $connectionString = "sqlsrv:server=$serverName;Database=$database;TrustServerCertificate=yes;Encrypt=no";
    
    // PDO Options untuk optimasi performance
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ];
    
    // Add SQL Server specific encoding if extension is loaded
    if (extension_loaded('pdo_sqlsrv') && defined('PDO::SQLSRV_ATTR_ENCODING')) {
        $options[PDO::SQLSRV_ATTR_ENCODING] = PDO::SQLSRV_ENCODING_UTF8;
    }
    
    $pdo = new PDO($connectionString, $username, $password, $options);
    
    // Additional optimizations
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);  // Fetch as associative array by default
} catch (PDOException $e) {
    error_log("Database Connection Failed: " . $e->getMessage());
    die("Database Connection Failed: " . $e->getMessage());
}

// Make PDO connection globally available
$GLOBALS['pdo'] = $pdo;
