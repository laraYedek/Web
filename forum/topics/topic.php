<?php require "../includes/header.php"; ?>
<?php require "../config/config.php"; ?>

<?php
$id = $_GET['id'];

// Konuyu çekme işlemi
$query = "SELECT * FROM topics WHERE id = $1";
$result = pg_query_params($conn, $query, array($id));

if (!$result) {
    echo "Sorgu çalıştırma hatası: " . pg_last_error($conn);
    exit;
}

$singleTopic = pg_fetch_object($result);

// Her kullanıcı için post sayısı
$query = "SELECT COUNT(*) AS count_topics FROM topics WHERE user_name = $1";
$result = pg_query_params($conn, $query, array($singleTopic->user_name));

if (!$result) {
    echo "Sorgu çalıştırma hatası: " . pg_last_error($conn);
    exit;
}

$count = pg_fetch_object($result);

// Yanıtlar
$query = "SELECT * FROM replies WHERE topic_id = $1";
$result = pg_query_params($conn, $query, array($id));

if (!$result) {
    echo "Sorgu çalıştırma hatası: " . pg_last_error($conn);
    exit;
}

$allReplies = pg_fetch_all($result);

// Yanıt gönderme işlemi
if (isset($_POST['submit'])) {
    if (empty($_POST['reply'])) {
        echo "<script>alert('bilgileriniz eksik');</script>";
    } else {
        $replyText = $_POST['reply'];
        $user_id = $_SESSION['user_id'];
        $user_image = 'profilePicture.jpg'; // Varsayılan profil resmi yolu
        $user_name = $_SESSION['username'];

        $query = "INSERT INTO replies (reply, user_id, user_image, topic_id, user_name) VALUES ($1, $2, $3, $4, $5)";
        $result = pg_query_params($conn, $query, array($replyText, $user_id, $user_image, $id, $user_name));

        if (!$result) {
            echo "Veritabanına ekleme hatası: " . pg_last_error($conn);
            exit;
        }

        header("Location: /FitCheck/forum/topics/topic.php?id=" . $id);
        exit();
    }
}
?>
<script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="main-col">
                <div class="block">
                    <h1 class="pull-left"><?php echo htmlspecialchars($singleTopic->title); ?></h1>
                    <h4 class="pull-right">Forum</h4>
                    <div class="clearfix"></div>
                    <hr>
                    <ul id="topics">
                        <li id="main-topic" class="topic topic">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="user-info">
                                        <img class="avatar pull-left" src="profilePicture.jpg" />
                                        <ul>
                                            <li><strong><?php echo htmlspecialchars($singleTopic->user_name); ?></strong></li>
                                            <li><?php echo htmlspecialchars($count->count_topics); ?> Gönderiler</li>
                                            <li><a href="/FitCheck/forum/user/profile.php?name=<?php echo htmlspecialchars($singleTopic->user_name); ?>">Profil</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    <div class="topic-content pull-right">
                                        <p><?php echo htmlspecialchars($singleTopic->body); ?></p>
                                    </div>
                                    <?php if (isset($_SESSION['username'])): ?>
                                        <?php if ($singleTopic->user_name == $_SESSION['username']): ?>
                                            <a class="btn btn-danger" href="delete.php?id=<?php echo htmlspecialchars($singleTopic->id); ?>" role="button">Sil</a>
                                            <a class="btn btn-warning" href="update.php?id=<?php echo htmlspecialchars($singleTopic->id); ?>" role="button">Güncelle</a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </li>
                        <?php if ($allReplies): ?>
                            <?php foreach ($allReplies as $reply): ?>
                                <li class="topic topic">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="user-info">
                                             <img class="avatar pull-left" src="profilePicture.jpg" />
                                                <ul>
                                                    <li><strong><?php echo htmlspecialchars($reply['user_name']); ?></strong></li>
                                                    <li><a href="/FitCheck/forum/user/profile.php?name=<?php echo htmlspecialchars($reply['user_name']); ?>">Profil</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-10">
                                            <div class="topic-content pull-right">
                                                <p><?php echo htmlspecialchars($reply['reply']); ?></p>
                                            </div>
                                            <?php if (isset($_SESSION['username'])): ?>
                                                <?php if ($reply['user_id'] == $_SESSION['user_id']): ?>
                                                    <a class="btn btn-danger" href="../replies/delete.php?id=<?php echo htmlspecialchars($reply['id']); ?>" role="button">Sil</a>
                                                    <a class="btn btn-warning" href="../replies/update.php?id=<?php echo htmlspecialchars($reply['id']); ?>" role="button">Güncelle</a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                    <h3>Tartışmayı Yanıtla</h3>
                    <form role="form" method="POST" action="topic.php?id=<?php echo htmlspecialchars($id); ?>">
                        <div class="form-group">
                            <textarea id="reply" rows="10" cols="80" class="form-control" name="reply"></textarea>
                            <script>
                                CKEDITOR.replace('reply');
                            </script>
                        </div>
                        <button type="submit" name="submit" class="btn btn-default">Gönder</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="/FitCheck/forum/js/bootstrap.js"></script>
<?php require "../includes/footer.php"; ?>
