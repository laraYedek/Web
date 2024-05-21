<?php require "includes/header.php"; ?>
<?php require "config/config.php"; ?>

<?php
$query = "SELECT
    topics.id AS id,
    topics.title AS title,
    categories.name AS category_name,
    topics.user_name AS user_name,
    topics.user_image AS user_image,
    topics.created_at AS created_at,
    COUNT(replies.topic_id) AS count_replies
FROM
    topics
LEFT JOIN
    replies ON topics.id = replies.topic_id
LEFT JOIN
    categories ON topics.category = categories.id
GROUP BY
    topics.id, topics.title, categories.name, topics.user_name, topics.user_image, topics.created_at";

$result = pg_query($conn, $query);

if (!$result) {
    echo "Sorgu çalıştırma hatası: " . pg_last_error($conn);
    exit;
}

$allTopics = pg_fetch_all($result);
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
                            <?php foreach($allTopics as $topic) : ?>
                            <li class="topic">
                                <div class="row">
                                    <div class="col-md-2">
                                        <?php
                                        $user_image = 'topics/profilePicture.jpg';
                                        ?>
                                        <img class="avatar pull-left" src="<?php echo $user_image; ?>" />
                                    </div>
                                    <div class="col-md-10">
                                        <div class="topic-content pull-right">
                                            <h3><a href="topics/topic.php?id=<?php echo htmlspecialchars($topic['id']); ?>"><?php echo htmlspecialchars($topic['title']); ?></a></h3>
                                            <div class="topic-info">
                                              <?php echo htmlspecialchars($topic['category_name']); ?></a> >> <?php echo htmlspecialchars($topic['user_name']); ?></a> >> Gönderildi: <?php echo htmlspecialchars($topic['created_at']); ?>
                                                <span class="badge color pull-right"><?php echo htmlspecialchars($topic['count_replies']); ?></span>
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

        <div class="col-md-4">
            <div class="sidebar">
                <div class="block">
                    <h3>Kategoriler</h3>
                    <div class="list-group block">
                        <?php
                        // Her kategori için gönderi sayısı
                        $result = pg_query($conn, "SELECT categories.name AS name,
                            COUNT(topics.category) AS count_category 
                            FROM categories 
                            LEFT JOIN topics ON categories.id = topics.category
                            GROUP BY categories.name;");
                        if (!$result) {
                            echo "Sorgu hatası: " . pg_last_error($conn);
                            exit;
                        }
                        $allCategories = pg_fetch_all($result);

                        // Toplam konu sayısı
                        $result = pg_query($conn, "SELECT COUNT(*) AS all_topics FROM topics");
                        if (!$result) {
                            echo "Sorgu hatası: " . pg_last_error($conn);
                            exit;
                        }
                        $allTopicsCount = pg_fetch_object($result);

                        // Kullanıcı sayısı
                        $result = pg_query($conn, "SELECT COUNT(*) AS count_users FROM kullanicilar");
                        if (!$result) {
                            echo "Sorgu hatası: " . pg_last_error($conn);
                            exit;
                        }
                        $allUsers = pg_fetch_object($result);

                        // Toplam kategori sayısı
                        $result = pg_query($conn, "SELECT COUNT(*) AS count_categories FROM categories");
                        if (!$result) {
                            echo "Sorgu hatası: " . pg_last_error($conn);
                            exit;
                        }
                        $allCategoriesCount = pg_fetch_object($result);
                        ?>

                        <a href="#" class="list-group-item active">Tüm Konular <span class="badge pull-right"><?php echo $allTopicsCount->all_topics; ?></span></a> 
                        <?php if ($allCategories): ?>
                            <?php foreach($allCategories as $category) : ?>
                            <a href="categories/show.php?name=<?php echo htmlspecialchars($category['name']); ?>" class="list-group-item"><?php echo htmlspecialchars($category['name']); ?><span class="badge pull-right"><?php echo $category['count_category']; ?></span></a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="block" style="margin-top: 20px;">
                    <h3>Forum Bilgileri</h3>
                    <div class="list-group">
                        <a href="#" class="list-group-item">Toplam kullanıcı sayısı:<span class="badge pull-right"><?php echo $allUsers->count_users; ?></span></a>
                        <a href="#" class="list-group-item">Toplam konu sayısı:<span class="badge pull-right"><?php echo $allTopicsCount->all_topics; ?></span></a>
                        <a href="/FitCheck/forum/categories/show.php" class="list-group-item">Toplam kategori sayısı: <span class="badge pull-right"><?php echo $allCategoriesCount->count_categories; ?></span></a>                          
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><!-- /.container -->

<?php require "includes/footer.php"; ?>
