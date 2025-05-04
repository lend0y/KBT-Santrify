<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ' . SITE_NAME : SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo-text">
                <a href="<?php echo BASE_URL; ?>">SANTRIFY</a>
            <div class="tagline">Platform Belajar Qur'an</div>
            </div>
            <nav>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>"><i class="fas fa-home"></i> Beranda</a></li>
                    <li><a href="<?php echo BASE_URL; ?>modules/quran/"><i class="fas fa-book-quran"></i> Al-Qur'an</a></li>
                    <li><a href="<?php echo BASE_URL; ?>modules/hadits/"><i class="fas fa-book"></i> Hadits</a></li>
                    <li><a href="<?php echo BASE_URL; ?>modules/fiqh/"><i class="fas fa-balance-scale"></i> Fiqih</a></li>
                    <li><a href="<?php echo BASE_URL; ?>pages/about.php">Tentang</a></li>
                    <li><a href="<?php echo BASE_URL; ?>pages/contact.php"><i class="fas fa-envelope"></i> Kontak</a></li>
                </ul>
            </nav>
            <div class="auth-buttons">
                <?php if (isLoggedIn()): ?>
                    <div class="user-dropdown">
                        <img src="<?php echo BASE_URL; ?>assets/images/users/<?php echo $_SESSION['user_profile_pic']; ?>" alt="Profile" class="profile-pic">
                        <div class="dropdown-content">
                            <a href="<?php echo BASE_URL; ?>users/profile.php"><i class="fas fa-user"></i> Profil</a>
                            <?php if (isAdmin()): ?>
                                <a href="<?php echo BASE_URL; ?>admin/dashboard.php"><i class="fas fa-cog"></i> Admin</a>
                            <?php endif; ?>
                            <a href="<?php echo BASE_URL; ?>users/settings.php"><i class="fas fa-cog"></i> Pengaturan</a>
                            <a href="<?php echo BASE_URL; ?>auth/logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>auth/login.php" class="btn btn-outline"><i class="fas fa-sign-in-alt"></i> Masuk</a>
                    <a href="<?php echo BASE_URL; ?>register.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <main class="container"></main>