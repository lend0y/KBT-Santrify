<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$pageTitle = 'Daftar';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = sanitize($_POST['full_name']);
    
    // Validation
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Username harus diisi.";
    } elseif (strlen($username) < 4) {
        $errors[] = "Username minimal 4 karakter.";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email tidak valid.";
    }
    
    if (empty($password)) {
        $errors[] = "Password harus diisi.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Password dan konfirmasi password tidak sama.";
    }
    
    // Check if username or email exists
    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $errors[] = "Username atau email sudah digunakan.";
            }
        } catch(PDOException $e) {
            $errors[] = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
    
    // If no errors, register user
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name) VALUES (:username, :email, :password, :full_name)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':full_name', $full_name);
            $stmt->execute();
            
            $success = "Pendaftaran berhasil! Silakan login.";
        } catch(PDOException $e) {
            $errors[] = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}

require_once 'includes/header.php';
?>

<section class="auth-form">
    <div class="auth-container">
        <h2><i class="fas fa-user-plus"></i> Buat Akun Baru</h2>
        
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
            <div class="text-center">
                <a href="login.php" class="btn btn-primary">Login Sekarang</a>
            </div>
        <?php else: ?>
            <form action="register.php" method="POST">
                <div class="form-group">
                    <label for="full_name"><i class="fas fa-user"></i> Nama Lengkap</label>
                    <input type="text" id="full_name" name="full_name" required>
                </div>
                <div class="form-group">
                    <label for="username"><i class="fas fa-at"></i> Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password"><i class="fas fa-lock"></i> Konfirmasi Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Daftar</button>
            </form>
            
            <div class="auth-footer">
                <p>Sudah punya akun? <a href="login.php">Masuk sekarang</a></p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
require_once 'includes/footer.php';
?>