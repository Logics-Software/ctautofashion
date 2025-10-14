<?php
$title = 'Profile Saya';
?>

<div class="container">
    <div class="main-container fade-in">
        <div class="row me-0">
            <div class="col-12">
                <h2>Edit Profil</h2>
            </div>
        </div>
        
        <hr>

        <div class="row justify-content-center">
            <div class="col-md-12">
                <!-- Edit Profile Form -->

                <form method="POST" action="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/profile/update" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="user_id" class="form-label">
                                    <i class="fas fa-id-card icon"></i>User ID
                                </label>
                                <input type="text" 
                                    class="form-control" 
                                    id="user_id" 
                                    value="<?php echo htmlspecialchars($user_id); ?>" 
                                    readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="user_name" class="form-label">
                                    <i class="fas fa-user icon"></i>Nama User <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                    class="form-control" 
                                    id="user_name" 
                                    name="user_name" 
                                    value="<?php echo htmlspecialchars($user_name); ?>" 
                                    placeholder="Masukkan nama user"
                                    required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Profile Photo Section -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="profile_photo" class="form-label">
                                    <i class="fas fa-camera icon"></i>Foto Profile
                                </label>
                                <input type="file" 
                                    class="form-control" 
                                    id="profile_photo" 
                                    name="profile_photo" 
                                    accept="image/*">
                                <div class="form-text">
                                    <i class="fa-solid fa-circle-info icon"></i>
                                    Format: JPG, PNG, GIF. Maksimal 2MB
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Preview Foto</label>
                                <div class="text-center">
                                    <?php if ($profile_photo && file_exists(BASE_PATH . '/' . $profile_photo)): ?>
                                        <img src="<?php echo dirname($_SERVER['SCRIPT_NAME']) . '/' . htmlspecialchars($profile_photo); ?>" 
                                            alt="Profile Photo" 
                                            class="profile-photo-preview img-fluid rounded-circle">
                                    <?php else: ?>
                                        <div class="profile-photo-placeholder">
                                            <i class="fas fa-user-circle fa-3x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/dashboard" class="btn btn-secondary">
                            <i class="fas fa-times icon"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save icon"></i>Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

