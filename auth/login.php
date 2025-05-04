<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$pageTitle = 'Masuk';

// Cek apakah kolom last_login sudah ada di tabel users
$last_login_exists = false;
try {
    $check_column = $conn->query("SHOW COLUMNS FROM users LIKE 'last_login'");
    $last_login_exists = ($check_column->rowCount() > 0);
} catch(PDOException $e) {
    // Kolom tidak ada, variabel last_login_exists sudah false
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username OR email = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        if ($stmt->rowCount() === 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_full_name'] = $user['full_name'];
                $_SESSION['user_profile_pic'] = $user['profile_pic'];
                $_SESSION['user_role'] = $user['role'];
                
                // Update last_login jika kolom tersedia
                if ($last_login_exists) {
                    $update_stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
                    $update_stmt->bindParam(':id', $user['id']);
                    $update_stmt->execute();
                }
                
                // Redirect to dashboard or previous page
                if (isset($_SESSION['redirect_url'])) {
                    $redirect_url = $_SESSION['redirect_url'];
                    unset($_SESSION['redirect_url']);
                    redirect($redirect_url);
                } else {
                    redirect('index.php');
                }
            } else {
                $error = "Username atau password salah.";
            }
        } else {
            $error = "Username atau password salah.";
        }
    } catch(PDOException $e) {
        $error = "Terjadi kesalahan: " . $e->getMessage();
    }
}

require_once '../includes/header.php';
?>

<section class="auth-form">
    <div class="auth-container">
        <h2><i class="fas fa-sign-in-alt"></i> Masuk ke Akun Anda</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> Username atau Email</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Masuk</button>
        </form>
        
        <div class="auth-footer">
            <p>Belum punya akun? <a href="register.php">Daftar sekarang</a></p>
        </div>
    </div>
</section>

<?php
require_once '../includes/footer.php';
?>