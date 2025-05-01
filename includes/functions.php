<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}

function sanitize($data) {
    global $conn;
    return htmlspecialchars(strip_tags(trim($data)));
}

function getArticles($limit = 5, $category = null) {
    global $conn;
    
    $sql = "SELECT a.*, u.full_name, u.profile_pic 
            FROM articles a 
            JOIN users u ON a.author_id = u.id";
            
    if ($category) {
        $sql .= " WHERE a.category = :category";
    }
    
    $sql .= " ORDER BY a.created_at DESC LIMIT :limit";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    
    if ($category) {
        $stmt->bindParam(':category', $category);
    }
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getArticlesByCategory($category) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM articles WHERE category = ? ORDER BY created_at DESC");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>  