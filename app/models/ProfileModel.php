<?php
class ProfileModel {
    private $pdo;
    
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }
    
    /**
     * Get profile photo for user
     */
    public function getProfilePhoto($user_id) {
        try {
            $sql = "SELECT Foto FROM FotoProfile WHERE UserID = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$user_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ? $result['Foto'] : null;
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Upload profile photo - SIMPLE VERSION
     */
    public function uploadProfilePhoto($user_id, $file) {
        try {
            // Basic validation
            if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
                return ['success' => false, 'message' => 'File tidak ditemukan.'];
            }
            
            if (!file_exists($file['tmp_name'])) {
                return ['success' => false, 'message' => 'File temporary tidak ditemukan.'];
            }
            
            // Check file type
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!in_array($file['type'], $allowed_types)) {
                return ['success' => false, 'message' => 'Format file tidak didukung.'];
            }
            
            // Check file size (2MB max)
            if ($file['size'] > 2 * 1024 * 1024) {
                return ['success' => false, 'message' => 'File terlalu besar. Maksimal 2MB.'];
            }
            
            // Create upload directory
            $upload_dir = __DIR__ . '/../../uploads/profiles/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Generate filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = $user_id . '_' . time() . '.' . $extension;
            $file_path = $upload_dir . $filename;
            
            // Move file
            if (move_uploaded_file($file['tmp_name'], $file_path)) {
                // Save to database
                $db_path = 'uploads/profiles/' . $filename;
                
                // Try UPDATE first
                $sql = "UPDATE FotoProfile SET Foto = ? WHERE UserID = ?";
                $stmt = $this->pdo->prepare($sql);
                $result = $stmt->execute([$db_path, $user_id]);
                
                if ($stmt->rowCount() > 0) {
                    return ['success' => true, 'message' => 'Foto profile berhasil diupload!', 'filename' => $filename];
                }
                
                // If no rows affected, try INSERT
                $sql = "INSERT INTO FotoProfile (UserID, Foto) VALUES (?, ?)";
                $stmt = $this->pdo->prepare($sql);
                $result = $stmt->execute([$user_id, $db_path]);
                
                if ($result) {
                    return ['success' => true, 'message' => 'Foto profile berhasil diupload!', 'filename' => $filename];
                } else {
                    // Delete file if database update failed
                    unlink($file_path);
                    return ['success' => false, 'message' => 'Gagal menyimpan ke database.'];
                }
            } else {
                return ['success' => false, 'message' => 'Gagal upload file.'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }
    
    /**
     * Delete profile photo
     */
    public function deleteProfilePhoto($user_id) {
        try {
            // Get current photo
            $current_photo = $this->getProfilePhoto($user_id);
            
            // Delete from database
            $sql = "DELETE FROM FotoProfile WHERE UserID = ?";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([$user_id]);
            
            if ($result) {
                // Delete file if exists
                if ($current_photo && file_exists(__DIR__ . '/../../' . $current_photo)) {
                    unlink(__DIR__ . '/../../' . $current_photo);
                }
                
                return ['success' => true, 'message' => 'Foto profile berhasil dihapus!'];
            } else {
                return ['success' => false, 'message' => 'Gagal menghapus dari database.'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }
    
    /**
     * Update user profile data
     */
    public function updateProfile($user_id, $data) {
        try {
            $sql = "UPDATE _FileUser SET ";
            $params = [];
            $fields = [];
            
            if (isset($data['UserName']) && !empty($data['UserName'])) {
                $fields[] = "UserName = ?";
                $params[] = $data['UserName'];
            }
            
            if (isset($data['Email']) && !empty($data['Email'])) {
                $fields[] = "Email = ?";
                $params[] = $data['Email'];
            }
            
            if (isset($data['Phone']) && !empty($data['Phone'])) {
                $fields[] = "Phone = ?";
                $params[] = $data['Phone'];
            }
            
            if (empty($fields)) {
                return ['success' => false, 'message' => 'Tidak ada data yang diupdate.'];
            }
            
            $sql .= implode(', ', $fields) . " WHERE UserID = ?";
            $params[] = $user_id;
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($params);
            
            if ($result) {
                return ['success' => true, 'message' => 'Profile berhasil diupdate!'];
            } else {
                return ['success' => false, 'message' => 'Gagal mengupdate profile.'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }
    
    /**
     * Update user password
     */
    public function updatePassword($user_id, $new_password) {
        try {
            $sql = "UPDATE _FileUser SET PasswordOnline = ? WHERE UserID = ?";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([$new_password, $user_id]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Password berhasil diubah!'];
            } else {
                return ['success' => false, 'message' => 'Gagal mengubah password.'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }
}
?>