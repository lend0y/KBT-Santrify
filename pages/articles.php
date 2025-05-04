<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

$pageTitle = 'Semua Artikel';
$articles = getArticles(100); // Get all articles with a high limit
require_once '../includes/header.php';
?>

<section class="all-articles">
    <div class="container">
        <h1><i class="fas fa-newspaper"></i> Semua Artikel</h1>
        
        <div class="articles-grid">
            <?php if (!empty($articles)): ?>
                <?php foreach ($articles as $article): ?>
                <article class="article-card">
                    <div class="article-category <?php echo $article['category']; ?>">
                        <?php echo ucfirst($article['category']); ?>
                    </div>
                    <h3><a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $article['id']; ?>"><?php echo $article['title']; ?></a></h3>
                    <div class="article-meta">
                        <img src="<?php echo BASE_URL; ?>assets/images/users/<?php echo $article['profile_pic']; ?>" alt="<?php echo $article['full_name']; ?>">
                        <span><?php echo $article['full_name']; ?></span>
                        <span><?php echo date('d M Y', strtotime($article['created_at'])); ?></span>
                    </div>
                    <p><?php echo substr(strip_tags($article['content']), 0, 150); ?>...</p>
                    <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $article['id']; ?>" class="read-more">Baca Selengkapnya</a>
                </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Belum ada artikel yang tersedia.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>