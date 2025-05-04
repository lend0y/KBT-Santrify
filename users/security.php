<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

$pageTitle = 'Keamanan Akun';
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

// Handle change password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    $errors = [];
    
    // Validate password
    if (empty($current_password)) {
        $errors[] = "Password saat ini harus diisi.";
    }
    
    if (empty($new_password)) {
        $errors[] = "Password baru harus diisi.";
    } elseif (strlen($new_password) < 6) {
        $errors[] = "Password minimal 6 karakter.";
    } elseif ($new_password !== $confirm_password) {
        $errors[] = "Password baru dan konfirmasi password tidak sama.";
    }
    
    // Verify current password
    if (empty($errors) && !password_verify($current_password, $user['password'])) {
        $errors[] = "Password saat ini salah.";
    }
    
    // Update password
    if (empty($errors)) {
        try {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = :password WHERE id = :id");
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            
            $success = "Password berhasil diubah.";
        } catch(PDOException $e) {
            $errors[] = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}

require_once '../includes/header.php';
?>

<div class="settings-container">
    <div class="settings-sidebar">
        <div class="settings-nav">
            <ul>
                <li><a href="settings.php"><i class="fas fa-user-cog"></i> Profil</a></li>
                <li class="active"><a href="security.php"><i class="fas fa-shield-alt"></i> Keamanan</a></li>
                <li><a href="notifications.php"><i class="fas fa-bell"></i> Notifikasi</a></li>
                <li><a href="privacy.php"><i class="fas fa-lock"></i> Privasi</a></li>
            </ul>
        </div>
    </div>
    
    <div class="settings-content">
        <h1><i class="fas fa-shield-alt"></i> Keamanan Akun</h1>
        
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
        
        <div class="form-section">
            <h2><i class="fas fa-key"></i> Ubah Password</h2>
            <p class="section-description">Password yang kuat membantu melindungi akun Anda.</p>
            
            <form action="security.php" method="POST">
                <div class="form-group">
                    <label for="current_password"><i class="fas fa-unlock"></i> Password Saat Ini</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                
                <div class="form-group">
                    <label for="new_password"><i class="fas fa-lock"></i> Password Baru</label>
                    <input type="password" id="new_password" name="new_password" required>
                    <small>Password harus minimal 6 karakter. Gunakan kombinasi huruf, angka, dan simbol.</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password"><i class="fas fa-check-circle"></i> Konfirmasi Password Baru</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
        
        <div class="form-section">
            <h2><i class="fas fa-history"></i> Riwayat Login</h2>
            <p class="section-description">Berikut adalah aktivitas login terbaru pada akun Anda.</p>
            
            <div class="login-history">
                <div class="login-item">
                    <div class="login-icon">
                        <i class="fas fa-laptop"></i>
                    </div>
                    <div class="login-details">
                        <h4>Login berhasil</h4>
                        <p>Perangkat: Chrome - Windows</p>
                        <small>Waktu: <?php echo date('d M Y, H:i', strtotime('-1 hour')); ?></small>
                    </div>
                    <div class="login-status success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                
                <div class="login-item">
                    <div class="login-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div class="login-details">
                        <h4>Login berhasil</h4>
                        <p>Perangkat: Safari - iPhone</p>
                        <small>Waktu: <?php echo date('d M Y, H:i', strtotime('-2 day')); ?></small>
                    </div>
                    <div class="login-status success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h2><i class="fas fa-question-circle"></i> Pertanyaan Keamanan</h2>
            <p class="section-description">Pertanyaan keamanan membantu memulihkan akun jika Anda lupa password.</p>
            
            <form action="#" method="POST" class="security-question-form">
                <div class="form-group">
                    <label for="security_question">Pilih Pertanyaan</label>
                    <select id="security_question" name="security_question">
                        <option value="1">Siapa nama guru favorit Anda?</option>
                        <option value="2">Apa nama hewan peliharaan pertama Anda?</option>
                        <option value="3">Di kota mana Anda lahir?</option>
                        <option value="4">Apa makanan favorit Anda?</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="security_answer">Jawaban</label>
                    <input type="text" id="security_answer" name="security_answer">
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-outline"><i class="fas fa-save"></i> Simpan Pertanyaan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.login-history {
    margin-top: 20px;
}

.login-item {
    display: flex;
    align-items: center;
    padding: 15px;
    border-radius: 8px;
    background-color: #f9f9f9;
    margin-bottom: 15px;
}

.login-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background-color: var(--light-color);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    font-size: 1.5rem;
    color: var(--primary-color);
}

.login-details {
    flex: 1;
}

.login-details h4 {
    margin: 0 0 5px;
    font-size: 1rem;
}

.login-details p {
    margin: 0 0 3px;
    font-size: 0.9rem;
}

.login-details small {
    color: #666;
}

.login-status {
    width: 30px;
    text-align: center;
}

.login-status.success {
    color: var(--success-color);
}

.login-status.failed {
    color: var(--danger-color);
}

.security-question-form {
    background-color: #f9f9f9;
    border-radius: 8px;
    padding: 20px;
    margin-top: 15px;
}
</style>

<?php
require_once '../includes/footer.php';
?>