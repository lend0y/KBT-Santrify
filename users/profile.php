<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

$pageTitle = 'Profil Pengguna';
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

// Get user progress
try {
    $progressStmt = $conn->prepare("
        SELECT l.title, l.duration, up.completed, up.completed_at 
        FROM user_progress up
        JOIN lessons l ON up.lesson_id = l.id
        WHERE up.user_id = :user_id
        ORDER BY up.completed_at DESC
    ");
    $progressStmt->bindParam(':user_id', $userId);
    $progressStmt->execute();
    $progress = $progressStmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $progressError = "Gagal memuat progress: " . $e->getMessage();
}

require_once '../includes/header.php';
?>

<div class="profile-container">
    <div class="profile-sidebar">
        <div class="profile-card">
            <div class="profile-picture">
                <img src="<?php echo BASE_URL; ?>assets/images/users/<?php echo $user['profile_pic']; ?>" alt="<?php echo $user['full_name']; ?>">
                <a href="settings.php" class="btn btn-outline btn-sm"><i class="fas fa-edit"></i> Edit Profil</a>
            </div>
            <div class="profile-info">
                <h2><?php echo $user['full_name']; ?></h2>
                <p><i class="fas fa-user"></i> <?php echo $user['username']; ?></p>
                <p><i class="fas fa-envelope"></i> <?php echo $user['email']; ?></p>
                <p><i class="fas fa-calendar-alt"></i> Bergabung pada <?php echo date('d M Y', strtotime($user['created_at'])); ?></p>
            </div>
        </div>
        
        <div class="profile-stats">
            <div class="stat-item">
                <h3>Pelajaran Selesai</h3>
                <p><?php echo count(array_filter($progress, function($item) { return $item['completed']; })); ?></p>
            </div>
            <div class="stat-item">
                <h3>Total Waktu Belajar</h3>
                <p>
                    <?php 
                    $totalMinutes = array_sum(array_map(function($item) { 
                        return $item['completed'] ? $item['duration'] : 0; 
                    }, $progress));
                    echo floor($totalMinutes / 60) . ' jam ' . ($totalMinutes % 60) . ' menit';
                    ?>
                </p>
            </div>
        </div>
    </div>
    
    <div class="profile-content">
        <section class="profile-section">
            <h2><i class="fas fa-info-circle"></i> Tentang Saya</h2>
            <div class="section-content">
                <?php if (!empty($user['bio'])): ?>
                    <p><?php echo nl2br($user['bio']); ?></p>
                <?php else: ?>
                    <p class="text-muted">Belum ada bio. <a href="settings.php">Tambahkan sekarang</a>.</p>
                <?php endif; ?>
            </div>
        </section>
        
        <section class="profile-section">
            <h2><i class="fas fa-chart-line"></i> Progress Belajar</h2>
            <div class="section-content">
                <?php if (isset($progressError)): ?>
                    <div class="alert alert-danger"><?php echo $progressError; ?></div>
                <?php elseif (empty($progress)): ?>
                    <p class="text-muted">Belum ada progress belajar. <a href="pages/services.php">Mulai belajar sekarang</a>.</p>
                <?php else: ?>
                    <div class="progress-list">
                        <?php foreach ($progress as $item): ?>
                            <div class="progress-item <?php echo $item['completed'] ? 'completed' : ''; ?>">
                                <div class="progress-title">
                                    <h3><?php echo $item['title']; ?></h3>
                                    <span><?php echo $item['duration']; ?> menit</span>
                                </div>
                                <div class="progress-status">
                                    <?php if ($item['completed']): ?>
                                        <span class="completed"><i class="fas fa-check-circle"></i> Selesai pada <?php echo date('d M Y', strtotime($item['completed_at'])); ?></span>
                                    <?php else: ?>
                                        <span class="in-progress"><i class="fas fa-spinner"></i> Dalam proses</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        
        <section class="profile-section">
            <h2><i class="fas fa-bookmark"></i> Artikel Favorit</h2>
            <div class="section-content">
                <p class="text-muted">Fitur ini akan segera hadir.</p>
            </div>
        </section>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>