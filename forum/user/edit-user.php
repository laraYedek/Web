<?php require "../includes/header.php"; ?>
<?php require "../config/config.php"; ?>

<?php
if (!isset($_SESSION['username'])) {
    header("Location: /FitCheck/forum/index.php");
    exit;
}

// Veriyi çekme işlemi
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "SELECT * FROM users WHERE id = $1";
    $result = pg_query_params($conn, $query, array($id));

    if ($result && pg_num_rows($result) > 0) {
        $user = pg_fetch_object($result);

        if ($user->id != $_SESSION['user_id']) {
            header("Location: /FitCheck/forum/index.php");
            exit;
        }
    } else {
        header("Location: /FitCheck/forum/index.php");
        exit;
    }
}

if (isset($_POST['submit'])) {
    if (empty($_POST['email']) || empty($_POST['about'])) {
        echo "<script>alert('Bilgileriniz eksik');</script>";
    } else {
        $email = $_POST['email'];
        $about = $_POST['about'];

        $update = "UPDATE users SET email = $1, about = $2 WHERE id = $3";
        $result = pg_query_params($conn, $update, array($email, $about, $id));

        if ($result) {
            header("Location: /FitCheck/forum/index.php");
            exit;
        } else {
            echo "Güncelleme hatası: " . pg_last_error($conn);
        }
    }
}
?>

<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="main-col">
                <div class="block">
                    <h1 class="pull-left">Profilini Düzenle</h1>
                    <h4 class="pull-right">Forum</h4>
                    <div class="clearfix"></div>
                    <hr>
                    <form role="form" method="POST" action="edit-user.php?id=<?php echo htmlspecialchars($id); ?>">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" value="<?php echo htmlspecialchars($user->email); ?>" class="form-control" name="email" placeholder="Email giriniz">
                        </div>
                        <div class="form-group">
                            <label>Hakkında</label>
                            <textarea id="body" rows="10" cols="80" class="form-control" name="about"><?php echo htmlspecialchars($user->about); ?></textarea>
                            <script>CKEDITOR.replace('body');</script>
                        </div>
                        <button type="submit" name="submit" class="color btn btn-default">Güncelle</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require "../includes/footer.php"; ?>
