<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$pageTitle = 'Beranda';
$articles = getArticles(3);

require_once 'includes/header.php';
?>

<section class="hero">
    <div class="hero-content">
        <h2>Belajar Agama Islam dengan Mudah</h2>
        <p>Temukan berbagai materi pembelajaran Al-Qur'an, Hadits, Fiqih, dan Akhlak dalam satu platform.</p>
        <a href="<?php echo BASE_URL; ?>services.php" class="btn btn-primary">Mulai Belajar Sekarang</a>
    </div>
</section>

<section class="features">
    <div class="feature">
        <i class="fas fa-book-quran"></i>
        <h3>Al-Qur'an</h3>
        <p>Belajar membaca, menghafal, dan memahami Al-Qur'an dengan metode yang mudah.</p>
    </div>
    <div class="feature">
        <i class="fas fa-book"></i>
        <h3>Hadits</h3>
        <p>Pelajari hadits-hadits Nabi Muhammad SAW dengan penjelasan yang lengkap.</p>
    </div>
    <div class="feature">
        <i class="fas fa-balance-scale"></i>
        <h3>Fiqih</h3>
        <p>Pahami hukum-hukum Islam dalam kehidupan sehari-hari.</p>
    </div>
</section>

<section class="latest-articles">
    <h2><i class="fas fa-newspaper"></i> Artikel Terbaru</h2>
    <div class="articles-grid">
        <?php foreach ($articles as $article): ?>
        <article class="article-card">
            <div class="article-category <?php echo $article['category']; ?>">
                <?php echo ucfirst($article['category']); ?>
            </div>
            <h3><a href="article.php?id=<?php echo $article['id']; ?>"><?php echo $article['title']; ?></a></h3>
            <div class="article-meta">
                <img src="<?php echo BASE_URL; ?>assets/images/users/<?php echo $article['profile_pic']; ?>" alt="<?php echo $article['full_name']; ?>">
                <span><?php echo $article['full_name']; ?></span>
                <span><?php echo date('d M Y', strtotime($article['created_at'])); ?></span>
            </div>
            <p><?php echo substr($article['content'], 0, 150); ?>...</p>
            <a href="article.php?id=<?php echo $article['id']; ?>" class="read-more">Baca Selengkapnya</a>
        </article>
        <?php endforeach; ?>
    </div>
    <a href="articles.php" class="btn btn-outline">Lihat Semua Artikel</a>
</section>

<section class="testimonials">
    <h2><i class="fas fa-comments"></i> Testimoni Santri</h2>
    <div class="testimonial-slider">
        <div class="testimonial">
            <img src="<?php echo BASE_URL; ?>assets/images/cowo.png" alt="Ahmad">
            <p>"Santriify sangat membantu saya dalam mempelajari agama Islam dengan lebih terstruktur."</p>
            <h4>Ahmad</h4>
            <span>Mahasiswa</span>
        </div>
        <div class="testimonial">
            <img src="<?php echo BASE_URL; ?>assets/images/cowo.png" alt="Siti">
            <p>"Materi yang disajikan mudah dipahami dan sangat bermanfaat untuk kehidupan sehari-hari."</p>
            <h4>Siti</h4>
            <span>Ibu Rumah Tangga</span>
        </div>
    </div>
</section>

<?php
require_once 'includes/footer.php';
?>