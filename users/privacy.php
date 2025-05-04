<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

$pageTitle = 'Pengaturan Privasi';
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
    // In production, these would be saved to a user_preferences table
    $profile_visibility = sanitize($_POST['profile_visibility']);
    $show_progress = isset($_POST['show_progress']) ? 1 : 0;
    $show_email = isset($_POST['show_email']) ? 1 : 0;
    
    $success = "Pengaturan privasi berhasil disimpan.";
}

require_once '../includes/header.php';
?>

<div class="settings-container">
    <div class="settings-sidebar">
        <div class="settings-nav">
            <ul>
                <li><a href="settings.php"><i class="fas fa-user-cog"></i> Profil</a></li>
                <li><a href="security.php"><i class="fas fa-shield-alt"></i> Keamanan</a></li>
                <li><a href="notifications.php"><i class="fas fa-bell"></i> Notifikasi</a></li>
                <li class="active"><a href="privacy.php"><i class="fas fa-lock"></i> Privasi</a></li>
            </ul>
        </div>
    </div>
    
    <div class="settings-content">
        <h1><i class="fas fa-lock"></i> Pengaturan Privasi</h1>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="form-section">
            <h2><i class="fas fa-eye"></i> Visibilitas Profil</h2>
            <p class="section-description">Atur siapa yang dapat melihat profil dan informasi Anda.</p>
            
            <form action="privacy.php" method="POST">
                <div class="form-group">
                    <label for="profile_visibility">Siapa yang dapat melihat profil saya</label>
                    <select id="profile_visibility" name="profile_visibility">
                        <option value="public">Semua orang</option>
                        <option value="users">Hanya pengguna terdaftar</option>
                        <option value="private">Hanya admin dan saya</option>
                    </select>
                </div>
                
                <div class="privacy-options">
                    <div class="privacy-item">
                        <div class="privacy-check">
                            <input type="checkbox" id="show_progress" name="show_progress" checked>
                            <label for="show_progress"></label>
                        </div>
                        <div class="privacy-details">
                            <h4>Progres Belajar</h4>
                            <p>Izinkan pengguna lain melihat progres belajar saya</p>
                        </div>
                    </div>
                    
                    <div class="privacy-item">
                        <div class="privacy-check">
                            <input type="checkbox" id="show_email" name="show_email">
                            <label for="show_email"></label>
                        </div>
                        <div class="privacy-details">
                            <h4>Alamat Email</h4>
                            <p>Tampilkan alamat email saya di profil</p>
                        </div>
                    </div>
                </div>
                
                <div class="form-section">
                    <h2><i class="fas fa-download"></i> Data Akun</h2>
                    <p class="section-description">Unduh data atau hapus akun Anda.</p>
                    
                    <div class="data-options">
                        <div class="data-option">
                            <h4><i class="fas fa-file-download"></i> Unduh Data</h4>
                            <p>Unduh semua data profil, progres belajar, dan aktivitas akun Anda.</p>
                            <button type="button" class="btn btn-outline btn-sm">Unduh Data Saya</button>
                        </div>
                        
                        <div class="data-option danger">
                            <h4><i class="fas fa-user-times"></i> Hapus Akun</h4>
                            <p>Menghapus akun Anda akan menghapus semua data pribadi Anda secara permanen.</p>
                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete()">Hapus Akun Saya</button>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Pengaturan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.privacy-options {
    margin-top: 20px;
}

.privacy-item {
    display: flex;
    align-items: center;
    padding: 15px;
    border-radius: 8px;
    background-color: #f9f9f9;
    margin-bottom: 15px;
}

.privacy-check {
    margin-right: 15px;
}

.privacy-check input[type="checkbox"] {
    display: none;
}

.privacy-check label {
    display: inline-block;
    width: 50px;
    height: 25px;
    position: relative;
    background-color: #ccc;
    border-radius: 25px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.privacy-check label:after {
    content: '';
    position: absolute;
    width: 21px;
    height: 21px;
    border-radius: 50%;
    background-color: white;
    top: 2px;
    left: 2px;
    transition: all 0.3s ease;
}

.privacy-check input[type="checkbox"]:checked + label {
    background-color: var(--primary-color);
}

.privacy-check input[type="checkbox"]:checked + label:after {
    left: 27px;
}

.privacy-details {
    flex: 1;
}

.privacy-details h4 {
    margin: 0 0 5px;
    font-size: 1rem;
}

.privacy-details p {
    margin: 0;
    font-size: 0.9rem;
    color: #666;
}

.data-options {
    margin-top: 20px;
}

.data-option {
    background-color: #f9f9f9;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 15px;
}

.data-option.danger {
    background-color: #fff0f0;
    border: 1px solid #ffdddd;
}

.data-option h4 {
    margin: 0 0 10px;
    font-size: 1.1rem;
    color: var(--dark-color);
}

.data-option.danger h4 {
    color: var(--danger-color);
}

.data-option p {
    margin: 0 0 15px;
    font-size: 0.9rem;
    color: #666;
}

.data-option .btn {
    margin-top: 5px;
}
</style>

<script>
function confirmDelete() {
    if (confirm("Anda yakin ingin menghapus akun? Tindakan ini tidak dapat dibatalkan dan semua data Anda akan hilang secara permanen.")) {
        alert("Fitur ini akan segera tersedia. Silakan hubungi admin untuk menghapus akun Anda saat ini.");
    }
}
</script>

<?php
require_once '../includes/footer.php';
?>