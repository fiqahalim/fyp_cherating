<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// $base_url = '/fyp_cherating';
$base_url = "http://localhost:8000/FYP/fyp_cherating"; //for macbook
$isLoggedIn = !empty($_SESSION['is_logged_in']);
$isAdmin = $isLoggedIn && ($_SESSION['auth_type'] ?? '') === 'admin';
$isCustomer = $isLoggedIn && ($_SESSION['auth_type'] ?? '') === 'customer';

// Detect current page
$current_page = basename($_SERVER['PHP_SELF']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Amelia Natasya">
    <title>Admin | Cherating Guest House</title>
    <!-- Admin Specific Styles -->
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/sb-admin-2.css">
    <link href="<?= $base_url ?>/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
    <link
    href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <script>
        let lastNotificationCount = 0;

        function checkNotifications() {
            fetch('<?= $base_url ?>/admin/getGlobalNotifications')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('notification-items-container');
                    const counter = document.getElementById('global-nav-count');
                    const sound = document.getElementById('notification-sound');

                    // 1. Play sound if count increased
                    if (data.total > lastNotificationCount) {
                        sound.play().catch(e => console.log("Sound blocked by browser until user interacts with page."));
                    }
                    lastNotificationCount = data.total;

                    // 2. Update Badge
                    counter.innerText = data.total > 0 ? data.total : '';

                    // 3. Build HTML
                    let html = '';

                    // Add Pending QR
                    data.pending_qr.forEach(item => {
                        html += `
                        <a class="dropdown-item d-flex align-items-center" href="<?= $base_url ?>/admin/bookings/view_payment">
                            <div class="mr-3">
                                <div class="icon-circle bg-warning"><i class="fas fa-qrcode text-white"></i></div>
                            </div>
                            <div>
                                <div class="small text-gray-500">QR Payment Pending</div>
                                <span class="font-weight-bold">Verify receipt for Ref: ${item.booking_ref_no}</span>
                            </div>
                        </a>`;
                    });

                    // Add New Bookings
                    data.new_bookings.forEach(item => {
                        html += `
                        <a class="dropdown-item d-flex align-items-center" href="<?= $base_url ?>/admin/bookings">
                            <div class="mr-3">
                                <div class="icon-circle bg-primary"><i class="fas fa-calendar-plus text-white"></i></div>
                            </div>
                            <div>
                                <div class="small text-gray-500">New Booking</div>
                                New reservation made: ${item.booking_ref_no}
                            </div>
                        </a>`;
                    });

                    // Add Cancellations
                    data.cancellations.forEach(item => {
                        html += `
                        <a class="dropdown-item d-flex align-items-center" href="<?= $base_url ?>/admin/bookings">
                            <div class="mr-3">
                                <div class="icon-circle bg-danger"><i class="fas fa-user-times text-white"></i></div>
                            </div>
                            <div>
                                <div class="small text-gray-500">Cancellation</div>
                                ${item.booking_ref_no} was cancelled (>5 days notice).
                            </div>
                        </a>`;
                    });

                    if(data.total === 0) {
                        html = '<a class="dropdown-item text-center small text-gray-500" href="#">No new alerts</a>';
                    }

                    container.innerHTML = html;
                });
        }

        // Check every 15 seconds
        setInterval(checkNotifications, 15000);
        document.addEventListener('DOMContentLoaded', checkNotifications);
    </script>
</head>
<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#" onclick="event.preventDefault();">
                <div class="sidebar-brand-icon rotate-n-15">
                    <!-- <i class="fas fa-laugh-wink"></i> -->
                    <img src="<?= $base_url ?>/assets/images/Cherating_Indah_Logo.png" alt="Cherating Guest House Logo" />
                </div>
                <!-- <div class="sidebar-brand-text mx-3">Cherating Guest House</div> -->
            </a>
            <!-- Divider -->
            <hr class="sidebar-divider my-0">
            <!-- Nav Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="<?= $base_url ?>/dashboard">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>DASHBOARD</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            <!-- Nav Menu -->
            <li class="nav-item">
                <a class="nav-link" href="<?= $base_url ?>/admin/bookings">
                    <i class="fas fa-book"></i><span>BOOKINGS</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= $base_url ?>/admin/payments">
                    <i class="fas fa-money"></i><span>PAYMENTS</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= $base_url ?>/admin/rooms">
                    <i class="fas fa-hotel"></i><span>ROOMS</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= $base_url ?>/admin/room-tours">
                    <i class="fas fa-magnifying-glass"></i><span>360° VIRTUAL TOUR</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= $base_url ?>/admin/messages">
                    <i class="fas fa-comment"></i><span>MESSAGES</span>
                </a>
            </li>
            <hr class="sidebar-divider d-none d-md-block">
            <li class="nav-item">
                <a class="nav-link" href="<?= $base_url ?>/auth/logout">
                    <i class="fas fa-sign-out-alt"></i>
                        Logout
                </a>
            </li>
            <hr class="sidebar-divider d-none d-md-block">
            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <span class="badge badge-danger badge-counter" id="global-nav-count">0</span>
                            </a>
                            
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">Alerts Center</h6>
                                
                                <div id="notification-items-container">
                                    <a class="dropdown-item text-center small text-gray-500" href="#">No new alerts</a>
                                </div>
                                
                                <a class="dropdown-item text-center small text-gray-500" href="<?= $base_url ?>/admin/bookings">View All Bookings</a>
                            </div>
                        </li>

                        <audio id="notification-sound" preload="auto">
                            <source src="<?= $base_url ?>/assets/audio/ding.mp3" type="audio/mpeg">
                        </audio>

                        <!-- Nav Item - Messages -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-envelope fa-fw"></i>
                                <!-- Counter - Messages -->
                                <span class="badge badge-danger badge-counter">7</span>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="messagesDropdown">
                                <h6 class="dropdown-header">
                                    Message Center
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="<?= $base_url ?>/assets/images/undraw_profile_1.svg"
                                            alt="...">
                                        <div class="status-indicator bg-success"></div>
                                    </div>
                                    <div class="font-weight-bold">
                                        <div class="text-truncate">Hi there! I am wondering if you can help me with a
                                            problem I've been having.</div>
                                        <div class="small text-gray-500">Emily Fowler · 58m</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="<?= $base_url ?>/assets/images/undraw_profile_2.svg"
                                            alt="...">
                                        <div class="status-indicator"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">I have the photos that you ordered last month, how
                                            would you like them sent to you?</div>
                                        <div class="small text-gray-500">Jae Chun · 1d</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="<?= $base_url ?>/assets/images/undraw_profile_3.svg"
                                            alt="...">
                                        <div class="status-indicator bg-warning"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">Last month's report looks great, I am very happy with
                                            the progress so far, keep up the good work!</div>
                                        <div class="small text-gray-500">Morgan Alvarez · 2d</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="https://source.unsplash.com/Mv9hjnEUHR4/60x60"
                                            alt="...">
                                        <div class="status-indicator bg-success"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">Am I a good boy? The reason I ask is because someone
                                            told me that people say this to all dogs, even if they aren't good...</div>
                                        <div class="small text-gray-500">Chicken the Dog · 2w</div>
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>
                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?= $_SESSION['full_name'] ?? 'Admin' ?>
                                </span>
                                <img class="img-profile rounded-circle"
                                    src="<?= $base_url ?>/assets/images/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="<?= $base_url ?>/profile">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal" onclick="event.preventDefault();">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>

