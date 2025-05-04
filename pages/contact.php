<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
$pageTitle = 'Kontak Kami';
require_once '../includes/header.php';
?>

<section class="contact-section">
    <div class="container">
        <h1><i class="fas fa-envelope"></i> Hubungi Kami</h1>
        
        <div class="company-profile">
            <h2>Profil Perusahaan</h2>
            <div class="profile-card">
                <img src="<?php echo BASE_URL; ?>assets/images/logo.png" alt="Santrify">
                <div class="profile-info">
                    <h3>Santrify</h3>
                    <p>Platform belajar Al-Qur'an dan ilmu agama online terkemuka di Indonesia</p>
                    <div class="profile-details">
                        <p><i class="fas fa-map-marker-alt"></i> Jl. Tegalgondo, Kabupaten Malang</p>
                        <p><i class="fas fa-phone"></i> +62 123 4567 890</p>
                        <p><i class="fas fa-envelope"></i> info@santrify.com</p>
                    </div>
                </div>
            </div>
        </div>
</section>

<?php require_once '../includes/footer.php'; ?>