<?php
class UserModel {
    private $pdo;
    
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }
    
    /**
     * Authenticate user with UserID and PasswordOnline
     * Login is case-insensitive (UPPERCASE)
     */
    public function authenticate($userid, $password) {
        try {
            // Convert to uppercase for case-insensitive login
            $userid_upper = strtoupper($userid);
            $password_upper = strtoupper($password);
            
            
            // Try different table names in case table structure is different
            $tableNames = ['_FileUser', 'FileUser', 'Users', 'users'];
            
            foreach ($tableNames as $tableName) {
                try {
                    // First try exact match (case sensitive)
                    $sql = "SELECT * FROM [$tableName] WHERE UserID = ? AND PasswordOnline = ?";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([$userid, $password]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($user) {
                        return $user;
                    }
                    
                    // Then try case-insensitive match
                    $sql = "SELECT * FROM [$tableName] WHERE UPPER(UserID) = ? AND UPPER(PasswordOnline) = ?";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([$userid_upper, $password_upper]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($user) {
                        return $user;
                    }
                } catch (PDOException $e) {
                    continue;
                }
            }
            
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Get user by UserID
     */
    public function getUserById($userid) {
        try {
            $sql = "SELECT * FROM _FileUser WHERE UPPER(UserID) = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([strtoupper($userid)]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Get all users (for admin purposes)
     */
    public function getAllUsers() {
        try {
            // Try different table names
            $tableNames = ['_FileUser', 'FileUser', 'Users', 'users'];
            
            foreach ($tableNames as $tableName) {
                try {
                    $sql = "SELECT UserID, PasswordOnline FROM [$tableName]";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute();
                    
                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    continue;
                }
            }
            
            return [];
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>
