<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../auth/login.php');
}

$pageTitle = 'Detail Pengguna';

// Cek ID pengguna
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('manage-users.php');
}

$userId = intval($_GET['id']);

// Ambil data pengguna
try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userId);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        redirect('manage-users.php');
    }
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Ambil progres belajar pengguna
    $progressStmt = $conn->prepare("
        SELECT l.title, l.duration, up.completed, up.completed_at 
        FROM user_progress up
        JOIN lessons l ON up.lesson_id = l.id
        WHERE up.user_id = :user_id
        ORDER BY up.completed_at DESC
        LIMIT 5
    ");
    $progressStmt->bindParam(':user_id', $userId);
    $progressStmt->execute();
    $progress = $progressStmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    $error = "Error: " . $e->getMessage();
    redirect('manage-users.php');
}

require_once '../includes/header.php';
?>

<div class="admin-container">
    <aside class="admin-sidebar">
        <nav>
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li class="active"><a href="manage-users.php"><i class="fas fa-users"></i> Kelola Pengguna</a></li>
                <li><a href="manage-articles.php"><i class="fas fa-newspaper"></i> Kelola Artikel</a></li>
                <li><a href="manage-lessons.php"><i class="fas fa-book-open"></i> Kelola Pelajaran</a></li>
                <li><a href="../index.php"><i class="fas fa-arrow-left"></i> Kembali ke Situs</a></li>
            </ul>
        </nav>
    </aside>
    
    <main class="admin-content">
        <div class="content-header">
            <h1><i class="fas fa-user"></i> Detail Pengguna</h1>
            <div class="action-buttons">
                <a href="edit-user.php?id=<?php echo $userId; ?>" class="btn btn-warning"><i class="fas fa-edit"></i> Edit</a>
                <a href="manage-users.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Kembali</a>
            </div>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="user-profile">
            <div class="profile-header">
                <div class="profile-avatar">
                    <img src="<?php echo BASE_URL; ?>assets/images/users/<?php echo $user['profile_pic']; ?>" alt="<?php echo $user['username']; ?>">
                </div>
                <div class="profile-info">
                    <h2><?php echo $user['full_name']; ?></h2>
                    <span class="badge <?php echo $user['role'] == 'admin' ? 'badge-admin' : 'badge-user'; ?>">
                        <?php echo ucfirst($user['role']); ?>
                    </span>
                    <p class="profile-since">Bergabung sejak <?php echo date('d M Y', strtotime($user['created_at'])); ?></p>
                </div>
            </div>
            
            <div class="profile-details">
                <div class="detail-card">
                    <h3>Informasi Akun</h3>
                    <div class="detail-item">
                        <span class="label">ID Pengguna:</span>
                        <span class="value"><?php echo $user['id']; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Username:</span>
                        <span class="value"><?php echo $user['username']; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Email:</span>
                        <span class="value"><?php echo $user['email']; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Nama Lengkap:</span>
                        <span class="value"><?php echo $user['full_name']; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Role:</span>
                        <span class="value"><?php echo ucfirst($user['role']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Terakhir Diperbarui:</span>
                        <span class="value"><?php echo date('d M Y H:i', strtotime($user['updated_at'])); ?></span>
                    </div>
                </div>
                
                <div class="detail-card">
                    <h3>Bio</h3>
                    <div class="bio-content">
                        <?php if (!empty($user['bio'])): ?>
                            <p><?php echo nl2br($user['bio']); ?></p>
                        <?php else: ?>
                            <p class="text-muted">Pengguna belum menambahkan bio.</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="detail-card">
                    <h3>Progress Belajar</h3>
                    <?php if (!empty($progress)): ?>
                        <div class="progress-list">
                            <?php foreach ($progress as $item): ?>
                                <div class="progress-item <?php echo $item['completed'] ? 'completed' : ''; ?>">
                                    <div class="progress-title">
                                        <h4><?php echo $item['title']; ?></h4>
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
                        <a href="../users/profile.php?id=<?php echo $userId; ?>" class="view-more">Lihat semua progress <i class="fas fa-arrow-right"></i></a>
                    <?php else: ?>
                        <p class="text-muted">Pengguna belum memiliki progress belajar.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
/* CSS untuk Detail Pengguna */
.content-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.action-buttons {
    display: flex;
    gap: 10px;
}

.user-profile {
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.profile-header {
    display: flex;
    align-items: center;
    background-color: var(--secondary-color);
    color: white;
    padding: 30px;
}

.profile-avatar {
    margin-right: 30px;
}

.profile-avatar img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 5px solid rgba(255, 255, 255, 0.2);
}

.profile-info h2 {
    margin-bottom: 10px;
    color: white;
}

.badge {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    margin-bottom: 10px;
    display: inline-block;
}

.badge-admin {
    background-color: var(--accent-color);
    color: var(--dark-color);
}

.badge-user {
    background-color: rgba(255, 255, 255, 0.2);
    color: white;
}

.profile-since {
    font-size: 0.9rem;
    opacity: 0.8;
}

.profile-details {
    padding: 30px;
}

.detail-card {
    background-color: #f9f9f9;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
}

.detail-card h3 {
    color: var(--secondary-color);
    font-size: 1.2rem;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px dashed #eee;
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-item .label {
    font-weight: 600;
    color: var(--dark-color);
}

.detail-item .value {
    color: #666;
}

.bio-content {
    padding: 10px;
}

.text-muted {
    color: #666;
    font-style: italic;
}

.progress-list {
    margin-top: 10px;
}

.progress-item {
    padding: 15px;
    background-color: white;
    border-radius: 8px;
    margin-bottom: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.progress-item.completed {
    border-left: 3px solid var(--success-color);
}

.progress-item:not(.completed) {
    border-left: 3px solid var(--warning-color);
}

.progress-title {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
}

.progress-title h4 {
    margin: 0;
    font-size: 1rem;
    color: var(--dark-color);
}

.progress-status .completed {
    color: var(--success-color);
    font-size: 0.9rem;
    display: flex;
    align-items: center;
}

.progress-status .completed i,
.progress-status .in-progress i {
    margin-right: 5px;
}

.progress-status .in-progress {
    color: var(--warning-color);
    font-size: 0.9rem;
    display: flex;
    align-items: center;
}

.view-more {
    display: block;
    margin-top: 15px;
    text-align: right;
    color: var(--primary-color);
    font-weight: 500;
    transition: all 0.3s ease;
}

.view-more i {
    margin-left: 5px;
    transition: transform 0.3s ease;
}

.view-more:hover {
    color: var(--secondary-color);
}

.view-more:hover i {
    transform: translateX(3px);
}

@media (max-width: 768px) {
    .profile-header {
        flex-direction: column;
        text-align: center;
    }
    
    .profile-avatar {
        margin-right: 0;
        margin-bottom: 20px;
    }
    
    .detail-item {
        flex-direction: column;
    }
    
    .detail-item .label {
        margin-bottom: 5px;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .action-buttons .btn {
        width: 100%;
        margin-bottom: 10px;
    }
}
</style>

<?php
require_once '../includes/footer.php';
?>