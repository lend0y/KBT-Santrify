<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$pageTitle = 'Dashboard Admin';

// Cek apakah kolom last_login sudah ada
$last_login_exists = false;
try {
    $check_column = $conn->query("SHOW COLUMNS FROM users LIKE 'last_login'");
    $last_login_exists = ($check_column->rowCount() > 0);
} catch(PDOException $e) {
    // Kolom tidak ada, variabel last_login_exists sudah false
}

// Get stats
try {
    $userCount = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $articleCount = $conn->query("SELECT COUNT(*) FROM articles")->fetchColumn();
    $lessonCount = $conn->query("SELECT COUNT(*) FROM lessons")->fetchColumn();
    
    // Get active users count jika kolom last_login ada
    $activeUsersCount = 0;
    if ($last_login_exists) {
        $stmt = $conn->prepare("
            SELECT COUNT(*) FROM users 
            WHERE last_login > DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ");
        $stmt->execute();
        $activeUsersCount = $stmt->fetchColumn();
    }
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
            
            <?php if ($last_login_exists): ?>
            <div class="stat-card highlight">
                <div class="stat-icon">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div class="stat-info">
                    <h3>Pengguna Aktif</h3>
                    <p><?php echo $activeUsersCount; ?></p>
                    <small>dalam 24 jam terakhir</small>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <section class="recent-activity">
            <div class="section-header">
                <h2><i class="fas fa-history"></i> Aktivitas Terkini</h2>
                <div class="filter-options">
                    <select id="activity-filter">
                        <option value="all">Semua Aktivitas</option>
                        <option value="user">Pengguna</option>
                        <option value="article">Artikel</option>
                        <option value="lesson">Pelajaran</option>
                    </select>
                </div>
            </div>
            
            <div class="activity-list">
                <?php
                try {
                    // Get recent user activities - registrations, logins, etc.
                    $stmt = $conn->query("
                        SELECT 'user_register' AS type, u.username, u.full_name, u.created_at AS activity_time, 
                               'mendaftar sebagai pengguna baru' AS description, NULL AS title
                        FROM users u 
                        ORDER BY u.created_at DESC 
                        LIMIT 3
                    ");
                    $userActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Get recent article activities
                    $stmt = $conn->query("
                        SELECT 'article' AS type, a.title, a.created_at AS activity_time, u.username, u.full_name,
                               'membuat artikel baru' AS description
                        FROM articles a 
                        JOIN users u ON a.author_id = u.id 
                        ORDER BY a.created_at DESC 
                        LIMIT 3
                    ");
                    $articleActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Get recent lesson progress
                    $stmt = $conn->query("
                        SELECT 'lesson_complete' AS type, l.title, up.completed_at AS activity_time, 
                               u.username, u.full_name,
                               'menyelesaikan pelajaran' AS description
                        FROM user_progress up
                        JOIN users u ON up.user_id = u.id
                        JOIN lessons l ON up.lesson_id = l.id
                        WHERE up.completed = 1
                        ORDER BY up.completed_at DESC
                        LIMIT 3
                    ");
                    $lessonActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Combine and sort all activities by time
                    $allActivities = array_merge($userActivities, $articleActivities, $lessonActivities);
                    usort($allActivities, function($a, $b) {
                        return strtotime($b['activity_time']) - strtotime($a['activity_time']);
                    });
                    
                    // Display the 5 most recent activities
                    $activities = array_slice($allActivities, 0, 5);
                    
                    foreach ($activities as $activity): ?>
                        <div class="activity-item" data-type="<?php echo $activity['type']; ?>">
                            <div class="activity-icon">
                                <?php if (strpos($activity['type'], 'user') === 0): ?>
                                    <i class="fas fa-user-plus"></i>
                                <?php elseif ($activity['type'] === 'article'): ?>
                                    <i class="fas fa-newspaper"></i>
                                <?php elseif (strpos($activity['type'], 'lesson') === 0): ?>
                                    <i class="fas fa-book-open"></i>
                                <?php endif; ?>
                            </div>
                            <div class="activity-details">
                                <p><strong><?php echo $activity['username']; ?></strong> <?php echo $activity['description']; ?>
                                <?php if (isset($activity['title']) && !empty($activity['title'])): ?>
                                    : <strong><?php echo $activity['title']; ?></strong>
                                <?php endif; ?>
                                </p>
                                <small><?php echo date('d M Y H:i', strtotime($activity['activity_time'])); ?></small>
                            </div>
                        </div>
                    <?php endforeach;
                } catch(PDOException $e) {
                    echo "<p>Gagal memuat aktivitas: " . $e->getMessage() . "</p>";
                }
                
                if (empty($activities)): ?>
                    <div class="no-activity">
                        <i class="fas fa-info-circle"></i>
                        <p>Belum ada aktivitas untuk ditampilkan.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="view-more-activities">
                <a href="activities.php" class="btn btn-outline"><i class="fas fa-list"></i> Lihat Semua Aktivitas</a>
            </div>
        </section>
        
        <?php if ($last_login_exists): ?>
        <div class="dashboard-row">
            <section class="active-users">
                <h2><i class="fas fa-user-clock"></i> Pengguna Aktif Saat Ini</h2>
                <div class="active-users-list">
                    <?php
                    try {
                        // Get users who are currently online (last activity within 15 minutes)
                        $stmt = $conn->query("
                            SELECT u.username, u.full_name, u.profile_pic, u.last_login
                            FROM users u 
                            WHERE u.last_login > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
                            ORDER BY u.last_login DESC
                            LIMIT 5
                        ");
                        $activeUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if (!empty($activeUsers)):
                            foreach ($activeUsers as $user): ?>
                                <div class="active-user">
                                    <img src="<?php echo BASE_URL; ?>assets/images/users/<?php echo $user['profile_pic']; ?>" alt="<?php echo $user['username']; ?>">
                                    <div class="user-info">
                                        <h4><?php echo $user['full_name']; ?></h4>
                                        <p><?php echo $user['username']; ?></p>
                                    </div>
                                    <div class="active-status">
                                        <span class="status-indicator"></span>
                                    </div>
                                </div>
                            <?php endforeach;
                        else: ?>
                            <div class="no-users">
                                <i class="fas fa-user-slash"></i>
                                <p>Tidak ada pengguna yang aktif saat ini.</p>
                            </div>
                        <?php endif;
                    } catch(PDOException $e) {
                        echo "<p>Gagal memuat pengguna aktif: " . $e->getMessage() . "</p>";
                    }
                    ?>
                </div>
            </section>
            
            <section class="quick-actions">
                <h2><i class="fas fa-bolt"></i> Aksi Cepat</h2>
                <div class="actions-grid">
                    <a href="add-user.php" class="action-card">
                        <i class="fas fa-user-plus"></i>
                        <span>Tambah Pengguna</span>
                    </a>
                    <a href="add-article.php" class="action-card">
                        <i class="fas fa-newspaper"></i>
                        <span>Tulis Artikel</span>
                    </a>
                    <a href="add-lesson.php" class="action-card">
                        <i class="fas fa-book-open"></i>
                        <span>Tambah Pelajaran</span>
                    </a>
                    <a href="reports.php" class="action-card">
                        <i class="fas fa-chart-bar"></i>
                        <span>Lihat Laporan</span>
                    </a>
                </div>
            </section>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
            <p><i class="fas fa-info-circle"></i> Untuk mengaktifkan fitur Pengguna Aktif, tambahkan kolom <code>last_login</code> ke tabel users:</p>
            <pre>ALTER TABLE users ADD COLUMN last_login TIMESTAMP NULL DEFAULT NULL;</pre>
            <p>Kemudian perbarui kode login.php untuk memperbarui kolom ini saat pengguna login.</p>
        </div>
        <?php endif; ?>
        
        <style>
        /* Additional CSS for improved dashboard */
        .stat-card.highlight {
            border-left: 4px solid var(--primary-color);
        }
        
        .stat-card small {
            display: block;
            font-size: 0.8rem;
            color: #666;
            margin-top: 3px;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .filter-options select {
            padding: 8px 12px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 0.9rem;
        }
        
        .dashboard-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 30px;
        }
        
        .active-users, .quick-actions {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .active-users h2, .quick-actions h2 {
            font-size: 1.3rem;
            margin-bottom: 20px;
            color: var(--secondary-color);
        }
        
        .active-users-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .active-user {
            display: flex;
            align-items: center;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 8px;
        }
        
        .active-user img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
        }
        
        .user-info {
            flex: 1;
        }
        
        .user-info h4 {
            margin: 0;
            font-size: 0.95rem;
        }
        
        .user-info p {
            margin: 0;
            font-size: 0.85rem;
            color: #666;
        }
        
        .active-status {
            display: flex;
            align-items: center;
        }
        
        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #42b983;
            display: inline-block;
        }
        
        .actions-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .action-card {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            color: var(--dark-color);
        }
        
        .action-card:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-5px);
        }
        
        .action-card i {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .no-activity, .no-users {
            text-align: center;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            color: #666;
        }
        
        .no-activity i, .no-users i {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #ddd;
        }
        
        .view-more-activities {
            text-align: center;
            margin-top: 20px;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border-color: #bee5eb;
            padding: 15px;
            margin-top: 30px;
            border-radius: 5px;
        }
        
        .alert-info pre {
            background-color: rgba(255, 255, 255, 0.5);
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            overflow-x: auto;
        }
        
        .alert-info code {
            font-family: monospace;
            background-color: rgba(255, 255, 255, 0.5);
            padding: 2px 4px;
            border-radius: 3px;
        }
        
        @media (max-width: 768px) {
            .dashboard-row {
                grid-template-columns: 1fr;
            }
            
            .section-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .filter-options {
                margin-top: 10px;
                width: 100%;
            }
            
            .filter-options select {
                width: 100%;
            }
        }
        </style>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Activity filter functionality
            const filterSelect = document.getElementById('activity-filter');
            const activityItems = document.querySelectorAll('.activity-item');
            
            filterSelect.addEventListener('change', function() {
                const filterValue = this.value;
                
                activityItems.forEach(item => {
                    if (filterValue === 'all' || item.dataset.type.startsWith(filterValue)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
        </script>
    </main>
</div>

<?php
require_once '../includes/footer.php';
?>