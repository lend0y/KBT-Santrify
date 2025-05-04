<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
$pageTitle = 'Tentang Kami';
require_once 'includes/header.php';
?>

<section class="about-section">
    <div class="container">
        <h1><i class="fas fa-info-circle"></i> Tentang <strong>Santrify</strong></h1>

        <div class="about-content">
            <div class="about-image">
                <img src="<?php echo BASE_URL; ?>assets/images/about-us.jpg" alt="Tim Santrify">
            </div>

            <div class="about-text">
                <h2>Mengapa Memilih Santrify?</h2>
                <p>
                    <strong>Santrify</strong> adalah platform pembelajaran agama Islam berbasis digital yang dirancang untuk mempermudah akses santri dan masyarakat umum dalam mempelajari Al-Qur'an, Hadits, Fiqih, dan Akhlak. Kami mengintegrasikan teknologi dan metode belajar interaktif untuk memberikan pengalaman belajar yang menyenangkan dan mudah dipahami.
                </p>

                <div class="about-features">
                    <div class="feature">
                        <i class="fas fa-user-graduate"></i>
                        <h3>10.000+ Santri</h3>
                        <p>Bergabung dan aktif belajar setiap hari</p>
                    </div>
                    <div class="feature">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <h3>50+ Pengajar</h3>
                        <p>Berpengalaman di bidang agama Islam</p>
                    </div>
                    <div class="feature">
                        <i class="fas fa-book"></i>
                        <h3>100+ Materi</h3>
                        <p>Lengkap & terus diperbarui</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
