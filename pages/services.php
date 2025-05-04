<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
$pageTitle = 'Pilihan Belajar';
require_once '../includes/header.php';
?>

<section class="services-section">
    <div class="container">
        <h1 class="text-center"><i class="fas fa-book-open"></i> Pilihan Program Belajar</h1>
        
        <div class="services-grid">
            <!-- Paket 1 -->
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-book-quran"></i>
                </div>
                <h3>Tahsin Qur'an</h3>
                <ul>
                    <li><i class="fas fa-check text-success"></i> Belajar membaca Al-Qur'an</li>
                    <li><i class="fas fa-check text-success"></i> Koreksi tajwid</li>
                    <li><i class="fas fa-check text-success"></i> Guru bersertifikat</li>
                </ul>
                <div class="service-price">Rp 200.000/bulan</div>
                <a href="<?php echo BASE_URL; ?>payments/confirm-order.php?package=tahsin" class="btn btn-primary">Daftar Sekarang</a>
            </div>
            
            <!-- Paket 2 -->
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-hands-praying"></i>
                </div>
                <h3>Tahfidz Intensive</h3>
                <ul>
                    <li><i class="fas fa-check text-success"></i> Hafalan per 10 ayat</li>
                    <li><i class="fas fa-check text-success"></i> Setoran harian</li>
                    <li><i class="fas fa-check text-success"></i> Bimbingan musyrif</li>
                </ul>
                <div class="service-price">Rp 350.000/bulan</div>
                <a href="<?php echo BASE_URL; ?>payments/confirm-order.php?package=tahfidz" class="btn btn-primary">Daftar Sekarang</a>
            </div>
        </div>
    </div>
</section>

<style>
Inline CSS untuk memastikan tampilan benar
.services-section {
    padding: 60px 0;
    background-color: #f5f5f5;
}

.services-section h1 {
    margin-bottom: 40px;
    color: var(--secondary-color);
    font-size: 1.8rem;
    font-weight: 700;
}

.services-section h1 i {
    margin-right: 10px;
    color: var(--primary-color);
}

.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin-bottom: 40px;
}

.service-card {
    background-color: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    transition: transform 0.3s ease;
    text-align: center;
    padding: 30px;
}

.service-card:hover {
    transform: translateY(-10px);
}

.service-icon {
    width: 100px;
    height: 100px;
    background-color: #faf5e4;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 2.5rem;
    color: #2c786c;
}

.service-card h3 {
    margin-bottom: 20px;
    color: #004445;
    font-size: 1.3rem;
    font-weight: 600;
}

.service-card ul {
    list-style: none;
    padding: 0;
    margin-bottom: 25px;
    text-align: left;
}

.service-card ul li {
    padding: 10px 0;
    border-bottom: 1px dashed #eee;
    display: flex;
    align-items: center;
}

.service-card ul li i {
    margin-right: 10px;
    color: #28a745;
}

.service-card ul li:last-child {
    border-bottom: none;
}

.service-price {
    font-size: 1.5rem;
    font-weight: 700;
    color: #004445;
    margin-bottom: 20px;
}

.btn-primary {
    background-color: #2c786c;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 5px;
    font-weight: 600;
    display: inline-block;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background-color: #004445;
    transform: translateY(-3px);
}

.text-success {
    color: #28a745;
}

@media (max-width: 768px) {
    .services-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php require_once '../includes/footer.php'; ?>