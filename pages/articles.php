<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

$pageTitle = 'Semua Artikel';
$articles = getArticles(); // Ambil semua artikel
require_once '../includes/header.php';
?>

<section class="all-articles">
    <h1><i class="fas fa-newspaper"></i> Semua Artikel</h1>
    <div class="articles-list">
        <?php if (!empty($articles)): ?>
            <?php foreach ($articles as $article): ?>
                <div class="article-card">
                    <h2><?php echo htmlspecialchars($article['title']); ?></h2>
                    <p class="date"><?php echo date('d M Y', strtotime($article['published_at'])); ?></p>
                    <p><?php echo nl2br(htmlspecialchars(substr($article['content'], 0, 150))) . '...'; ?></p>
                    <a href="view-article.php?id=<?php echo $article['id']; ?>" class="read-more">Baca Selengkapnya</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Tidak ada artikel yang tersedia.</p>
        <?php endif; ?>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
