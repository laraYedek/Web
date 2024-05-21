<?php require "../includes/header.php"; ?>
<?php require "../config/config.php"; ?>
<?php

if (!isset($_SESSION['username'])) {
    header("Location: /forum");
    exit;
}

// Veriyi çekme işlemi
if (isset($_GET['id'])) {

    $id = $_GET['id'];

    $query = "SELECT * FROM topics WHERE id = $1";
    $result = pg_query_params($conn, $query, array($id));

    if (!$result) {
        echo "Sorgu çalıştırma hatası: " . pg_last_error($conn);
        exit;
    }

    $topic = pg_fetch_object($result);

    if ($topic->user_name != $_SESSION['username']) {
        header("Location: /forum");
        exit;
    }
}

// Form gönderildiğinde güncelleme işlemi
if (isset($_POST['submit'])) {

    if (empty($_POST['title']) || empty($_POST['category']) || empty($_POST['body'])) {
        echo "<script>alert('Bilgileriniz eksik');</script>";
    } else {

        $title = $_POST['title'];
        $category = $_POST['category'];
        $body = $_POST['body'];
        $user_name = $_SESSION['username'];

        $query = "UPDATE topics SET title = $1, category = $2, body = $3, user_name = $4 WHERE id = $5";
        $result = pg_query_params($conn, $query, array($title, $category, $body, $user_name, $id));

        if (!$result) {
            echo "Güncelleme hatası: " . pg_last_error($conn);
            exit;
        }

        header("Location: /forum");
        exit;
    }
}

?>

<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="main-col">
                <div class="block">
                    <h1 class="pull-left">Tartışma Güncelle</h1>
                    <h4 class="pull-right">Forum</h4>
                    <div class="clearfix"></div>
                    <hr>
                    <form role="form" method="POST" action="update.php?id=<?php echo $id; ?>">
                        <div class="form-group">
                            <label>Konu Başlığı</label>
                            <input type="text" value="<?php echo htmlspecialchars($topic->title); ?>" class="form-control" name="title" placeholder="Konu Başlığı Girin">
                        </div>
                        <div class="form-group">
                            <label>Kategoriler</label>
                            <select name="category" class="form-control">
                                <option value="Yemekler" <?php if ($topic->category == 'Yemekler') echo 'selected'; ?>>Yemekler</option>
                                <option value="Egzersizler" <?php if ($topic->category == 'Egzersizler') echo 'selected'; ?>>Egzersizler</option>
                                <option value="Uygulama Önerisi" <?php if ($topic->category == 'Uygulama Önerisi') echo 'selected'; ?>>Uygulama Önerisi</option>
                                <option value="Uygulama Hatası" <?php if ($topic->category == 'Uygulama Hatası') echo 'selected'; ?>>Uygulama Hatası</option>
                                <option value="Danışma" <?php if ($topic->category == 'Danışma') echo 'selected'; ?>>Danışma</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Konu İçeriği</label>
                            <textarea id="body" rows="10" cols="80" class="form-control" name="body"><?php echo htmlspecialchars($topic->body); ?></textarea>
                            <script>CKEDITOR.replace('body');</script>
                        </div>
                        <button type="submit" name="submit" class="btn btn-default">Güncelle</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require "../includes/footer.php"; ?>
