<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Foruma Hoş Geldiniz</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" type="text/css" href="/FitCheck/forum/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/FitCheck/forum/css/custom.css">

</head>
<body>

<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/FitCheck/forum/index.php">Forum</a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <?php if (isset($_SESSION['user_id'])): ?>
                   <li> <a href="/FitCheck/forum/create.php">Konu Yarat</a></li>
                    <li><a href="/FitCheck/account/logout.php">Çıkış Yap</a></li>
                    <li><a href="/FitCheck/asistan.php">Asistanım</a></li>
                    <li><a href="/forum/profil.php">Profilim</a></li>
                    <li><a href="/FitCheck//MainPage.php">Anasayfa</a></li>
                <?php else: ?>
                    <li><a href="/FitCheck/account/login.html">Giriş Yap</a></li>
                    <li><a href="/FitCheck/account/register.html">Üye Ol</a></li>
                    <li><a href="/FitCheck//MainPage.php">Anasayfa</a></li>
                <?php endif; ?>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</div>
