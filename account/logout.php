<?php
session_start();
// Tüm oturum değişkenlerini temizle
$_SESSION = array();
// Oturumu sonlandır
session_destroy();
header("Location: ../MainPage.php");
exit();
?>
