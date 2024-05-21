<?php require "../includes/header.php"; ?>
<?php require "../config/config.php"; ?>

<?php
if(isset($_GET['name'])) {
    $name = $_GET['name'];

    // Veritabanı sorgusunu yapıyoruz
    $query = "SELECT topics.*, categories.name AS category_name FROM topics
              LEFT JOIN categories ON topics.category = categories.id
              WHERE categories.name = $1";
    $result = pg_query_params($conn, $query, array($name));

    if (!$result) {
        echo "Sorgu çalıştırma hatası: " . pg_last_error($conn);
        exit;
    }

    $allTopics = pg_fetch_all($result);
} else {
    echo "Kategori belirtilmedi.";
    exit;
}
?>

<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="main-col">
                <div class="block">
                    <h1 class="pull-left">Foruma Hoş Geldiniz</h1>
                    <h4 class="pull-right">Forum</h4>
                    <div class="clearfix"></div>
                    <hr>
                    <ul id="topics">
                        <?php if ($allTopics): ?>
                            <?php foreach($allTopics as $topic): ?>
                            <li class="topic">
                                <div class="row">
                                    <div class="col-md-2">
                                        <img class="avatar pull-left" src="/FitCheck/forum/img/<?php echo htmlspecialchars($topic['user_image']); ?>" />
                                    </div>
                                    <div class="col-md-10">
                                        <div class="topic-content pull-right">
                                            <h3><a href="/FitCheck/forum/topics/topic.php?id=<?php echo htmlspecialchars($topic['id']); ?>"><?php echo htmlspecialchars($topic['title']); ?></a></h3>
                                            <div class="topic-info">
                                                <a href="/FitCheck/forum/categories/show.php?name=<?php echo htmlspecialchars($topic['category_name']); ?>"><?php echo htmlspecialchars($topic['category_name']); ?></a> >> <a href="/FitCheck/forum/user/profile.php?name=<?php echo htmlspecialchars($topic['user_name']); ?>"><?php echo htmlspecialchars($topic['user_name']); ?></a> >> <?php echo htmlspecialchars($topic['created_at']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Hiç konu bulunamadı.</p>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require "../includes/footer.php"; ?>
