<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

$pageTitle = 'Pengaturan Notifikasi';
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
    $email_notification = isset($_POST['email_notification']) ? 1 : 0;
    $new_article = isset($_POST['new_article']) ? 1 : 0;
    $learning_reminder = isset($_POST['learning_reminder']) ? 1 : 0;
    
    $success = "Pengaturan notifikasi berhasil disimpan.";
}

require_once '../includes/header.php';
?>

<div class="settings-container">
    <div class="settings-sidebar">
        <div class="settings-nav">
            <ul>
                <li><a href="settings.php"><i class="fas fa-user-cog"></i> Profil</a></li>
                <li><a href="security.php"><i class="fas fa-shield-alt"></i> Keamanan</a></li>
                <li class="active"><a href="notifications.php"><i class="fas fa-bell"></i> Notifikasi</a></li>
                <li><a href="privacy.php"><i class="fas fa-lock"></i> Privasi</a></li>
            </ul>
        </div>
    </div>
    
    <div class="settings-content">
        <h1><i class="fas fa-bell"></i> Pengaturan Notifikasi</h1>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="form-section">
            <h2><i class="fas fa-envelope"></i> Notifikasi Email</h2>
            <p class="section-description">Atur notifikasi email yang ingin Anda terima dari Santrify.</p>
            
            <form action="notifications.php" method="POST">
                <div class="notification-settings">
                    <div class="notification-item">
                        <div class="notification-check">
                            <input type="checkbox" id="email_notification" name="email_notification" checked>
                            <label for="email_notification"></label>
                        </div>
                        <div class="notification-details">
                            <h4>Email Notifikasi</h4>
                            <p>Terima email notifikasi dari Santrify</p>
                        </div>
                    </div>
                    
                    <div class="notification-item">
                        <div class="notification-check">
                            <input type="checkbox" id="new_article" name="new_article" checked>
                            <label for="new_article"></label>
                        </div>
                        <div class="notification-details">
                            <h4>Artikel Baru</h4>
                            <p>Dapatkan notifikasi saat artikel baru dipublikasikan</p>
                        </div>
                    </div>
                    
                    <div class="notification-item">
                        <div class="notification-check">
                            <input type="checkbox" id="learning_reminder" name="learning_reminder" checked>
                            <label for="learning_reminder"></label>
                        </div>
                        <div class="notification-details">
                            <h4>Pengingat Belajar</h4>
                            <p>Terima pengingat untuk melanjutkan pembelajaran</p>
                        </div>
                    </div>
                </div>
                
                <div class="form-section">
                    <h2><i class="fas fa-mobile-alt"></i> Pengaturan WhatsApp</h2>
                    <p class="section-description">Atur notifikasi WhatsApp untuk informasi penting.</p>
                    
                    <div class="form-group">
                        <label for="phone_number">Nomor WhatsApp</label>
                        <input type="text" id="phone_number" name="phone_number" value="<?php echo isset($user['phone']) ? $user['phone'] : ''; ?>" placeholder="Contoh: 08123456789">
                        <small>Kami akan mengirimkan kode verifikasi untuk mengonfirmasi nomor WhatsApp Anda.</small>
                    </div>
                    
                    <div class="notification-item">
                        <div class="notification-check">
                            <input type="checkbox" id="whatsapp_notification" name="whatsapp_notification">
                            <label for="whatsapp_notification"></label>
                        </div>
                        <div class="notification-details">
                            <h4>Notifikasi WhatsApp</h4>
                            <p>Terima notifikasi penting melalui WhatsApp</p>
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
.notification-settings {
    margin-top: 20px;
}

.notification-item {
    display: flex;
    align-items: center;
    padding: 15px;
    border-radius: 8px;
    background-color: #f9f9f9;
    margin-bottom: 15px;
}

.notification-check {
    margin-right: 15px;
}

.notification-check input[type="checkbox"] {
    display: none;
}

.notification-check label {
    display: inline-block;
    width: 50px;
    height: 25px;
    position: relative;
    background-color: #ccc;
    border-radius: 25px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.notification-check label:after {
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

.notification-check input[type="checkbox"]:checked + label {
    background-color: var(--primary-color);
}

.notification-check input[type="checkbox"]:checked + label:after {
    left: 27px;
}

.notification-details {
    flex: 1;
}

.notification-details h4 {
    margin: 0 0 5px;
    font-size: 1rem;
}

.notification-details p {
    margin: 0;
    font-size: 0.9rem;
    color: #666;
}
</style>

<?php
require_once '../includes/footer.php';
?>