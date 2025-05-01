<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$pageTitle = 'Semua Artikel';
$articles = getArticles(); // Ambil semua artikel
require_once 'includes/header.php';
?>

<section class="all-articles">
    <h1><i class="fas fa-newspaper"></i> Semua Artikel</h1>
    <div class="articles-list">
        <?php foreach ($articles as $article): ?>
            <!-- Tampilkan artikel -->
        <?php endforeach; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>