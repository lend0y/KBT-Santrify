<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../auth/login.php');
}

$pageTitle = 'Kelola Pengguna';

// Proses hapus pengguna
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $userId = intval($_GET['id']);
    
    try {
        // Jangan hapus akun yang sedang login
        if ($userId == $_SESSION['user_id']) {
            $error = "Anda tidak dapat menghapus akun yang sedang aktif.";
        } else {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            
            $success = "Pengguna berhasil dihapus.";
        }
    } catch(PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Ambil daftar pengguna
try {
    $stmt = $conn->query("SELECT * FROM users ORDER BY id DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error: " . $e->getMessage();
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
            <h1><i class="fas fa-users"></i> Kelola Pengguna</h1>
            <a href="add-user.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Tambah Pengguna Baru</a>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Foto</th>
                                <th>Username</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Tanggal Daftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($users) && count($users) > 0): ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo $user['id']; ?></td>
                                        <td>
                                            <img src="<?php echo BASE_URL; ?>assets/images/users/<?php echo $user['profile_pic']; ?>" alt="<?php echo $user['username']; ?>" class="user-img">
                                        </td>
                                        <td><?php echo $user['username']; ?></td>
                                        <td><?php echo $user['full_name']; ?></td>
                                        <td><?php echo $user['email']; ?></td>
                                        <td>
                                            <span class="badge <?php echo $user['role'] == 'admin' ? 'badge-admin' : 'badge-user'; ?>">
                                                <?php echo ucfirst($user['role']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                                        <td class="actions">
                                            <a href="view-user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-info" title="Lihat Detail"><i class="fas fa-eye"></i></a>
                                            <a href="edit-user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                            <a href="manage-users.php?action=delete&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus pengguna ini?');"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data pengguna.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
/* Tambahan CSS untuk halaman Kelola Pengguna */
.content-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.card {
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    margin-bottom: 20px;
}

.card-body {
    padding: 20px;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th, .table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.table th {
    background-color: #f9f9f9;
    font-weight: 600;
    color: var(--secondary-color);
}

.table tr:hover {
    background-color: #f5f5f5;
}

.user-img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #eee;
}

.badge {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.badge-admin {
    background-color: var(--primary-color);
    color: white;
}

.badge-user {
    background-color: var(--light-color);
    color: var(--dark-color);
}

.actions {
    display: flex;
    gap: 5px;
}

.btn-sm {
    padding: 5px 10px;
    font-size: 0.8rem;
}

.btn-info {
    background-color: var(--info-color);
    color: white;
    border: none;
}

.btn-warning {
    background-color: var(--warning-color);
    color: var(--dark-color);
    border: none;
}

.btn-danger {
    background-color: var(--danger-color);
    color: white;
    border: none;
}
</style>

<?php
require_once '../includes/footer.php';
?>