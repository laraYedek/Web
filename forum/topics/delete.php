<?php require "../includes/header.php"; ?>
<?php require "../config/config.php"; ?>

<?php

if (isset($_GET['id'])) {

    $id = $_GET['id'];

    // Veritabanı sorgusu
    $query = "SELECT * FROM topics WHERE id = $1";
    $result = pg_query_params($conn, $query, array($id));

    if (!$result) {
        echo "Sorgu çalıştırma hatası: " . pg_last_error($conn);
        exit;
    }

    $topic = pg_fetch_object($result);

    // Kullanıcı kontrolü
    if ($topic->user_name != $_SESSION['username']) {
        header("Location: /forum");
        exit;
    } else {
        // Silme işlemi
        $delete_query = "DELETE FROM topics WHERE id = $1";
        $delete_result = pg_query_params($conn, $delete_query, array($id));

        if (!$delete_result) {
            echo "Silme işlemi hatası: " . pg_last_error($conn);
            exit;
        }

        header("Location: /forum");
        exit;
    }
}

?>
