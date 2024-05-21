<?php require "includes/header.php"; ?>
<?php require "config/config.php"; ?>

<?php
if (isset($_POST['submit'])) {
    if (empty($_POST['title']) || empty($_POST['category']) || empty($_POST['body'])) {
        echo "<script>alert('Bilgileriniz eksik.');</script>";
    } else {
        $title = $_POST['title'];
        $category = (int)$_POST['category'];
        $body = $_POST['body'];
        $user_id = $_SESSION['user_id'];

        $userQuery = "SELECT kullanici_adi, avatar FROM kullanicilar WHERE id = $user_id";
        $userResult = pg_query($conn, $userQuery);
        if ($userResult && pg_num_rows($userResult) > 0) {
            $userData = pg_fetch_assoc($userResult);
            $user_name = $userData['kullanici_adi'];
            $user_image = $userData['avatar'] ? $userData['avatar'] : '/FitCheck/assets/images/profilePicture.jpg'; // Varsayılan resim yolu

            $insert_query = "INSERT INTO topics (title, category, body, user_name, user_image) VALUES ($1, $2, $3, $4, $5)";
            $result = pg_query_params($conn, $insert_query, array($title, $category, $body, $user_name, $user_image));

            if ($result) {
                header("Location: index.php");
                exit;
            } else {
                echo "Veritabanına ekleme hatası: " . pg_last_error($conn);
            }
        } else {
            echo "<script>alert('Kullanıcı bilgileri alınamadı.');</script>";
        }
    }
}
?>

<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="main-col">
                <div class="block">
                    <h1 class="pull-left">Tartışma Yarat</h1>
                    <h4 class="pull-right">Forum</h4>
                    <div class="clearfix"></div>
                    <hr>
                    <form role="form" method="POST" action="create.php">
                        <div class="form-group">
                            <label>Konu Başlığı</label>
                            <input type="text" class="form-control" name="title" placeholder="Konu Başlığı Girin">
                        </div>
                        <div class="form-group">
                            <label>Kategoriler</label>
                            <select name="category" class="form-control">
                                <?php
                                $categoryQuery = "SELECT id, name FROM categories";
                                $categoryResult = pg_query($conn, $categoryQuery);
                                while ($category = pg_fetch_assoc($categoryResult)) {
                                    echo '<option value="' . $category['id'] . '">' . htmlspecialchars($category['name']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Açıklama</label>
                            <textarea class="form-control" name="body"></textarea>
                        </div>
                        
                        <button type="submit" name="submit" class="btn btn-default">Oluştur</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <?php require "includes/leftSide.php"; ?>
        </div>
    </div>
</div>

<?php require "includes/footer.php"; ?>
