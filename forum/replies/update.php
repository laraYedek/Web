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

    $query = "SELECT * FROM replies WHERE id = $1";
    $result = pg_query_params($conn, $query, array($id));

    if (!$result) {
        echo "Sorgu çalıştırma hatası: " . pg_last_error($conn);
        exit;
    }

    $reply = pg_fetch_object($result);

    if ($reply->user_id != $_SESSION['user_id']) {
        header("Location: /forum");
        exit;
    }
}

// Form gönderildiğinde güncelleme işlemi
if (isset($_POST['submit'])) {

    if (empty($_POST['reply'])) {
        echo "<script>alert('bilgileriniz eksik');</script>";
    } else {

        $replyText = $_POST['reply'];

        $update_query = "UPDATE replies SET reply = $1 WHERE id = $2";
        $result = pg_query_params($conn, $update_query, array($replyText, $id));

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
                    <h1 class="pull-left">Tartışma yarat</h1>
                    <h4 class="pull-right">Forum</h4>
                    <div class="clearfix"></div>
                    <hr>
                    <form role="form" method="POST" action="update.php?id=<?php echo $id; ?>">
                        <div class="form-group">
                            <label>Yanıtla</label>
                            <input type="text" value="<?php echo htmlspecialchars($reply->reply); ?>" class="form-control" name="reply" placeholder="Enter reply">
                        </div>
                        <button type="submit" name="submit" class="btn btn-default">Güncelle</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require "../includes/footer.php"; ?>
