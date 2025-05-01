<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

$pageTitle = 'Artikel Al-Qur\'an';
$articles = getArticlesByCategory('quran');
require_once '../../includes/header.php';
?>

<section class="category-articles">
    <h1><i class="fas fa-book-quran"></i> Artikel Al-Qur'an</h1>
    <?php foreach ($articles as $article): ?>
        <!-- Tampilkan artikel -->
    <?php endforeach; ?>
</section>

<?php require_once '../../includes/footer.php'; ?>