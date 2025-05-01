<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$pageTitle = 'Dashboard Admin';

// Get stats
try {
    $userCount = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $articleCount = $conn->query("SELECT COUNT(*) FROM articles")->fetchColumn();
    $lessonCount = $conn->query("SELECT COUNT(*) FROM lessons")->fetchColumn();
} catch(PDOException $e) {
    $error = "Error: " . $e->getMessage();
}

require_once '../includes/header.php';
?>

<div class="admin-container">
    <aside class="admin-sidebar">
        <nav>
            <ul>
                <li class="active"><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="manage-users.php"><i class="fas fa-users"></i> Kelola Pengguna</a></li>
                <li><a href="manage-articles.php"><i class="fas fa-newspaper"></i> Kelola Artikel</a></li>
                <li><a href="manage-lessons.php"><i class="fas fa-book-open"></i> Kelola Pelajaran</a></li>
                <li><a href="../index.php"><i class="fas fa-arrow-left"></i> Kembali ke Situs</a></li>
            </ul>
        </nav>
    </aside>
    
    <main class="admin-content">
        <h1><i class="fas fa-tachometer-alt"></i> Dashboard Admin</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>Total Pengguna</h3>
                    <p><?php echo $userCount; ?></p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-newspaper"></i>
                </div>
                <div class="stat-info">
                    <h3>Total Artikel</h3>
                    <p><?php echo $articleCount; ?></p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <div class="stat-info">
                    <h3>Total Pelajaran</h3>
                    <p><?php echo $lessonCount; ?></p>
                </div>
            </div>
        </div>
        
        <section class="recent-activity">
            <h2><i class="fas fa-history"></i> Aktivitas Terkini</h2>
            
            <div class="activity-list">
                <?php
                try {
                    $stmt = $conn->query("
                        SELECT 'article' AS type, a.title, a.created_at, u.username 
                        FROM articles a 
                        JOIN users u ON a.author_id = u.id 
                        ORDER BY a.created_at DESC 
                        LIMIT 5
                    ");
                    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach ($activities as $activity): ?>
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-newspaper"></i>
                            </div>
                            <div class="activity-details">
                                <p><strong><?php echo $activity['username']; ?></strong> membuat artikel baru: <strong><?php echo $activity['title']; ?></strong></p>
                                <small><?php echo date('d M Y H:i', strtotime($activity['created_at'])); ?></small>
                            </div>
                        </div>
                    <?php endforeach;
                } catch(PDOException $e) {
                    echo "<p>Gagal memuat aktivitas: " . $e->getMessage() . "</p>";
                }
                ?>
            </div>
        </section>
    </main>
</div>

<?php
require_once '../includes/footer.php';
?>