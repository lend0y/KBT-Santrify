<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../auth/login.php');
}

$pageTitle = 'Edit Pengguna';

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
} catch(PDOException $e) {
    $error = "Error: " . $e->getMessage();
    redirect('manage-users.php');
}

// Proses form edit pengguna
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $full_name = sanitize($_POST['full_name']);
    $role = sanitize($_POST['role']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Username harus diisi.";
    } elseif (strlen($username) < 4) {
        $errors[] = "Username minimal 4 karakter.";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email tidak valid.";
    }
    
    // Check if username or email exists (except for current user)
    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("SELECT id FROM users WHERE (username = :username OR email = :email) AND id != :id");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $errors[] = "Username atau email sudah digunakan.";
            }
        } catch(PDOException $e) {
            $errors[] = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
    
    // Validasi password jika diisi
    if (!empty($new_password)) {
        if (strlen($new_password) < 6) {
            $errors[] = "Password minimal 6 karakter.";
        } elseif ($new_password !== $confirm_password) {
            $errors[] = "Password dan konfirmasi password tidak sama.";
        }
    }
    
    // If no errors, update user
    if (empty($errors)) {
        try {
            if (!empty($new_password)) {
                // Update dengan password baru
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                $stmt = $conn->prepare("UPDATE users SET username = :username, email = :email, password = :password, full_name = :full_name, role = :role WHERE id = :id");
                $stmt->bindParam(':password', $hashed_password);
            } else {
                // Update tanpa password baru
                $stmt = $conn->prepare("UPDATE users SET username = :username, email = :email, full_name = :full_name, role = :role WHERE id = :id");
            }
            
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            
            $success = "Data pengguna berhasil diperbarui.";
            
            // Refresh user data
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            $errors[] = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
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
            <h1><i class="fas fa-user-edit"></i> Edit Pengguna</h1>
            <a href="manage-users.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
        
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
        
        <div class="card">
            <div class="card-body">
                <div class="user-profile-header">
                    <div class="user-avatar">
                        <img src="<?php echo BASE_URL; ?>assets/images/users/<?php echo $user['profile_pic']; ?>" alt="<?php echo $user['username']; ?>">
                    </div>
                    <div class="user-info">
                        <h2><?php echo $user['full_name']; ?></h2>
                        <p class="user-since">User ID: <?php echo $user['id']; ?> | Bergabung: <?php echo date('d M Y', strtotime($user['created_at'])); ?></p>
                    </div>
                </div>
                
                <form action="edit-user.php?id=<?php echo $userId; ?>" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="username"><i class="fas fa-user"></i> Username</label>
                            <input type="text" id="username" name="username" value="<?php echo $user['username']; ?>" required>
                            <small>Username minimal 4 karakter, tidak boleh menggunakan spasi.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope"></i> Email</label>
                            <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="new_password"><i class="fas fa-lock"></i> Password Baru</label>
                            <input type="password" id="new_password" name="new_password">
                            <small>Kosongkan jika tidak ingin mengubah password. Minimal 6 karakter.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password"><i class="fas fa-lock"></i> Konfirmasi Password Baru</label>
                            <input type="password" id="confirm_password" name="confirm_password">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="full_name"><i class="fas fa-id-card"></i> Nama Lengkap</label>
                            <input type="text" id="full_name" name="full_name" value="<?php echo $user['full_name']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="role"><i class="fas fa-user-tag"></i> Role</label>
                            <select id="role" name="role" required>
                                <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                        <a href="manage-users.php" class="btn btn-outline">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<style>
/* Tambahan CSS untuk halaman Edit Pengguna */
.user-profile-header {
    display: flex;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #eee;
}

.user-avatar {
    margin-right: 20px;
}

.user-avatar img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 5px solid var(--light-color);
}

.user-info h2 {
    margin-bottom: 5px;
    color: var(--secondary-color);
}

.user-since {
    color: #666;
    font-size: 0.9rem;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--dark-color);
}

.form-group label i {
    margin-right: 8px;
    color: var(--primary-color);
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: var(--primary-color);
}

.form-group small {
    display: block;
    margin-top: 5px;
    color.form-group small {
    display: block;
    margin-top: 5px;
    color: #666;
    font-size: 0.85rem;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

@media (max-width: 768px) {
    .user-profile-header {
        flex-direction: column;
        text-align: center;
    }
    
    .user-avatar {
        margin-right: 0;
        margin-bottom: 15px;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .form-actions .btn {
        width: 100%;
    }
}
</style>

<?php
require_once '../includes/footer.php';
?>