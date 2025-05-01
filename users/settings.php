<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

$pageTitle = 'Pengaturan Akun';
$userId = $_SESSION['user_id'];

// Get user data
try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userId);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        $error = "Pengguna tidak ditemukan.";
    }
} catch(PDOException $e) {
    $error = "Error: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name']);
    $bio = sanitize($_POST['bio']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    $errors = [];
    
    // Update profile info
    try {
        $updateStmt = $conn->prepare("UPDATE users SET full_name = :full_name, bio = :bio WHERE id = :id");
        $updateStmt->bindParam(':full_name', $full_name);
        $updateStmt->bindParam(':bio', $bio);
        $updateStmt->bindParam(':id', $userId);
        $updateStmt->execute();
        
        $_SESSION['user_full_name'] = $full_name;
        $success = "Profil berhasil diperbarui.";
    } catch(PDOException $e) {
        $errors[] = "Gagal memperbarui profil: " . $e->getMessage();
    }
    
    // Update password if provided
    if (!empty($current_password) && !empty($new_password)) {
        if (empty($new_password) || empty($confirm_password)) {
            $errors[] = "Password baru harus diisi.";
        } elseif ($new_password !== $confirm_password) {
            $errors[] = "Password baru dan konfirmasi password tidak sama.";
        } elseif (!password_verify($current_password, $user['password'])) {
            $errors[] = "Password saat ini salah.";
        } else {
            try {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $passwordStmt = $conn->prepare("UPDATE users SET password = :password WHERE id = :id");
                $passwordStmt->bindParam(':password', $hashed_password);
                $passwordStmt->bindParam(':id', $userId);
                $passwordStmt->execute();
                
                $success = "Profil dan password berhasil diperbarui.";
            } catch(PDOException $e) {
                $errors[] = "Gagal memperbarui password: " . $e->getMessage();
            }
        }
    }
    
    // Handle profile picture upload
    if (!empty($_FILES['profile_pic']['name'])) {
        $target_dir = "../assets/images/users/";
        $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["profile_pic"]["tmp_name"]);
        if ($check === false) {
            $errors[] = "File bukan gambar.";
        }
        
        // Check file size (max 2MB)
        if ($_FILES["profile_pic"]["size"] > 2000000) {
            $errors[] = "Ukuran gambar terlalu besar (maksimal 2MB).";
        }
        
        // Allow certain file formats
        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $errors[] = "Hanya format JPG, JPEG, PNG & GIF yang diperbolehkan.";
        }
        
        if (empty($errors)) {
            // Generate unique filename
            $new_filename = 'user_' . $userId . '_' . time() . '.' . $imageFileType;
            $target_file = $target_dir . $new_filename;
            
            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                try {
                    // Delete old profile pic if not default
                    if ($user['profile_pic'] !== 'default.jpg') {
                        $old_file = $target_dir . $user['profile_pic'];
                        if (file_exists($old_file)) {
                            unlink($old_file);
                        }
                    }
                    
                    // Update database
                    $picStmt = $conn->prepare("UPDATE users SET profile_pic = :profile_pic WHERE id = :id");
                    $picStmt->bindParam(':profile_pic', $new_filename);
                    $picStmt->bindParam(':id', $userId);
                    $picStmt->execute();
                    
                    $_SESSION['user_profile_pic'] = $new_filename;
                    $success = "Profil dan foto profil berhasil diperbarui.";
                } catch(PDOException $e) {
                    $errors[] = "Gagal memperbarui foto profil: " . $e->getMessage();
                }
            } else {
                $errors[] = "Terjadi kesalahan saat mengunggah gambar.";
            }
        }
    }
    
    // Refresh user data after update
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

require_once '../includes/header.php';
?>

<div class="settings-container">
    <div class="settings-sidebar">
        <div class="settings-nav">
            <ul>
                <li class="active"><a href="settings.php"><i class="fas fa-user-cog"></i> Profil</a></li>
                <li><a href="security.php"><i class="fas fa-shield-alt"></i> Keamanan</a></li>
                <li><a href="notifications.php"><i class="fas fa-bell"></i> Notifikasi</a></li>
                <li><a href="privacy.php"><i class="fas fa-lock"></i> Privasi</a></li>
            </ul>
        </div>
    </div>
    
    <div class="settings-content">
        <h1><i class="fas fa-user-cog"></i> Pengaturan Profil</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form action="settings.php" method="POST" enctype="multipart/form-data">
            <div class="form-section">
                <h2><i class="fas fa-id-card"></i> Informasi Pribadi</h2>
                
                <div class="form-group">
                    <label for="profile_pic">Foto Profil</label>
                    <div class="profile-pic-upload">
                        <img src="<?php echo BASE_URL; ?>assets/images/users/<?php echo $user['profile_pic']; ?>" alt="Foto Profil" id="profile-pic-preview">
                        <div class="upload-controls">
                            <input type="file" id="profile_pic" name="profile_pic" accept="image/*">
                            <label for="profile_pic" class="btn btn-outline">Pilih Gambar</label>
                            <small>Format: JPG, JPEG, PNG (maks. 2MB)</small>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="full_name"><i class="fas fa-user"></i> Nama Lengkap</label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo $user['full_name']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="bio"><i class="fas fa-edit"></i> Bio</label>
                    <textarea id="bio" name="bio" rows="4"><?php echo $user['bio']; ?></textarea>
                </div>
            </div>
            
            <div class="form-section">
                <h2><i class="fas fa-lock"></i> Ubah Password</h2>
                <p class="section-description">Biarkan kosong jika tidak ingin mengubah password.</p>
                
                <div class="form-group">
                    <label for="current_password"><i class="fas fa-key"></i> Password Saat Ini</label>
                    <input type="password" id="current_password" name="current_password">
                </div>
                
                <div class="form-group">
                    <label for="new_password"><i class="fas fa-key"></i> Password Baru</label>
                    <input type="password" id="new_password" name="new_password">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password"><i class="fas fa-key"></i> Konfirmasi Password Baru</label>
                    <input type="password" id="confirm_password" name="confirm_password">
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                <a href="profile.php" class="btn btn-outline"><i class="fas fa-times"></i> Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
// Preview profile picture before upload
document.getElementById('profile_pic').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profile-pic-preview').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});
</script>

<?php
require_once '../includes/footer.php';
?>