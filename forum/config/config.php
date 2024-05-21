<?php


try {
    $host = "localhost";
    $port = "5432";
    $dbname = "FitCheck";
    $user = "postgres";
    $password = "123123";    

    $conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");
    $userId = $_SESSION['user_id'] ?? null;
    if (!$conn) {
        die("Veritabanı bağlantısı başarısız: " . pg_last_error());
    }
   
}  catch( PDOException $Exception ) {
    echo $Exception->getMessage();
} 