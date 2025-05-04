<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
$pageTitle = 'Pilihan Belajar';
require_once '../includes/header.php';
?>

<section class="services-section">
    <div class="container">
        <h1><i class="fas fa-book-open"></i> Pilihan Program Belajar</h1>
        
        <div class="services-grid">
            <!-- Paket 1 -->
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-book-quran"></i>
                </div>
                <h3>Tahsin Qur'an</h3>
                <ul>
                    <li>Belajar membaca Al-Qur'an</li>
                    <li>Koreksi tajwid</li>
                    <li>Guru bersertifikat</li>
                </ul>
                <div class="service-price">Rp 299.000/bulan</div>
                <a href="<?php echo BASE_URL; ?>payments/confirm-order.php?package=tahsin" class="btn btn-primary">Daftar Sekarang</a>
            </div>
            
            <!-- Paket 2 -->
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-hands-praying"></i>
                </div>
                <h3>Tahfidz Intensive</h3>
                <ul>
                    <li>Hafalan per 10 ayat</li>
                    <li>Setoran harian</li>
                    <li>Bimbingan musyrif</li>
                </ul>
                <div class="service-price">Rp 499.000/bulan</div>
                <a href="<?php echo BASE_URL; ?>payments/confirm-order.php?package=tahfidz" class="btn btn-primary">Daftar Sekarang</a>
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>