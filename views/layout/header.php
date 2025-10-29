<?php
$base_url = '/fyp_cherating';
$isAdmin = isset($_SESSION['admin_id']); // check if admin logged in
$isLoggedIn = isset($_SESSION['role']); // check if user logged in

// Get the current path
$current_page = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

//  code for php 8.0
// $base_path = trim($base_url, '/');
// if (str_starts_with($current_page, $base_path)) {
//     $current_page = trim(substr($current_page, strlen($base_path)), '/');
// }
// code for php 7.0
$base_path = trim($base_url, '/');
if (strpos($current_page, $base_path) === 0) {
    $current_page = trim(substr($current_page, strlen($base_path)), '/');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1">
    <title>Cherating - GuestHouse</title>
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="author" content="Amelia Natasya">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/responsive.css">
    <link rel="icon" href="<?= $base_url ?>/assets/images/fevicon.png" type="image/gif" />
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/jquery.mCustomScrollbar.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css" media="screen">
</head>
<body class="main-layout">
    <div class="loader_bg">
        <div class="loader"><img src="<?= $base_url ?>/assets/images/loading.gif" alt="#"/></div>
    </div>
    <?php if (!$isAdmin): ?>
    <header>
        <div class="header">
            <div class="container">
                <div class="row">
                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col logo_section">
                        <div class="full">
                            <div class="center-desk">
                                <div class="logo">
                                    <?php if (!$isLoggedIn): ?>
                                        <a href="<?= $base_url ?>/"><img src="<?= $base_url ?>/assets/images/logo.png" alt="#" /></a>
                                    <?php else: ?>
                                        <a href=""><img src="<?= $base_url ?>/assets/images/logo.png" alt="#" /></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                        <nav class="navigation navbar navbar-expand-md navbar-dark">
                            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample04" aria-controls="navbarsExample04" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <div class="collapse navbar-collapse" id="navbarsExample04">
                                <ul class="navbar-nav mr-auto">
                                    <?php if ($isLoggedIn): ?>
                                        <li class="nav-item active">
                                            <a class="nav-link" href="<?= APP_URL ?>/dashboard">Dashboard</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="<?= APP_URL ?>/profile">My Profile</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= $base_url ?>/auth/logout" class="btn btn-danger">Logout</a>
                                        </li>
                                    <?php else: ?>
                                        <li class="nav-item">
                                            <a class="nav-link <?= $current_page == '' ? 'active' : '' ?>" href="<?= $base_url ?>/">Home</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link <?= $current_page == 'about' ? 'active' : '' ?>" href="<?= $base_url ?>/about">About</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link <?= $current_page == 'rooms' ? 'active' : '' ?>" href="<?= $base_url ?>/rooms">Our Rooms</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link <?= $current_page == 'contact' ? 'active' : '' ?>" href="<?= $base_url ?>/contact">Contact Us</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= $base_url ?>/auth/login" class="btn btn-danger">Signup/Login</a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <?php endif; ?>
    <!-- Flash messages (global) -->
    <div class="container mt-2">
        <?php Flash::display(); ?>
    </div>
