<?php require "../includes/header.php"; ?>
<?php require "../config/config.php"; ?>

<?php
if (!isset($_SESSION['username'])) {
    header("Location: /FitCheck/forum/index.php");
    exit;
}

// Veriyi çekme işlemi
if (isset($_GET['name'])) {
    $name = $_GET['name'];

    // Kullanıcı bilgilerini çekme işlemi
    $query = "SELECT * FROM users WHERE username = $1";
    $result = pg_query_params($conn, $query, array($name));

    if ($result && pg_num_rows($result) > 0) {
        $user = pg_fetch_object($result);

        // Gönderi sayısı
        $query = "SELECT COUNT(*) AS num_topics FROM topics WHERE user_name = $1";
        $result = pg_query_params($conn, $query, array($name));
        $all_num_topics = pg_fetch_object($result);

        // Yanıt sayısı
        $query = "SELECT COUNT(*) AS num_replies FROM replies WHERE user_name = $1";
        $result = pg_query_params($conn, $query, array($name));
        $all_num_replies = pg_fetch_object($result);
    } else {
        // Kullanıcı bulunamazsa ana sayfaya yönlendirme
        header("Location: /FitCheck/forum/index.php");
        exit;
    }
} else {
    header("Location: /FitCheck/forum/index.php");
    exit;
}
?>

<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="main-col">
                <div class="block">
                    <h1 class="pull-left"><?php echo htmlspecialchars($user->name); ?></h1>
                    <h4 class="pull-right">Forum</h4>
                    <div class="clearfix"></div>
                    <hr>
                    <ul id="topics">
                        <li id="main-topic" class="topic topic">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="user-info">
                                        <img class="avatar pull-left" src="../img/<?php echo htmlspecialchars($user->avatar); ?>" />
                                        <ul>
                                            <li><strong><?php echo htmlspecialchars($user->username); ?></strong></li>
                                            <li><a href="profile.php?name=<?php echo htmlspecialchars($user->username); ?>">Profil</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    <div class="topic-content pull-right">
                                        <p><?php echo htmlspecialchars($user->about); ?></p>
                                    </div>
                                    <a class="btn btn-success" href="#" role="button">Gönderi Sayısı: <?php echo htmlspecialchars($all_num_topics->num_topics); ?></a>
                                    <a class="btn btn-primary" href="#" role="button">Yanıt Sayısı: <?php echo htmlspecialchars($all_num_replies->num_replies); ?></a>
                                </div>
                            </div>
                        </li>
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
                        $result = pg_query($conn, "SELECT COUNT(*) AS count_users FROM users");
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

                        <a href="#" class="list-group-item active">Tüm Konular <span class="badge pull-right"><?php echo htmlspecialchars($allTopicsCount->all_topics); ?></span></a> 
                        <?php if ($allCategories): ?>
                            <?php foreach ($allCategories as $category) : ?>
                            <a href="categories/show.php?name=<?php echo htmlspecialchars($category['name']); ?>" class="list-group-item"><?php echo htmlspecialchars($category['name']); ?><span class="badge pull-right"><?php echo htmlspecialchars($category['count_category']); ?></span></a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="block" style="margin-top: 20px;">
                    <h3>Forum Bilgileri</h3>
                    <div class="list-group">
                        <a href="#" class="list-group-item">Toplam Kullanıcı Sayısı: <span class="badge pull-right"><?php echo htmlspecialchars($allUsers->count_users); ?></span></a>
                        <a href="#" class="list-group-item">Toplam Konu Sayısı: <span class="badge pull-right"><?php echo htmlspecialchars($allTopicsCount->all_topics); ?></span></a>
                        <a href="#" class="list-group-item">Toplam Kategori Sayısı: <span class="badge pull-right"><?php echo htmlspecialchars($allCategoriesCount->count_categories); ?></span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap core JavaScript -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="/FitCheck/forum/js/bootstrap.js"></script>
<?php require "../includes/footer.php"; ?>
