<?php require "../includes/header.php"; ?>
<?php require "../config/config.php"; ?>

<?php

if(isset($_GET['id'])) {

    $id = $_GET['id'];
    
    // Veritabanı sorgusu
    $query = "SELECT * FROM replies WHERE id = '$id'";
    $result = pg_query($conn, $query);

    if (!$result) {
        echo "Sorgu çalıştırma hatası: " . pg_last_error($conn);
        exit;
    }

    $reply = pg_fetch_object($result);

    // Kullanıcı kontrolü
    if($reply->user_id != $_SESSION['user_id']) {
        header("Location: /forum");
        exit;
    } else {
        // Silme işlemi
        $delete_query = "DELETE FROM replies WHERE id = '$id'";
        $delete_result = pg_query($conn, $delete_query);

        if (!$delete_result) {
            echo "Silme işlemi hatası: " . pg_last_error($conn);
            exit;
        }

        header("Location: /forum");
        exit;
    }

}

?>
